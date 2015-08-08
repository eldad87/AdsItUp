<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="brand")
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
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="brand")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="brand")
     */
    protected $offers;

    /**
     * @ORM\OneToMany(targetEntity="OfferCategory", mappedBy="brand")
     */
    protected $offerCategories;

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
}
