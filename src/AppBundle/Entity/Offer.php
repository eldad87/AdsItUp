<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer")
 * @GRID\Source(columns="id, name, destination, description, isActive, offerCategory.name, brand.name")
 * @ORM\HasLifecycleCallbacks()
 */
class Offer {
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
     * @Assert\Length(
     *      min = 2,
     *      max = 255
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @GRID\Column(title="Name", type="text", operatorsVisible=false)
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 500
     * )
     * @ORM\Column(type="string", length=500)
     *
     * @GRID\Column(title="Destination", type="text", operatorsVisible=false)
     */
    protected $destination;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 50
     * )
     *
     * @ORM\Column(type="text")
     *
     * @GRID\Column(title="Description", type="text", operatorsVisible=false)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=32, options={"fixed" = true})
     */
    protected $salt;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     *
     * @GRID\Column(title="Is Active", type="boolean", operatorsVisible=false)
     */
    protected $isActive;

    /**
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
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="OfferCategory", inversedBy="offers")
     *
     * @GRID\Column(field="offerCategory.name", title="Category", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $offerCategory;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="offers")
     *
     * @GRID\Column(field="brand.name", title="Brand", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $brand;

    /**
     * @ORM\OneToMany(targetEntity="OfferBanner", mappedBy="offer")
     */
    protected $offerBanners;

    /**
     * @ORM\OneToMany(targetEntity="OfferClick", mappedBy="offer")
     */
    protected $offerClicks;

    /**
     * @ORM\OneToMany(targetEntity="BrandRecord", mappedBy="offer")
     */
    protected $brandRecords;

    /**
     * @ORM\OneToMany(targetEntity="PixelLog", mappedBy="offer")
     */
    protected $pixelLog;

    public function __construct()
    {
        $this->offerBanners = new ArrayCollection();
        $this->offerClicks = new ArrayCollection();
        $this->brandRecords = new ArrayCollection();
        $this->pixelLog = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     * @return $this
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
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
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
     * Set brand
     *
     * @param \AppBundle\Entity\Brand $brand
     * @return User
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
     * Set offerCategory
     *
     * @param \AppBundle\Entity\OfferCategory $offerCategory
     * @return Offer
     */
    public function setOfferCategory(\AppBundle\Entity\OfferCategory $offerCategory = null)
    {
        $this->offerCategory = $offerCategory;

        return $this;
    }

    /**
     * Get offerCategory
     *
     * @return \AppBundle\Entity\OfferCategory 
     */
    public function getOfferCategory()
    {
        return $this->offerCategory;
    }

    /**
     * Add offers
     *
     * @param \AppBundle\Entity\OfferBanner $offerBanner
     * @return OfferCategory
     */
    public function addOfferBanner(\AppBundle\Entity\OfferBanner $offerBanner)
    {
        $this->offerBanners[] = $offerBanner;

        return $this;
    }

    /**
     * Remove offers
     *
     * @param \AppBundle\Entity\OfferBanner $offerBanner
     */
    public function removeOfferBanner(\AppBundle\Entity\OfferBanner $offerBanner)
    {
        $this->offerBanners->removeElement($offerBanner);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOfferBanners()
    {
        return $this->offerBanners;
    }

    /**
     * Add click
     *
     * @param \AppBundle\Entity\OfferClick $offerClick
     * @return OfferCategory
     */
    public function addOfferClick(\AppBundle\Entity\OfferClick $offerClick)
    {
        $this->offerClicks[] = $offerClick;

        return $this;
    }

    /**
     * Remove click
     *
     * @param \AppBundle\Entity\OfferClick $offerClick
     */
    public function removeOfferClick(\AppBundle\Entity\OfferClick $offerClick)
    {
        $this->offerClicks->removeElement($offerClick);
    }

    /**
     * Get clicks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOfferClicks()
    {
        return $this->offerClicks;
    }

    /**
     * Add record
     *
     * @param \AppBundle\Entity\BrandRecord $brandRecord
     * @return OfferCategory
     */
    public function addBrandRecord(\AppBundle\Entity\BrandRecord $brandRecord)
    {
        $this->brandRecords[] = $brandRecord;

        return $this;
    }

    /**
     * Remove record
     *
     * @param \AppBundle\Entity\BrandRecord $brandRecord
     */
    public function removeBrandRecord(\AppBundle\Entity\BrandRecord $brandRecord)
    {
        $this->brandRecords->removeElement($brandRecord);
    }

    /**
     * Get records
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrandRecords()
    {
        return $this->brandRecords;
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

    public function __toString()
    {
        return $this->getName();
    }
}
