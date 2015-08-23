<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer_banner")
 * @UniqueEntity(fields={"file"})
 * @GRID\Source(columns="id, file, width, height")
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
     * @Assert\Image(minWidth=2, minHeight=2, maxSize = "10240k", mimeTypes={ "image/jpeg", "image/png", "image/gif", "application/x-shockwave-flash" })
     *
     * @ORM\Column(type="string", length=255)
     *
     * @GRID\Column(title="Name", type="text", operatorsVisible=false)
     */
    protected $file;

    /**
     * @Assert\NotBlank(groups={"postUpload"})
     * @Assert\Range(
     *      min = 2,
     *      groups={"postUpload"}
     * )
     *
     * @ORM\Column(type="integer")
     *
     * @GRID\Column(title="Width", title="Width", type="number", operatorsVisible=false, operators="eq", defaultOperator="eq")
     */
    protected $width;

    /**
     * @Assert\NotBlank(groups={"postUpload"})
     * @Assert\Range(
     *      min = 2,
     *      groups={"postUpload"}
     * )
     *
     * @ORM\Column(type="integer")
     *
     * @GRID\Column(title="Height", title="Width", type="number", operatorsVisible=false, operators="eq", defaultOperator="eq")
     */
    protected $height;

    /**
     * @Assert\NotBlank(groups={"postUpload"})
     *
     * @ORM\Column(type="boolean")
     *
     * @GRID\Column(title="Is Active", type="boolean", operatorsVisible=false)
     */
    protected $isActive;

    /**
     * @Assert\NotBlank(groups={"postUpload"})
     *
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="offerBanners")
     *
     * @GRID\Column(field="offer.name", title="Offer", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $offer;

    /**
     * @Assert\NotBlank(groups={"postUpload"})
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
     * Set file
     *
     * @param string $file
     * @return Brand
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
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
        return $this->getFile();
    }
}
