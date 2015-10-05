<?php

namespace AppBundle\Services\Platform\Spot;

use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferClick;
use AppBundle\Entity\User;
use AppBundle\Services\Platform\Exception\InvalidPixelException;
use AppBundle\Services\Platform\Exception\InvalidSettingException;
use AppBundle\Services\Platform\PlatformAbstract;
use AppBundle\Services\Platform\RecordAffiliateIdentity;
use AppBundle\Services\Platform\SettingAbstract;
use AppBundle\Services\Platform\Spot\Communication\BatchRequest;
use AppBundle\Services\Platform\Spot\Communication\BatchResponse;
use AppBundle\Services\Platform\Spot\Communication\Request;
use AppBundle\Services\Platform\Spot\Communication\RequestException;
use AppBundle\Services\Platform\Spot\Communication\Response;
use AppBundle\Services\Platform\Spot\Communication\ResponseException;
use AppBundle\Services\Platform\Spot\Communication\ResponseParser;
use AppBundle\Services\Platform\Spot\Setting;
use Buzz\Client\Curl;
use FOS\UserBundle\Model\UserManager;

class Spot extends PlatformAbstract {

	/** @var BatchRequest */
	private $batchRequest;

	private $batchMode = true;

	/** @var Setting */
	protected $setting;

	public function __construct()
	{
		$this->batchRequest = new BatchRequest();
	}

	public function setSetting(SettingAbstract $setting)
	{
		if(!($setting instanceof Setting)) {
			throw new InvalidSettingException(sprintf('Wrong setting [%s] given for brand %d',
				get_class($setting),
				$this->brand->getId()));
		}

		return parent::setSetting($setting);
	}
	/**
	 * Enable/Disable batch mode
	 * @param bool|true $batchMode
	 */
	public function setBatchMode($batchMode=true)
	{
		$this->batchMode = $batchMode;
	}

	/**
	 * Get batch mode status
	 * @return bool
	 */
	public function getBatchMode()
	{
		return $this->batchMode;
	}

	/**
	 * @inheritdoc
	 */
	public function handleClick(OfferClick $offerClick)
	{
		$pixelParameter = array(
			'aid' => $offerClick->getUser()->getId(),
			'campaign' => $this->setting->getCampaignId(),
			'subCampaign' => sprintf('%s_%s_%s',
				$offerClick->getUser()->getId(), $offerClick->getOffer()->getId()), $offerClick->getId()
		);
		$pixelParameter['btag'] = $pixelParameter['subCampaign'];

		$destination = $offerClick->getOffer()->getDestination();
		return $this->appendParametersToURL($destination, $pixelParameter);
	}

	/**
	 * @inheritdoc
	 */
	public function getRecordByPixel($id, $pixelType)
	{
		$data = false;
		switch($pixelType) {
			case PlatformAbstract::PIXEL_TYPE_LEAD:
				$req = new Request('Lead', 'view', array('FILTER'=>array('id'=>$id)));
				$data = $this->executeRequest($req);
				break;
			case PlatformAbstract::PIXEL_TYPE_CUSTOMER:
				$req = new Request('Customer', 'view', array('FILTER'=>array('id'=>$id)));
				$data = $this->executeRequest($req);
				break;
			case PlatformAbstract::PIXEL_TYPE_DEPOSIT:
				$req = new Request('CustomerDeposits', 'view', array('FILTER'=>array('customerId'=>$id)));
				$deposits = $this->executeRequest($req);
				if(is_array($deposits) && isset($deposits[0])) {
					$customerId = $deposits[0]['customerId'];
					$req = new Request('Customer', 'view', array('FILTER'=>array('id'=>$customerId)));
					$data = $this->executeRequest($req);
				}
				break;
			case PlatformAbstract::PIXEL_TYPE_GAME:
				throw new InvalidPixelException(sprintf('Pixel [%s] not supported for brand [%d]',
					PlatformAbstract::PIXEL_TYPE_GAME, $this->brand->getId()));
				break;
		}

		return (is_array($data) && isset($data[0])) ? $data[0] : false;
	}

