<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="brand_record", indexes={@ORM\Index(name="updatedAt", columns={"is_processed", "updated_at"})})
 * @ORM\HasLifecycleCallbacks()
 * @GRID\Source(columns="id, externalId, offer.name, user.email, referrer.email, type, country, language, status, totalDepositsAmount, totalGamesCount, commissionPlan.name, payout, recordCreatedAt, recordUpdatedAt")
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
     * @GRID\Column(title="Type", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="Lead","2"="Customer","3"="Depositor","4"="Gamer"})
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=2, options={"fixed" = true})
     *
     * @GRID\Column(title="Country", operatorsVisible=false, operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=2, options={"fixed" = true})
     *
     * @GRID\Column(title="Language", operatorsVisible=false, operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $language;

    /**
     * @ORM\Column(type="string")
     *
     * @GRID\Column(title="Status", operatorsVisible=false, operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $status;

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
     * @GRID\Column(title="Total Games Count", operatorsVisible=false, defaultOperator="btw")
     */
    protected $totalGamesCount;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     *
     * @GRID\Column(title="Is Processed", type="boolean", operatorsVisible=false)
     */
    protected $isProcessed;

    /**
     * @ORM\Column(type="datetime")
     *
     * @GRID\Column(title="Created At", type="datetime", operatorsVisible=false, defaultOperator="btwe")
     */
    protected $recordCreatedAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @GRID\Column(title="Updated At", type="datetime", operatorsVisible=false, defaultOperator="btwe")
     */
    protected $recordUpdatedAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @GRID\Column(title="DB Created At", type="datetime", operatorsVisible=false, defaultOperator="btwe")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @GRID\Column(title="DB Updated At", type="datetime", operatorsVisible=false, defaultOperator="btwe")
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
     * @GRID\Column(field="user.username", title="Username", operatorsVisible=false, filter="select", selectFrom="query", role="ROLE_AFFILIATE_MANAGER")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="refBrandRecords")
     * @GRID\Column(field="referrer.username", title="Referrer First Name", operatorsVisible=false, filter="select", selectFrom="query", role="ROLE_AFFILIATE_MANAGER")
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
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="CommissionPlan", inversedBy="brandRecords")
     *
     * @GRID\Column(field="commissionPlan.name", title="Commission Plan", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $commissionPlan;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0
     * )
     *
     * @ORM\Column(type="decimal")
     *
     * @GRID\Column(title="Payout", operatorsVisible=false, defaultOperator="btw")
     */
    protected $payout;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0
     * )
     *
     * @ORM\Column(type="decimal")
     *
     * @GRID\Column(title="Referrer Payout", operatorsVisible=false, defaultOperator="btw")
     */
    protected $referrerPayout;

    /**
     * @ORM\OneToMany(targetEntity="PixelLog", mappedBy="brandRecord")
     */
    protected $pixelLog;

    public function __construct()
    {
        $this->pixelLog = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


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
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     * @return BrandRecord
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     * @return BrandRecord
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return BrandRecord
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getTotalGamesCount()
    {
        return $this->totalGamesCount;
    }

    /**
     * @param mixed $totalGamesCount
     * @return $this
     */
    public function setTotalGamesCount($totalGamesCount)
    {
        $this->totalGamesCount = $totalGamesCount;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsProcessed()
    {
        return $this->isProcessed;
    }

    /**
     * @param bool $isProcessed
     * @return $this
     */
    public function setIsProcessed($isProcessed)
    {
        $this->isProcessed = $isProcessed;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRecordCreatedAt()
    {
        return $this->recordCreatedAt;
    }

    /**
     * @param \DateTime $recordCreatedAt
     */
    public function setRecordCreatedAt(\DateTime $recordCreatedAt)
    {
        $this->recordCreatedAt = $recordCreatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getRecordUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $recordUpdatedAt
     */
    public function setRecordUpdatedAt(\DateTime $recordUpdatedAt)
    {
        $this->recordUpdatedAt = $recordUpdatedAt;
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
     * @ORM\PrePersist
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

    /**
     * Set Commission Plan
     *
     * @param \AppBundle\Entity\CommissionPlan $commissionPlan
     * @return User
     */
    public function setCommissionPlan(\AppBundle\Entity\CommissionPlan $commissionPlan = null)
    {
        $this->commissionPlan = $commissionPlan;

        return $this;
    }

    /**
     * Get Commission Plan
     *
     * @return \AppBundle\Entity\CommissionPlan
     */
    public function getCommissionPlan()
    {
        return $this->commissionPlan;
    }

    /**
     * @return float
     */
    public function getPayout()
    {
        return $this->payout;
    }

    /**
     * @param float $payout
     * @return $this
     */
    public function setPayout($payout)
    {
        $this->payout = $payout;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReferrerPayout()
    {
        return $this->referrerPayout;
    }

    /**
     * @param mixed $referrerPayout
     * @return BrandRecord
     */
    public function setReferrerPayout($referrerPayout)
    {
        $this->referrerPayout = $referrerPayout;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * Add PixelLog
     *
     * @param \AppBundle\Entity\PixelLog $pixelLog
     * @return OfferCategory
     */
    public function addPixelLog(\AppBundle\Entity\PixelLog $pixelLog)
    {
        $this->pixelLog[] = $pixelLog;

        return $this;
    }

    /**
     * Remove record
     *
     * @param \AppBundle\Entity\PixelLog $pixelLog
     */
    public function removePixelLog(\AppBundle\Entity\PixelLog $pixelLog)
    {
        $this->pixelLog->removeElement($pixelLog);
    }

    /**
     * Get records
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPixelLogs()
    {
        return $this->pixelLog;
    }
}
