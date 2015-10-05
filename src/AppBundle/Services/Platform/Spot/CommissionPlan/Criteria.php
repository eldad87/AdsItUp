<?php

namespace AppBundle\Services\Platform\Spot\CommissionPlan;

use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\CommissionPlan;
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
	protected $customerSelectedLang;

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
	 * @Assert\NotBlank()
	 * @Assert\Range(
	 *      min = 0
	 * )
	 */
	protected $minPositionCount;

	public function isMatch(CommissionPlan $commissionPlan, BrandRecord $brandRecord)
	{
		//CPL can accept all types of records
		if(CommissionPlan::TYPE_CPL == $commissionPlan->getStrategy()) {
			if(!in_array($brandRecord->getType(),
				array(BrandRecord::USER_TYPE_LEAD, BrandRecord::USER_TYPE_CUSTOMER,
					BrandRecord::USER_TYPE_DEPOSITOR, BrandRecord::USER_TYPE_GAMER))) {
				return false;
			}
		//CPA accept only depositor and gamer types of records
		} else if(CommissionPlan::TYPE_CPA == $commissionPlan->getStrategy()) {
			if(!in_array($brandRecord->getType(),
				array(BrandRecord::USER_TYPE_DEPOSITOR, BrandRecord::USER_TYPE_GAMER))) {
				return false;
			}
		}

		//Check CPA / CPL conditions
		$record = $brandRecord->getRecord();
		if(!isSet($record['Country']) || strtolower($record['Country'])!=strtolower($this->getCountry())) {
			return false;
		}
		if(!isSet($record['saleStatus']) || strtolower($record['saleStatus'])!=strtolower($this->getSaleStatus())) {
			return false;
		}
		if(!isSet($record['leadStatus']) || strtolower($record['leadStatus'])!=strtolower($this->getLeadStatus())) {
			return false;
		}
		if((!isSet($record['siteLanguage']) && !isSet($record['customerSelectedLang'])) ||
			(strtolower($record['siteLanguage'])!=strtolower($this->getSiteLanguage()) &&
				strtolower($record['customerSelectedLang'])!=strtolower($this->getCustomerSelectedLang()))) {
			return false;
		}

		if(CommissionPlan::TYPE_CPA == $commissionPlan->getStrategy()) {
			if($this->getMinDepositAmount() < $brandRecord->getTotalDepositsAmount()) {
				return false;
			}
			if($this->getMinPositionCount() < $brandRecord->getTotalPositionsCount()) {
				return false;
			}
		}

		return true;
	}

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
	public function getCustomerSelectedLang()
	{
		return $this->customerSelectedLang;
	}

	/**
	 * @param array $customerSelectedLang
	 * @return $this
	 */
	public function setCustomerSelectedLang($customerSelectedLang)
	{
		$this->customerSelectedLang = $customerSelectedLang;
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
	
	/**
	 * @return float
	 */
	public function getMinPositionCount()
	{
		return $this->minPositionCount;
	}

	/**
	 * @param float $minPositionCount
	 * @return $this
	 */
	public function setMinPositionCount($minPositionCount)
	{
		$this->minPositionCount = $minPositionCount;
		return $this;
	}
}