<?php
namespace AppBundle\Services\Platform;

use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\OfferClick;
use AppBundle\Entity\User;

class RecordAffiliateIdentity {
	private $id;
	private $type;
	private $totalDepositsAmount;
	private $totalPositionsCount;

	/** @var User */
	private $user;
	/** @var Offer */
	private $offer;
	/** @var OfferBanner */
	private $offerBanner;
	/** @var OfferClick */
	private $offerClick;

	public function __construct($id, $type, $totalDepositsAmount, $totalPositionsCount,
								User $user, Offer $offer=null, OfferBanner $offerBanner=null, OfferClick $offerClick=null)
	{
		$this->id = $id;
		$this->type = $type;
		$this->totalDepositsAmount = $totalDepositsAmount;
		$this->totalPositionsCount = $totalPositionsCount;
		$this->user = $user;
		$this->offer = $offer;
		$this->offerBanner = $offerBanner;
		$this->offerClick = $offerClick;
	}

	/**
	 * @return User
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return User
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function getTotalDepositsAmount()
	{
		return $this->totalDepositsAmount;
	}

	/**
	 * @return mixed
	 */
	public function getTotalPositionsCount()
	{
		return $this->totalPositionsCount;
	}

	/**
	 * @return Offer|null
	 */
	public function getOffer()
	{
		return $this->offer;
	}

	/**
	 * @return OfferBanner|null
	 */
	public function getOfferBanner()
	{
		return $this->offerBanner;
	}

	/**
	 * @return OfferClick|null
	 */
	public function getOfferClick()
	{
		return $this->offerClick;
	}
}