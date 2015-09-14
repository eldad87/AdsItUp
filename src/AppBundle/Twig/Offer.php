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
			'getClickParameters' => new \Twig_Function_Method($this, 'getClickParameters'),
			'getBannerClickParameters' => new \Twig_Function_Method($this, 'getBannerClickParameters'),
			'getClickHost' => new \Twig_Function_Method($this, 'getClickHost'),
		);
	}

	public function getClickParameters(OfferEntity $offer, $encoded=true)
	{
		return $this->offer->getClickParameters($offer, $encoded);
	}

	public function getBannerClickParameters(OfferBanner $banner, $encoded=true)
	{
		return $this->offer->getBannerClickParameters($banner, $encoded);
	}

	public function getClickHost(OfferEntity $offer)
	{
		return $this->offer->getClickHost($offer);
	}

	public function getName()
	{
		return 'twig_offer';
	}
}