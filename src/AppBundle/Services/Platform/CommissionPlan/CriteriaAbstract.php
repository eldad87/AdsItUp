<?php
namespace AppBundle\Services\Platform\CommissionPlan;

use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\CommissionPlan;

abstract class CriteriaAbstract {
	/**
	 * Check if criteria match BrandRecord
	 * @param CommissionPlan $commissionPlan
	 * @param BrandRecord $brandRecord
	 * @return bool
	 */
	abstract public function isMatch(CommissionPlan $commissionPlan, BrandRecord $brandRecord);
}