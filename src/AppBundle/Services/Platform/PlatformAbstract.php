<?php

namespace AppBundle\Services\Platform;

use AppBundle\Entity\Brand;
use AppBundle\Entity\OfferClick;
use AppBundle\Services\Platform\CommissionPlan\CriteriaTypeAbstract;
use AppBundle\Services\Platform\Exception\InvalidSettingException;
use AppBundle\Services\Platform\SettingAbstract;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
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
    //abstract public function handleLead();
    //abstract public function handleCustomer();
    //abstract public function handleDeposit();
    //abstract public function handleGame();

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
}