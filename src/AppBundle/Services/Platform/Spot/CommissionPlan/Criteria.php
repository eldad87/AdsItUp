<?php

namespace AppBundle\Services\Platform\Spot\CommissionPlan;

use AppBundle\Services\Platform\CommissionPlan\CriteriaAbstract;
use Symfony\Component\Validator\Constraints as Assert;

class Criteria extends CriteriaAbstract {

	/**
	 * @Assert\NotBlank()
	 */
	protected $country;

	/**
	 * @Assert\NotBlank()
	 */
	protected $siteLanguage;

	/**
	 * @Assert\NotBlank()
	 */
	protected $siteLanguageSelected;

	/**
	 * @Assert\NotBlank()
	 */
	protected $saleStatus;

	/**
	 * @Assert\NotBlank()
	 */
	protected $leadStatus;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Range(
	 *      min = 0
	 * )
	 */
	protected $minDepositAmount;

	/**
	 * @return array
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param array $country
	 * @return $this
	 */
	public function setCountry($country)
	{
		$this->country = $country;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSiteLanguage()
	{
		return $this->siteLanguage;
	}

	/**
	 * @param array $siteLanguage
	 * @return $this
	 */
	public function setSiteLanguage($siteLanguage)
	{
		$this->siteLanguage = $siteLanguage;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSiteLanguageSelected()
	{
		return $this->siteLanguageSelected;
	}

	/**
	 * @param array $siteLanguageSelected
	 * @return $this
	 */
	public function setSiteLanguageSelected($siteLanguageSelected)
	{
		$this->siteLanguageSelected = $siteLanguageSelected;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSaleStatus()
	{
		return $this->saleStatus;
	}

	/**
	 * @param array $saleStatus
	 * @return $this
	 */
	public function setSaleStatus($saleStatus)
	{
		$this->saleStatus = $saleStatus;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getLeadStatus()
	{
		return $this->leadStatus;
	}

	/**
	 * @param array $leadStatus
	 * @return $this
	 */
	public function setLeadStatus($leadStatus)
	{
		$this->leadStatus = $leadStatus;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getMinDepositAmount()
	{
		return $this->minDepositAmount;
	}

	/**
	 * @param float $minDepositAmount
	 * @return $this
	 */
	public function setMinDepositAmount($minDepositAmount)
	{
		$this->minDepositAmount = $minDepositAmount;
		return $this;
	}
}