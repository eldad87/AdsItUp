<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Brand;
use AppBundle\Services\Platform\CommissionPlan\CriteriaAbstract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="pixel_log")
 * @GRID\Source(columns="id, offer.name, user.email, event, destinationType, originType, action, attempts, status, url, createdAt, updatedAt")
 * @ORM\HasLifecycleCallbacks()
 */
class PixelLog {
	const STATUS_UNKNOWN = 0;
	const STATUS_SUCCESS = 1;
	const STATUS_ERROR = 2;
	const STATUS_SERVER_PENDING = 3;
	const STATUS_WILL_NOT_FIRE = 4;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 *
	 * @GRID\Column(title="Id", type="number", operatorsVisible=false)
	 */
	protected $id;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"1", "2", "3", "4"})
	 *
	 * @ORM\Column(type="integer", options={"default" = 1})
	 *
	 * @GRID\Column(title="Event", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="Lead","2"="Customer","3"="Deposit","4"="Game"})
	 */
	protected $event;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"1", "2"})
	 *
	 * @ORM\Column(type="integer")
	 *
	 * @GRID\Column(title="Destination", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="Client","2"="Server"})
	 */
	protected $destinationType;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"1", "2", "3"})
	 *
	 * @ORM\Column(type="integer")
	 *
	 * @GRID\Column(title="Origin", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="Client","2"="Server","3"="CLI"})
	 */
	protected $originType;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"GET", "POST"})
	 *
	 * @ORM\Column(type="integer")
	 *
	 * @GRID\Column(title="Action", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="GET","2"="POST"})
	 */
	protected $action;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Url()
	 *
	 * @ORM\Column(type="text")
	 *
	 * @GRID\Column(title="Name", type="text", operatorsVisible=false, role="ROLE_AFFILIATE_MANAGER")
	 */
	protected $url;

	/**
	 * @ORM\Column(type="integer")
	 *
	 * @GRID\Column(title="Attempts", type="number", operatorsVisible=false)
	 */
	protected $attempts;


	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @GRID\Column(title="Response", type="text", operatorsVisible=false)
	 */
	protected $response;

	/**
	 * @Assert\NotBlank()
	 *
	 * @ORM\Column(type="boolean")
	 *
	 * @GRID\Column(title="Status", operatorsVisible=false, filter="select", selectFrom="values", values={"0"="Unknown","1"="Success","2"="Error","3"="Server Pending","4"="Will not fire"})
	 */
	protected $status;

	/**
	 * @Assert\NotBlank()
	 * @ORM\Column(type="datetime")
	 *
	 * @GRID\Column(title="Created At", type="datetime", operatorsVisible=false, defaultOperator="btwe")
	 */
	protected $createdAt;

	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @GRID\Column(title="Updated At", type="datetime", operatorsVisible=false, defaultOperator="btwe")
	 */
	protected $updatedAt;

	/**
	 * @Assert\NotBlank(groups={"postUpload"})
	 *
	 * @ORM\ManyToOne(targetEntity="Offer", inversedBy="offerBanners")
	 *
	 * @GRID\Column(field="offer.name", title="Offer", operatorsVisible=false, filter="select", selectFrom="query")
	 */
	protected $offer;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="offerClicks")
	 *
	 * @GRID\Column(field="user.username", title="Username", operatorsVisible=false, filter="select", selectFrom="query")
	 */
	protected $user;

	/**
	 * @Assert\NotBlank()
	 *
	 * @ORM\ManyToOne(targetEntity="Brand", inversedBy="pixelLog")
	 *
	 * @GRID\Column(field="brand.name", title="Brand", operatorsVisible=false, filter="select", selectFrom="query")
	 */
	protected $brand;

	/**
	 * @Assert\NotBlank()
	 * @ORM\OneToMany(targetEntity="BrandRecord", mappedBy="pixelLog")
	 */
	protected $brandRecord;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getEvent()
	{
		return $this->event;
	}

	/**
	 * @param mixed $event
	 * @return $this
	 */
	public function setEvent($event)
	{
		$this->event = $event;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDestinationType()
	{
		return $this->destinationType;
	}

	/**
	 * @param mixed $destinationType
	 * @return $this
	 */
	public function setDestinationType($destinationType)
	{
		$this->destinationType = $destinationType;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getOriginType()
	{
		return $this->originType;
	}

	/**
	 * @param mixed $originType
	 * @return PixelLog
	 */
	public function setOriginType($originType)
	{
		$this->originType = $originType;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param mixed $action
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param mixed $url
	 * @return $this;
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAttempts()
	{
		return $this->attempts;
	}

	/**
	 * @param mixed $attempts
	 * @return PixelLog
	 */
	public function setAttempts($attempts)
	{
		$this->attempts = $attempts;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * @param mixed $response
	 * @return $this;
	 */
	public function setResponse($response)
	{
		$this->response = $response;
		return $this;
	}

	/**
	 * Set status
	 *
	 * @param int $status
	 * @return $this
	 */
	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * Get isSuccess
	 *
	 * @return boolean
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
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt(\DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedAtValue()
	{
		$this->createdAt = new \DateTime();
	}

	public function __toString()
	{
		return (string) $this->getId();
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	/**
	 * @param \DateTime $updatedAt
	 */
	public function setUpdatedAt(\DateTime $updatedAt)
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @ORM\PreUpdate
	 * @ORM\PreUpdate
	 */
	public function setUpdatedAtValue()
	{
		$this->updatedAt = new \DateTime();
	}

	/**
	 * Set user
	 *
	 * @param \AppBundle\Entity\User $user
	 * @return $this
	 */
	public function setUser(\AppBundle\Entity\User $user = null)
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * Get userCategory
	 *
	 * @return \AppBundle\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set offerCategory
	 *
	 * @param \AppBundle\Entity\Offer $offer
	 * @return $this
	 */
	public function setOffer(\AppBundle\Entity\Offer $offer = null)
	{
		$this->offer = $offer;
		return $this;
	}

	/**
	 * Get offer
	 *
	 * @return \AppBundle\Entity\Offer
	 */
	public function getOffer()
	{
		return $this->offer;
	}

	/**
	 * Set brand
	 *
	 * @param \AppBundle\Entity\Brand $brand
	 * @return $this
	 */
	public function setBrand(\AppBundle\Entity\Brand $brand = null)
	{
		$this->brand = $brand;

		return $this;
	}

	/**
	 * Get brand
	 *
	 * @return \AppBundle\Entity\Brand
	 */
	public function getBrand()
	{
		return $this->brand;
	}

	/**
	 * Set brand record
	 *
	 * @param \AppBundle\Entity\BrandRecord $brandRecord
	 * @return $this
	 */
	public function setBrandRecord(\AppBundle\Entity\BrandRecord $brandRecord = null)
	{
		$this->brandRecord = $brandRecord;

		return $this;
	}

	/**
	 * Get brand record
	 *
	 * @return \AppBundle\Entity\BrandRecord
	 */
	public function getBrandRecord()
	{
		return $this->brandRecord;
	}
}