	/**
	 * @inheritdoc
	 */
	protected function getAffiliateIdentity(array $record)
	{
		if($this->setting->getCampaignId() != $record['campaignId']) {
			//Other affiliate's campaign
			throw new InvalidPixelException(sprintf('Invalid campaign [%d], only campaign [%d] is supported for record [%d]',
				$record['campaignId'], $this->setting->getCampaignId(), $record['id']));
		}

		$subCampaignParam = $record['subCampaignParam'];
		$subCampaignParam = explode('_', $subCampaignParam);
		if(count($subCampaignParam) != 3) {
			//Not our pixel
			throw new InvalidPixelException(sprintf('Invalid pixel [%s] for record [%d]',
				$record['subCampaignParam'], $record['id']));
		}

		/** @var User $user */
		$user = $this->userManager->findUserBy(array('id'=>$subCampaignParam[0]));
		/** @var Offer $offer */
		$offer = $this->getOffer($subCampaignParam[1]);
		/** @var OfferClick $click */
		$offerClick = $this->getOfferClick($subCampaignParam[2]);

		if($offerClick->getUser()->getId() != $user->getId() ||
			$offerClick->getOffer()->getId() != $offer->getId()) {
			//Data is wrong
			throw new InvalidPixelException(sprintf('Pixel [%s] contain invalid links for record [%d]',
				$record['subCampaignParam'], $record['id']));
		}

		$totalDepositsAmount = 0;
		$totalPositionsCount = 0;

		$this->setBatchMode(true);
		$type = BrandRecord::USER_TYPE_LEAD;
		if(isSet($record['firstDepositDate'])) {
			$type = BrandRecord::USER_TYPE_CUSTOMER;
			if('0000-00-00'!=$record['firstDepositDate'] && ''!=$record['firstDepositDate']) {
				$type = BrandRecord::USER_TYPE_DEPOSITOR;

				$req = new Request('CustomerDeposits', 'view', array('FILTER'=>array('customerId'=>$record['id'], 'type'=>'deposit', 'status'=>'approved')));
				$this->executeRequest($req);
				$req = new Request('Withdrawal', 'view', array('FILTER'=>array('customerId'=>$record['id'], 'status'=>'approved')));
				$this->executeRequest($req);

				if('' != $record['pnl'] && 0 < intval($record['pnl'])) {
					$type = BrandRecord::USER_TYPE_GAMER;
					$req = new Request('Positions', 'view', array('FILTER'=>array('customerId'=>$record['id'])));
					$this->executeRequest($req);
					$req = new Request('OneTouch', 'view', array('FILTER'=>array('customerId'=>$record['id'])));
					$this->executeRequest($req);
				}

				$responses = $this->executeBatchRequest();
				//Handle CustomerDeposits
				foreach($responses[0] AS $deposit) {
					$totalDepositsAmount += $deposit['amountUSD'];
				}
				//Handle Withdrawal
				foreach($responses[1] AS $withdrawal) {
					$totalDepositsAmount -= $withdrawal['amountUSD'];
				}

				if(isSet($responses[2])) {
					$totalPositionsCount += count($responses[2]);
					$totalPositionsCount += count($responses[3]);
				}
			}
		}

		return new RecordAffiliateIdentity(
			$record['id'], $type, $totalDepositsAmount, $totalPositionsCount,
				$user, $offer, $offerClick->getOfferBanner(), $offerClick
		);
	}

	/**
	 * Execute all pending requests in batch
	 * @return BatchResponse
	 * @throws RequestException
	 * @throws ResponseException
	 */
	public function executeBatchRequest()
	{
		if(!$this->batchMode) {
			throw new RequestException('Batch mode is off');
		}

		if(!$this->batchRequest) {
			return new BatchResponse();
		}

		$parameters = array();
		/** @var Request $req */
		foreach($this->batchRequest AS $req) {
			$parameters['BATCH'][] = $req->getParameters();
		}

		$params['api_username'] = $this->setting->getUser();
		$params['api_password'] = $this->setting->getPassword();
		$params['jsonResponse'] = 'true';

		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => count($params),
			CURLOPT_POSTFIELDS => http_build_query($params)
		);

		$request = new \Buzz\Message\Request('POST',
			parse_url($this->setting->getUrl(), PHP_URL_PATH),
			parse_url($this->setting->getUrl(), PHP_URL_HOST));
		$response = new \Buzz\Message\Response();

		$curl = new Curl();
		$curl->send($request, $response, $options);

		$batchResponse = ResponseParser::parseBatchResponse($this->batchRequest, $response->getContent());
		$this->batchRequest->reset();
		return $batchResponse;
	}

	/**
	 * Execute an API request
	 * 	in case batch-mode is on, it will be added to queue
	 * @param Request $req
	 * @return Response
	 * @throws ResponseException
	 */
	private function executeRequest(Request $req) {

		$req = $this->normalizeFilters($req);
		if($this->getBatchMode()) {
			$this->batchRequest->add($req);
			return true;
		}

		$params = $req->getParameters();
		$params['MODULE'] = $req->getModel();
		$params['COMMAND'] = $req->getCommand();
		$params['api_username'] = $this->setting->getUser();
		$params['api_password'] = $this->setting->getPassword();
		$params['jsonResponse'] = 'true';

		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => count($params),
			CURLOPT_POSTFIELDS => http_build_query($params)
		);

		$request = new \Buzz\Message\Request('POST',
			parse_url($this->setting->getUrl(), PHP_URL_PATH),
			parse_url($this->setting->getUrl(), PHP_URL_HOST));
		$response = new \Buzz\Message\Response();

		$curl = new Curl();
		$curl->send($request, $response, $options);

		return ResponseParser::parseResponse($req, $response->getContent());
	}

	/**
	 * @param Request $req
	 * @return Request
	 */
	private function normalizeFilters(Request $req)
	{
		$parameters = $req->getParameters();
		if(Request::COMMAND_VIEW == $req->getCommand() && !isset($parameters['FILTER']['id'])) {
			$parameters['FILTER']['id']['min'] = 0;
		}
		return $req->setParameters($parameters);
	}
}