<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="brand")
 * @ORM\HasLifecycleCallbacks()
 */
class Brand {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $host;
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $adServerHost;
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $clickServerHost;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Platform", inversedBy="brands")
     */
    protected $platform;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="brand")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="brand")
     */
    protected $offers;

    /**
     * @ORM\OneToMany(targetEntity="OfferBanner", mappedBy="brand")
     */
    protected $offerBanners;

    /**
     * @ORM\OneToMany(targetEntity="OfferCategory", mappedBy="brand")
     */
    protected $offerCategories;

    /**
     * @ORM\OneToMany(targetEntity="OfferClick", mappedBy="brand")
     */
    protected $offerClicks;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Brand
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
     * Set host
     *
     * @param string $host
     * @return Brand
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getAdServerHost()
    {
        return $this->adServerHost;
    }

    /**
     * @param mixed $adServerHost
     * @return $this
     */
    public function setAdServerHost($adServerHost)
    {
        $this->adServerHost = $adServerHost;
        return $this;
    }

    /**
     * @return string
     */
    public function getClickServerHost()
    {
        return $this->clickServerHost;
    }

    /**
     * @param mixed $clickServerHost
     * @return $this
     */
    public function setClickServerHost($clickServerHost)
    {
        $this->clickServerHost = $clickServerHost;
        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Brand
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
     * @param \DateTime $createdAt
     */
    public function setUpdatedAt(\DateTime $createdAt)
    {
        $this->updatedAt = $createdAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Set platform
     *
     * @param \AppBundle\Entity\Platform $platform
     * @return User
     */
    public function setPlatform(\AppBundle\Entity\Platform $platform = null)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return \AppBundle\Entity\Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Brand
     */
    public function addUser(\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add offers
     *
     * @param \AppBundle\Entity\Offer $offers
     * @return Brand
     */
    public function addOffer(\AppBundle\Entity\Offer $offers)
    {
        $this->offers[] = $offers;

        return $this;
    }

    /**
     * Remove offers
     *
     * @param \AppBundle\Entity\Offer $offers
     */
    public function removeOffer(\AppBundle\Entity\Offer $offers)
    {
        $this->offers->removeElement($offers);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * Add offer banner
     *
     * @param \AppBundle\Entity\OfferBanner $offerBanner
     * @return Brand
     */
    public function addOfferBanner(\AppBundle\Entity\OfferBanner $offerBanner)
    {
        $this->offerBanners[] = $offerBanner;

        return $this;
    }

    /**
     * Remove offer Banner
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
     * Add offerCatrgories
     *
     * @param \AppBundle\Entity\OfferCategory $offerCatrgories
     * @return Brand
     */
    public function addOfferCategory(\AppBundle\Entity\OfferCategory $offerCatrgories)
    {
        $this->offerCategories[] = $offerCatrgories;

        return $this;
    }

    /**
     * Remove offerCatrgories
     *
     * @param \AppBundle\Entity\OfferCategory $offerCatrgories
     */
    public function removeOfferCategory(\AppBundle\Entity\OfferCategory $offerCatrgories)
    {
        $this->offerCategories->removeElement($offerCatrgories);
    }

    /**
     * Get offerCatrgories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfferCategories()
    {
        return $this->offerCategories;
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
}
