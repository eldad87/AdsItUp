<?php

namespace AppBundle\Services\Platform;

use AppBundle\Entity\Brand;
use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\CommissionPlan;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\OfferClick;
use AppBundle\Entity\PixelLog;
use AppBundle\Entity\User;
use AppBundle\Services\Platform\CommissionPlan\CriteriaTypeAbstract;
use AppBundle\Services\Platform\Exception\InvalidPixelException;
use AppBundle\Services\Platform\Exception\InvalidSettingException;
use AppBundle\Services\Platform\Pixel\PixelSetting;
use AppBundle\Services\Platform\Pixel\PixelSettingTypeAbstract;
use AppBundle\Services\Platform\SettingAbstract;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\RecursiveValidator;

abstract class PlatformAbstract {

    /** @var AbstractManagerRegistry */
    protected $doctrine;
    /** @var RecursiveValidator */
    protected $validator;
    /** @var SettingAbstract */
    protected $setting;
    /** @var Brand */
    protected $brand;
    /** @var UserManager */
    protected $userManager;


    public function setUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function setDoctrine(AbstractManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setValidator(RecursiveValidator $validator)
    {
        $this->validator = $validator;
    }

    public function setBrand(Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * @param SettingAbstract $setting
     * @return $this
     * @throws InvalidSettingException
     */
    public function setSetting(SettingAbstract $setting)
    {
        $errors = $this->validator->validate($setting);
        if (count($errors) > 0) {
            throw new InvalidSettingException(sprintf('Invalid setting given for brand %d: %s',
               $this->brand->getId(), (string) $errors));
        }
        $this->setting = $setting;
        return $this;
    }

	/**
	 * @return PixelSettingTypeAbstract
	 */
	public function getPixelType()
	{
		$criteriaType = sprintf('AppBundle\Services\Platform\%s\Pixel\PixelSettingType',
			$this->brand->getPlatform()->getName());
		return new $criteriaType();
	}

	/**
	 * @return CriteriaTypeAbstract
	 */
	public function getCommissionPlanCriteriaType()
	{
		$criteriaType = sprintf('AppBundle\Services\Platform\%s\CommissionPlan\CriteriaType',
			$this->brand->getPlatform()->getName());
		return new $criteriaType();
	}

    /**
     * Apply affiliate parameters
     *  to offer.destination and return it.
     *
     * @param OfferClick $offerClick
     * @return string URL
     */
    abstract public function handleClick(OfferClick $offerClick);

    /**
     * Identify affiliate/assets involved with this record
     * @param array $record
     * @return RecordAffiliateIdentity
     * @throws InvalidPixelException
     */
    abstract protected function getAffiliateIdentity(array $record);

    /**
     * Return a record (Lead or Customer) from API by id and pixel type (PlatformAbstract::PIXEL_TYPE_*)
     *  return false if no such record exists
     * @param $id
     * @param $event - PixelSetting::EVENT_*
     * @return array|false
     * @throws InvalidPixelException
     */
    abstract public function getRecordByPixel($id, $event);

    /**
     * Fetch record from DB
     * @param array $record
     * @return BrandRecord|null
     */
    public function getBrandRecord(array $record)
    {
        return $this->doctrine->getManager()->getRepository('AppBundle\Entity\BrandRecord')
            ->findOneBy(array('brand'=>$this->brand, 'externalId'=>$record['id']));
    }

    /**
     * Get brand record
     * @param array $record
     * @return BrandRecord
     * @throws InvalidPixelException
     */
    public function updatedBrandRecord(array $record)
    {
        //Get most updated record from API
        $recordAffiliateIdentity = $this->getAffiliateIdentity($record);
        $type = $recordAffiliateIdentity->getType();

        //Get our local DB record
        /** @var BrandRecord $brandRecord */
        $brandRecord = $this->getBrandRecord($record);
        if(!$brandRecord) {
            //No local record found, init a new one
            $brandRecord = new BrandRecord();
            $brandRecord->setIsCommissionGranted(false);
            $brandRecord->setBrand($brandRecord->getOffer()->getBrand());
            $brandRecord->setUser($recordAffiliateIdentity->getUser());
            $brandRecord->setReferrer($recordAffiliateIdentity->getUser()->getReferrer());
            $brandRecord->setOffer($recordAffiliateIdentity->getOffer());
            $brandRecord->setOfferBanner($recordAffiliateIdentity->getOfferBanner());
            $brandRecord->setOfferClick($recordAffiliateIdentity->getOfferClick());
        }

        //Update brand record
        $brandRecord->setType($type);
        $brandRecord->setRecord($record);
        $brandRecord->setTotalDepositsAmount($recordAffiliateIdentity->getTotalDepositsAmount());
        $brandRecord->setTotalGamesCount($recordAffiliateIdentity->getTotalGamesCount());

        $this->doctrine->getManager()->persist($brandRecord);
        $this->doctrine->getManager()->flush();
        return $brandRecord;
    }

    /**
     * Handle a situation in which incoming pixel cannot be mapped to any of the brand's records
     * @param $origin - PixelSetting::ORIGIN_TYPE_*
     * @return Response
     */
    abstract public function getPixelResponseNoEventGivenAndNoneIdentified($origin);

    /**
     * Handle a situation in which incoming pixel cannot be mapped to any of the brand's records
     * @param $origin - PixelSetting::ORIGIN_TYPE_*
     * @return Response
     */
    abstract public function getPixelResponseBrandRecordNotFound($origin);

    /**
     * Handle a situation in which incoming commission is already st
     *  therefore, another pixel should be fired
     * @param $origin - PixelSetting::ORIGIN_TYPE_*
     * @return Response
     */
    abstract public function getPixelResponseBrandRecordAlreadyCommissionQualified($origin);

    /**
     * Handle a situation in which incoming pixel match a BrandRecord but its not yet qualified for commission
     *  therefore, pixel cannot fire.
     * @param $origin - PixelSetting::ORIGIN_TYPE_*
     * @return Response
     */
    abstract public function getPixelResponseBrandRecordNoCommissionPlanMatch($origin);

    /**
     * Handle a situation in which affiliate is not set with a relevant pixel.
     * @param $origin - PixelSetting::ORIGIN_TYPE_*
     * @return Response
     */
    abstract public function getPixelResponseAffiliatePixelNotDefined($origin);

    /**
     * Handle a situation in which pixel is fired or saved for offline (cron) processing
     * @param $origin - PixelSetting::ORIGIN_TYPE_*
     * @return Response
     */
    abstract public function getPixelResponseSuccess($origin);

    /**
     * Handle a situation in which affiliate pixel is defined as Client-side pixel
     *  while incoming pixel is Server-side pixel.
     * @param $origin - PixelSetting::ORIGIN_TYPE_*
     * @return Response
     */
    abstract public function getPixelResponseServerClientConflict($origin);

    /**
     * @param $origin PixelSetting::ORIGIN_*
     * @param array $record
     * @param $event PixelSetting::EVENT_*
     * @return Response
     */
    public function handlePixelAction($origin, array $record, $event=null)
    {
        $em = $this->doctrine->getManager();

        //Identify event
        if(!$event) {
            //Get current stored record
            /** @var BrandRecord $brandRecord */
            $brandRecord = $this->getBrandRecord($record);

            //Get updated record
            /** @var BrandRecord $brandRecord */
            $updatedBrandRecord = $this->updatedBrandRecord($record);

            //Check if there is an event
            if(!$brandRecord || $brandRecord->getType() < $updatedBrandRecord->getType()) {
                switch($updatedBrandRecord->getType()) {
                    case BrandRecord::USER_TYPE_LEAD:
                        $event  = PixelSetting::EVENT_LEAD;
                        break;
                    case BrandRecord::USER_TYPE_CUSTOMER:
                        $event  = PixelSetting::EVENT_CUSTOMER;
                        break;
                    case BrandRecord::USER_TYPE_DEPOSITOR:
                        $event  = PixelSetting::EVENT_DEPOSIT;
                        break;
                    case BrandRecord::USER_TYPE_GAMER:
                        $event  = PixelSetting::EVENT_GAME;
                        break;
                }
            } else if($brandRecord->getTotalDepositsAmount() < $updatedBrandRecord->getTotalDepositsAmount()) {
                $event  = PixelSetting::EVENT_DEPOSIT;
            } else if($brandRecord->getTotalGamesCount() < $updatedBrandRecord->getTotalGamesCount()) {
                $event  = PixelSetting::EVENT_GAME;
            } else {
                return $this->getPixelResponseNoEventGivenAndNoneIdentified($origin);
            }

        } else {
            /** @var BrandRecord $brandRecord */
            $updatedBrandRecord = $this->updatedBrandRecord($record);
        }

        if(!$updatedBrandRecord) {
            return $this->getPixelResponseBrandRecordNotFound($origin);
        }

        //Get relevant pixel
        /** @var PixelSetting $pixelSetting */
        $pixelSetting = false;
        switch($event) {
            case PixelSetting::EVENT_LEAD:
                $pixelSetting = $updatedBrandRecord->getUser()->getLeadPixel();
                break;
            case PixelSetting::EVENT_CUSTOMER:
                $pixelSetting = $updatedBrandRecord->getUser()->getCustomerPixel();
                break;
            case PixelSetting::EVENT_DEPOSIT:
                $pixelSetting = $updatedBrandRecord->getUser()->getDepositPixel();
                break;
            case PixelSetting::EVENT_GAME:
                $pixelSetting = $updatedBrandRecord->getUser()->getGamePixel();
                break;
        }

        //Get matching commission plan
        $commissionPlan = $this->getCommissionPlan($updatedBrandRecord);
        if($commissionPlan && !$updatedBrandRecord->getCommissionPlan()) {
            $updatedBrandRecord->setCommissionPlan($commissionPlan);
            $em->persist($updatedBrandRecord);
            $em->flush();
        }

        if(!$pixelSetting) {
            // Pixel is not defined
            return $this->getPixelResponseAffiliatePixelNotDefined($origin);
        }

        if($updatedBrandRecord->getCommissionPlan() && PixelSetting::FIRE_CONDITION_ON_QUALIFICATION == $pixelSetting->getFireCondition()) {
            // Commission already set
            return $this->getPixelResponseBrandRecordAlreadyCommissionQualified($origin);
        }

        if(!$commissionPlan && PixelSetting::FIRE_CONDITION_MATCH_A_COMMISSION_PLAN == $pixelSetting->getFireCondition()) {
            // No Commission Plan Match
            return $this->getPixelResponseBrandRecordNoCommissionPlanMatch($origin);
        }

        //Save pixel log
        $pixelLog = new PixelLog();
        $pixelLog->setAttempts(1);
        $pixelLog->setEvent($event);
        $pixelLog->setUrl($this->getOutGoingPixelURL($updatedBrandRecord, $pixelSetting));
        $pixelLog->setAction($pixelSetting->getAction());
        $pixelLog->setDestinationType($pixelSetting->getDestinationType());
        $pixelLog->setOriginType($origin);
        $pixelLog->setBrand($updatedBrandRecord->getBrand());
        $pixelLog->setUser($updatedBrandRecord->getUser());
        $pixelLog->setOffer($updatedBrandRecord->getOffer());
        $pixelLog->setBrandRecord($updatedBrandRecord);

        if(PixelSetting::DESTINATION_TYPE_CLIENT == $pixelSetting->getDestinationType() &&
            PixelSetting::ORIGIN_TYPE_CLIENT != $origin) {
            $pixelLog->setStatus(PixelLog::STATUS_WILL_NOT_FIRE);
            $em->persist($pixelLog);
            $em->flush();
            // Platform-Server-pixel cannot post-back to Affiliate-Client-pixel
            return $this->getPixelResponseServerClientConflict($origin);

        } else if(PixelSetting::DESTINATION_TYPE_CLIENT == $pixelSetting->getDestinationType()) {
            $pixelLog->setStatus(PixelLog::STATUS_SUCCESS);
            $em->persist($pixelLog);
            $em->flush();
            // Redirect
            return $this->getPixelResponseSuccess($origin);
        } else {
			$pixelLog->setAttempts(0);
            $pixelLog->setStatus(PixelLog::STATUS_SERVER_PENDING);
            $em->persist($pixelLog);
            $em->flush();
            return $this->getPixelResponseSuccess($origin);
        }
    }

    /**
     * a default Response for incoming client pixel
     *  used in cases such
     *      - No matching commission plan
     *      - Pixel already fired
     *      - No Pixel need to fire (Affiliate doesn't have pixel in his setting)
     *      - Incoming request error (Cannot find which record the incoming pixel is related to)
     * @param Brand $brand
     * @return RedirectResponse
     */
    protected function getPixelGifImageURL(Brand $brand)
    {
        return $brand->getHost() . '/img/pixel.gif';
    }

    /**
     *
     * @param BrandRecord $brandRecord
     * @return bool|string
     */
    public function getOutGoingPixelURL(BrandRecord $brandRecord, PixelSetting $pixelSetting)
    {
        if(!$brandRecord->getUser()) {
            return false;
        }

        $parameters = $brandRecord->getOfferClick() ? $brandRecord->getOfferClick()->getParameters() : array();
        return $this->appendParametersToURL($pixelSetting->getUrl(), $parameters);
    }

    /**
     * Find best matching CommissionPlan
     * @param BrandRecord $brandRecord
     * @return CommissionPlan|false
     */
    public function getCommissionPlan(BrandRecord $brandRecord)
    {
        if($brandRecord->getCommissionPlan()) {
            return $brandRecord->getCommissionPlan();
        }

        $commissionPlans = $this->doctrine->getManager()->getRepository('AppBundle\Entity\CommissionPlan')
            ->findBy(array('brand'=>$this->brand, 'users'=>array($brandRecord->getUser())), array('priority' => 'ASC'), 1);

        /** @var CommissionPlan $commissionPlan */
        foreach($commissionPlans AS $commissionPlan) {
            if($commissionPlan->getCriteria()->isMatch($commissionPlan, $brandRecord)) {
                return $brandRecord;
            }
        }

        if(is_array($commissionPlans) && isset($commissionPlans[0])) {
            return $commissionPlans[0];
        }

        return false;
    }

	/**
     * Append parameters to URL only if not exists!
	 * @param $url
	 * @param array $parameters
	 * @return string $url
	 */
    protected function appendParametersToURL($url, array $parameters=array())
    {
        $urlParts = parse_url($url);
        parse_str((isSet($urlParts['query']) ? $urlParts['query'] : ''), $params);
        $params = array_merge($parameters, $params);
        $urlParts['query'] = http_build_query($params);

        $url = $urlParts['scheme'] . '://' . $urlParts['host'];
        if(isSet($urlParts['path'])) {
            $url .= $urlParts['path'];
        }
        if(isSet($urlParts['query'])) {
            $url .= '?';
            $url .= $urlParts['query'];
        }

        return $url;
    }

    /**
     * @param $id
     * @return Offer
     */
    protected function getOffer($id)
    {
        return $this->doctrine->getManager()->getRepository('AppBundle\Entity\Offer')
            ->findOneBy(array('brand'=>$this->brand, 'id'=>$id));
    }
    /**
     * @param $id
     * @return OfferBanner
     */
    protected function getOfferBanner($id)
    {
        return $this->doctrine->getManager()->getRepository('AppBundle\Entity\OfferBanner')
            ->findOneBy(array('brand'=>$this->brand, 'id'=>$id));
    }
    /**
     * @param $id
     * @return OfferClick
     */
    protected function getOfferClick($id)
    {
        return $this->doctrine->getManager()->getRepository('AppBundle\Entity\OfferClick')
            ->findOneBy(array('brand'=>$this->brand, 'id'=>$id));
    }
}