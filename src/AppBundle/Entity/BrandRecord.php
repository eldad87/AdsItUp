<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="brand_record")
 * @ORM\HasLifecycleCallbacks()
 * @GRID\Source(columns="id, remote_id")
 * @UniqueEntity(
 *     fields={"brand", "externalId"},
 *     errorPath="externalId",
 *     message="This external-id is already in use."
 * )
 */
class BrandRecord {

    const USER_TYPE_LEAD = 1;
    const USER_TYPE_CUSTOMER = 2;
    const USER_TYPE_DEPOSITOR = 3;
    const USER_TYPE_GAMER = 4;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(title="Id", type="number", operatorsVisible=false)
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     *
     * @GRID\Column(title="External Id", type="number", operatorsVisible=false)
     */
    protected $externalId;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"1", "2", "3", "4"})
     *
     * @ORM\Column(type="integer")
     *
     * @GRID\Column(title="Type", type="text", operatorsVisible=false)
     */
    protected $type;


    /**
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0
     * )
     *
     * @ORM\Column(type="decimal")
     *
     * @GRID\Column(title="Total Deposits Amount", operatorsVisible=false, defaultOperator="btw")
     */
    protected $totalDepositsAmount;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0
     * )
     *
     * @ORM\Column(type="integer")
     *
     * @GRID\Column(title="Total Positions Count", operatorsVisible=false, defaultOperator="btw")
     */
    protected $totalPositionsCount;

    /**
     * @ORM\Column(type="datetime")
     *
     * @GRID\Column(title="Created At", type="datetime", operatorsVisible=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @GRID\Column(title="Updated At", type="datetime", operatorsVisible=false)
     */
    protected $updatedAt;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="array")
     */
    protected $record;

    /**
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="brandRecords")
     *
     * @GRID\Column(field="offer.name", title="Offer", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $offer;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="brandRecords")
     *
     * @GRID\Column(field="user.username", title="Username", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="refBrandRecords")
     * @GRID\Column(field="referrer.username", title="Referrer First Name", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $referrer;

    /**
     * @ORM\ManyToOne(targetEntity="OfferBanner", inversedBy="brandRecords")
     *
     * @GRID\Column(field="OfferBanner.name", title="Banner", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $offerBanner;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="OfferClick", inversedBy="brandRecords")
     *
     * @GRID\Column(field="offerClick.createdAt", title="Click", operatorsVisible=false, operator="btwe")
     */
    protected $offerClick;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="brandRecords")
     */
    protected $brand;

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param mixed $externalId
     * @return $this
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
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
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalDepositsAmount()
    {
        return $this->totalDepositsAmount;
    }

    /**
     * @param mixed $totalDepositsAmount
     * @return $this
     */
    public function setTotalDepositsAmount($totalDepositsAmount)
    {
        $this->totalDepositsAmount = $totalDepositsAmount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalPositionsCount()
    {
        return $this->totalPositionsCount;
    }

    /**
     * @param mixed $totalPositionsCount
     * @return $this
     */
    public function setTotalPositionsCount($totalPositionsCount)
    {
        $this->totalPositionsCount = $totalPositionsCount;
        return $this;
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

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setUpdatedAt(\DateTime $createdAt)
    {
        $this->updatedAt = $createdAt;
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
     * @return array
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param array $record
     * @return $this;
     */
    public function setRecord(array $record)
    {
        $this->record = $record;
        return $this;
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
     * Set offer banner
     *
     * @param \AppBundle\Entity\OfferBanner $offerBanner
     * @return $this
     */
    public function setOfferBanner(\AppBundle\Entity\OfferBanner $offerBanner = null)
    {
        $this->offerBanner = $offerBanner;

        return $this;
    }

    /**
     * Get offer banner
     *
     * @return \AppBundle\Entity\OfferBanner
     */
    public function getOfferBanner()
    {
        return $this->offerBanner;
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
     * Set referrer
     *
     * @param \AppBundle\Entity\User $referrer
     * @return $this
     */
    public function setReferrer(\AppBundle\Entity\User $referrer = null)
    {
        $this->referrer = $referrer;
        return $this;
    }

    /**
     * Get referrerCategory
     *
     * @return \AppBundle\Entity\User
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * Set offerClick
     *
     * @param \AppBundle\Entity\OfferClick $offerClick
     * @return $this
     */
    public function setOfferClick(\AppBundle\Entity\OfferClick $offerClick = null)
    {
        $this->offerClick = $offerClick;
        return $this;
    }

    /**
     * Get offerclick
     *
     * @return \AppBundle\Entity\OfferClick
     */
    public function getOfferClick()
    {
        return $this->offerClick;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
