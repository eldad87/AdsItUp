<?php

namespace AppBundle\Services\Platform;

use AppBundle\Entity\OfferClick;
use AppBundle\Entity\SpotBrand;
use AppBundle\Entity\SpotBrandSetting;
use AppBundle\Services\Platform\Exception\MissingBrandSettingException;

class Spot extends PlatformAbstract {

	/**
	 * @inheritdoc
	 */
	public function handleClick(OfferClick $offerClick)
	{
		$em = $this->doctrine->getManager();
		/** @var SpotBrandSetting $spotBrand */
		$spotBrand = $em->getRepository('AppBundle:SpotBrandSetting')->findOneBy(array(
			'brand' => $offerClick->getBrand()
		));

		if(!$spotBrand) {
			throw new MissingBrandSettingException(sprintf('SpotBrandSettings are not defined for brand %d',
				$offerClick->getBrand()->getId(),$offerClick->getBrand()->getHost()));
		}

		$pixelParameter = array(
			'campaignId' => $spotBrand->getCampaignId(),
			'subCampaign' => sprintf('%s_%s_%s',
				$offerClick->getId(), $offerClick->getUser()->getId(), $offerClick->getOffer()->getId())
		);

		$destination = $offerClick->getOffer()->getDestination();
		return $this->appendParametersToURL($destination, $pixelParameter);
	}
}