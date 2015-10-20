<?php
namespace AppBundle\Services\Platform;

use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\OfferClick;
use AppBundle\Entity\User;

class RecordAffiliateIdentity {
	private $id;
	/**
	 * @var BrandRecord::USER_TYPE_*
	 */
	private $type;
	private $country;
	private $language;
	private $status;
	private $totalDepositsAmount;
	private $totalGamesCount;
	/** @var  \DateTime */
	private $createdAt;
	/** @var  \DateTime */
	private $updatedAt;

	/** @var User */
	private $user;
	/** @var Offer */
	private $offer;
	/** @var OfferBanner */
	private $offerBanner;
	/** @var OfferClick */
	private $offerClick;

	public function __construct($id, $type, $country, $language, $status, \Datetime $createdAt, \Datetime $updatedAt, $totalDepositsAmount, $totalGamesCount,
								User $user, Offer $offer=null, OfferBanner $offerBanner=null, OfferClick $offerClick=null)
	{
		$this->id = $id;
		$this->type = $type;
		$this->country = $country;
		$this->language = $language;
		$this->status = $status;
		$this->totalDepositsAmount = $totalDepositsAmount;
		$this->totalGamesCount = $totalGamesCount;
		$this->user = $user;
		$this->offer = $offer;
		$this->offerBanner = $offerBanner;
		$this->offerClick = $offerClick;
		$this->createdAt = $createdAt;
		$this->updatedAt = $updatedAt;
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
	 * @return mixed
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @return User
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
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
	public function getTotalGamesCount()
	{
		return $this->totalGamesCount;
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