<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer_banner")
 * @GRID\Source(columns="id, name, width, height")
 */
class OfferBanner {
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
     *      max = 50
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @GRID\Column(title="Name", type="text", operatorsVisible=false)
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 2
     * )
     *
     * @ORM\Column(type="integer")
     *
     * @GRID\Column(title="Width", title="Width", type="number", operatorsVisible=false, operators="eq", defaultOperator="eq")
     */
    protected $width;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 2
     * )
     *
     * @ORM\Column(type="integer")
     *
     * @GRID\Column(title="Height", title="Width", type="number", operatorsVisible=false, operators="eq", defaultOperator="eq")
     */
    protected $height;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     *
     * @GRID\Column(title="Is Active", type="boolean", operatorsVisible=false)
     */
    protected $isActive;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="offerBanners")
     *
     * @GRID\Column(field="offer.name", title="Offer", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $offer;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="offerBanners")
     *
     * @GRID\Column(field="brand.name", title="Brand", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $brand;

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
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     * @return Brand
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     * @return Brand
     */
    public function setHeight($height)
    {
        $this->height = $height;

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
     * @return \AppBundle\Entity\User
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set offerCategory
     *
     * @param \AppBundle\Entity\Offer $offer
     * @return Offer
     */
    public function setOffer(\AppBundle\Entity\Offer $offer = null)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offerCategory
     *
     * @return \AppBundle\Entity\Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
