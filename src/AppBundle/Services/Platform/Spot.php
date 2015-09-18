<?php

namespace AppBundle\Services\Platform;

use AppBundle\Entity\OfferClick;
use AppBundle\Services\Platform\Exception\InvalidSettingException;
use AppBundle\Services\Platform\Setting\SettingAbstract;
use AppBundle\Services\Platform\Setting\SpotSetting;
use AppBundle\Services\Platform\Spot\BatchRequest;
use AppBundle\Services\Platform\Spot\BatchResponse;
use AppBundle\Services\Platform\Spot\Request;
use AppBundle\Services\Platform\Spot\RequestException;
use AppBundle\Services\Platform\Spot\Response;
use AppBundle\Services\Platform\Spot\ResponseException;
use AppBundle\Services\Platform\Spot\ResponseParser;

class Spot extends PlatformAbstract {

	/** @var BatchRequest */
	private $batchRequest;

	private $batchMode = true;

	/** @var SpotSetting */
	protected $setting;

	public function __construct()
	{
		$this->batchRequest = new BatchRequest();
	}

	public function setSetting(SettingAbstract $setting)
	{
		if(!($setting instanceof SpotSetting)) {
			throw new InvalidSettingException(sprintf('Wrong setting [%s] given instead of [%s] for brand %d',
				get_class($setting),
				'SpotSetting',
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
				$offerClick->getId(), $offerClick->getUser()->getId(), $offerClick->getOffer()->getId())
		);
		$pixelParameter['btag'] = $pixelParameter['subCampaign'];

		$destination = $offerClick->getOffer()->getDestination();
		return $this->appendParametersToURL($destination, $pixelParameter);
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

		//TODO: call SPOT API + add credentials
		$response = '';

		$batchResponse = ResponseParser::parseBatchResponse($this->batchRequest, $response);
		$this->batchRequest->reset();
		return $batchResponse;
	}

	/**
	 * Execute an API request
	 * 	in case batch-mode is on, it will be added to queue
	 * @param Request $req
	 * @return response|bool
	 * @throws ResponseException
	 */
	private function executeRequest(Request $req) {

		$req = $this->normalizeFilters($req);
		if($this->getBatchMode()) {
			$this->batchRequest->add($req);
			return true;
		}

		//TODO: call SPOT API + add credentials
		$response = '';

		return ResponseParser::parseResponse($req, $response);
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