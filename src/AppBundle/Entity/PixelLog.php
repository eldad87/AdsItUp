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
 * @GRID\Source(columns="id, created")
 * @ORM\HasLifecycleCallbacks()
 */
class PixelLog {

	const TYPE_CLIENT = 1;
	const TYPE_SERVER = 2;
	const ACTION_GET = 1;
	const ACTION_POST = 2;

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
	 * @Assert\Choice(choices = {"1", "2"})
	 *
	 * @ORM\Column(type="integer")
	 *
	 * @GRID\Column(title="Type", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="Client","2"="Server"})
	 */
	protected $type;

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
	 * @GRID\Column(title="Name", type="text", operatorsVisible=false)
	 */
	protected $url;

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
	 * @GRID\Column(title="Is Success", type="boolean", operatorsVisible=false)
	 */
	protected $isSuccess;

	/**
	 * @Assert\NotBlank()
	 * @ORM\Column(type="datetime")
	 *
	 * @GRID\Column(title="Created At", type="datetime", operatorsVisible=false)
	 */
	protected $createdAt;

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
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param mixed $type
	 * @return $this;
	 */
	public function setType($type)
	{
		$this->type = $type;
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
	 * Set isSuccess
	 *
	 * @param boolean $isSuccess
	 * @return $this
	 */
	public function setIsSuccess($isSuccess)
	{
		$this->isSuccess = $isSuccess;

		return $this;
	}

	/**
	 * Get isSuccess
	 *
	 * @return boolean
	 */
	public function getIsSuccess()
	{
		return $this->isSuccess;
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