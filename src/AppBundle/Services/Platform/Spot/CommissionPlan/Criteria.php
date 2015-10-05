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
	protected $totalDepositAmount;
	/**
	 * @Assert\NotBlank()
	 * @Assert\Range(
	 *      min = 0
	 * )
	 */
	protected $totalPositionCount;

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
	public function getTotalDepositAmount()
	{
		return $this->totalDepositAmount;
	}

	/**
	 * @param float $totalDepositAmount
	 * @return $this
	 */
	public function setTotalDepositAmount($totalDepositAmount)
	{
		$this->totalDepositAmount = $totalDepositAmount;
		return $this;
	}
	
	/**
	 * @return float
	 */
	public function getTotalPositionCount()
	{
		return $this->totalPositionCount;
	}

	/**
	 * @param float $totalPositionCount
	 * @return $this
	 */
	public function setTotalPositionCount($totalPositionCount)
	{
		$this->totalPositionCount = $totalPositionCount;
		return $this;
	}
}