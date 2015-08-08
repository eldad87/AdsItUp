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
     *
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
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     * @return Brand
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
     * @return Brand
     */
    public function setDescription($description)
    {
        $this->description = $description;

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

    public function __toString()
    {
        return $this->getName();
    }
}
