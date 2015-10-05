<?php

namespace AppBundle\Services\Platform;

use AppBundle\Entity\Brand;
use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\OfferClick;
use AppBundle\Services\Platform\CommissionPlan\CriteriaTypeAbstract;
use AppBundle\Services\Platform\Exception\InvalidPixelException;
use AppBundle\Services\Platform\Exception\InvalidSettingException;
use AppBundle\Services\Platform\SettingAbstract;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\Validator\Validator\RecursiveValidator;

abstract class PlatformAbstract {
    const PIXEL_TYPE_LEAD = 1;
    const PIXEL_TYPE_CUSTOMER = 2;
    const PIXEL_TYPE_DEPOSIT = 3;
    const PIXEL_TYPE_GAME = 4;

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
     * Fetch record from DB
     * @param $id
     * @param $type
     * @return BrandRecord|null
     */
    protected function _getBrandRecord($id, $type)
    {
        return $this->doctrine->getManager()->getRepository('AppBundle\Entity\BrandRecord')
            ->findOneBy(array('brand'=>$this->brand, 'externalId'=>$id));
    }

    /**
     * Identify affiliate/assets involved with this record
     * @param array $record
     * @return RecordAffiliateIdentity
     * @throws InvalidPixelException
     */
    abstract protected function getAffiliateIdentity(array $record);

    /**
     * Get brand record
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $record
     * @return BrandRecord
     * @throws InvalidPixelException
     */
    public function getBrandRecord(array $record, \Symfony\Component\HttpFoundation\Request $request=null)
    {
        //Get most updated record from API
        $recordAffiliateIdentity = $this->getAffiliateIdentity($record);
        $type = $recordAffiliateIdentity->getType();

        //Get our local DB record
        /** @var BrandRecord $brandRecord */
        $brandRecord = $this->_getBrandRecord($recordAffiliateIdentity->getId(), $type);
        if(!$brandRecord) {
            //No local record found, init a new one
            $brandRecord = new BrandRecord();
            $brandRecord->setBrand($brandRecord->getOffer()->getBrand());
            $brandRecord->setUser($recordAffiliateIdentity->getUser());
            $brandRecord->setOffer($recordAffiliateIdentity->getOffer());
            $brandRecord->setOfferBanner($recordAffiliateIdentity->getOfferBanner());
            $brandRecord->setOfferClick($recordAffiliateIdentity->getOfferClick());
        }

        //Update brand record
        $brandRecord->setType($type);
        $brandRecord->setRecord($record);
        $brandRecord->setTotalDepositsAmount($recordAffiliateIdentity->getTotalDepositsAmount());
        $brandRecord->setTotalPositionsCount($recordAffiliateIdentity->getTotalPositionsCount());

        $this->doctrine->getManager()->persist($brandRecord);
        $this->doctrine->getManager()->flush();
        return $brandRecord;
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