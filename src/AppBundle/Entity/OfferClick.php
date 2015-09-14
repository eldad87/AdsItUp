<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer_click")
 * @GRID\Source(columns="id, file, width, height")
 */
class OfferClick {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(title="Id", type="number", operatorsVisible=false)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @GRID\Column(title="User Agent", type="text", operatorsVisible=false)
     */
    protected $ua;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @GRID\Column(title="User Agent Version", type="text", operatorsVisible=false)
     */
    protected $uaVersion;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @GRID\Column(title="OS", type="text", operatorsVisible=false)
     */
    protected $os;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @GRID\Column(title="OS", type="text", operatorsVisible=false)
     */
    protected $osVersion;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @GRID\Column(title="Browser", type="text", operatorsVisible=false)
     */
    protected $device;

    /**
     * @ORM\Column(type="string", length=500)
     *
     * @GRID\Column(title="User Agent Raw", type="text", operatorsVisible=false)
     */
    protected $uaRaw;
    /**
     * @ORM\Column(type="string", length=15)
     *
     * @GRID\Column(title="IP", type="text", operatorsVisible=false)
     */
    protected $ip;

    /**
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="offerClicks")
     *
     * @GRID\Column(field="offer.name", title="Offer", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $offer;

    /**
     * @ORM\ManyToOne(targetEntity="OfferBanner", inversedBy="bannerClicks")
     *
     * @GRID\Column(field="OfferBanner.width", title="BannerW", operatorsVisible=false, filter="select", selectFrom="query")
     * @GRID\Column(field="OfferBanner.height", title="BannerH", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $offerBanner;

    /**
     * @Assert\NotBlank(groups={"postUpload"})
     *
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="offerClicks")
     *
     * @GRID\Column(field="brand.name", title="Brand", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $brand;

    /**
     * @return mixed
     */
    public function getUa()
    {
        return $this->ua;
    }

    /**
     * @param mixed $ua
     * @return $this
     */
    public function setUa($ua)
    {
        $this->ua = $ua;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUaVersion()
    {
        return $this->uaVersion;
    }

    /**
     * @param mixed $uaVersion
     * @return $this
     */
    public function setUaVersion($uaVersion)
    {
        $this->uaVersion = $uaVersion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param mixed $os
     * @return $this
     */
    public function setOs($os)
    {
        $this->os = $os;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOsVersion()
    {
        return $this->osVersion;
    }

    /**
     * @param mixed $osVersion
     * @return $this
     */
    public function setOsVersion($osVersion)
    {
        $this->osVersion = $osVersion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     * @return $this
     */
    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUaRaw()
    {
        return $this->uaRaw;
    }

    /**
     * @param mixed $uaRaw
     * @return $this
     */
    public function setUaRaw($uaRaw)
    {
        $this->uaRaw = $uaRaw;
        return $this;
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
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
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
     * Set offer banner
     *
     * @param \AppBundle\Entity\OfferBanner $offerBanner
     * @return User
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
        return 'click';
    }
}
