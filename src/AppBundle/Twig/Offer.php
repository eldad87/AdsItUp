<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Offer AS OfferEntity;
use AppBundle\Entity\OfferBanner;
use AppBundle\Services\Offer AS OfferService;

class Offer extends \Twig_Extension
{
	/** @var OfferService  */
	private $offer;

	public function __construct(OfferService $offerService) {
		$this->offer = $offerService;
	}

	public function getFunctions()
	{
		return array(
			'generateOfferClickURL' => new \Twig_Function_Method($this, 'getClickURL'),
			'generateOfferBannerClickURL' => new \Twig_Function_Method($this, 'getBannerClickURL'),
		);
	}

	public function getClickURL(OfferEntity $offer)
	{
		return $this->offer->getClickURL($offer);
	}

	public function getBannerClickURL(OfferBanner $banner)
	{
		return $this->offer->getBannerClickURL($banner);
	}

	public function getName()
	{
		return 'twig_offer';
	}
}