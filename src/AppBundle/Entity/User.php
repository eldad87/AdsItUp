<?php

namespace AppBundle\Entity;

use AppBundle\Services\Platform\Pixel\PixelSetting;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User AS BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user", uniqueConstraints={@ORM\UniqueConstraint(name="brand_email", columns={"brand_id", "email"})})
 * @GRID\Source(columns="id, enabled, balance, email, firstName, lastName, phone, country, skype, icq, company, website, manager.firstName, manager.lastName")
 * @UniqueEntity(
 *     fields={"brand", "email"},
 *     errorPath="email",
 *     message="This email is already in use.",
 *     groups={"CustomRegistration", "CustomProfile"}
 * )
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="usernameCanonical", column=@ORM\Column(type="string", length=255, unique=false, nullable=false)),
 *      @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(type="string", length=255, unique=false, nullable=false))
 * })
 */
class User extends BaseUser {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(title="Id", type="number", operatorsVisible=false)
     */
    protected $id;

    /**
     * @var boolean
     * @GRID\Column(title="Enable", type="boolean", operatorsVisible=false)
     */
    protected $enabled;

    /**
     * @var float
     * @ORM\Column(type="decimal", nullable=false, precision=5, scale=2, options={"default" = 0})
     * @GRID\Column(title="Balance", type="number", operatorsVisible=false)
     */
    protected $balance;

    /**
     * @var string
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(min="2", max="254", groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Email(groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="Email", type="text", operatorsVisible=false)
     */
    protected $email;

    /**
     * @ORM\ManyToMany(targetEntity="CommissionPlan", inversedBy="users")
     */
    protected $commissionPlans;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="users")
     */
    protected $brand;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subordinates")
     * @GRID\Column(field="manager.firstName", title="Manager First Name", operatorsVisible=false, filter="select", selectFrom="query")
     * @GRID\Column(field="manager.lastName", title="Manager Last Name", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $manager;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subordinates")
     * @GRID\Column(field="referrer.firstName", title="Referrer First Name", operatorsVisible=false, filter="select", selectFrom="query")
     * @GRID\Column(field="referrer.lastName", title="Referrer Last Name", operatorsVisible=false, filter="select", selectFrom="query")
     */
    protected $referrer;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="manager")
     */
    protected $subordinates;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="First Name", type="text", operatorsVisible=false)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="Last Name", type="text", operatorsVisible=false)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(max="20", min="10", groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="Phone", type="text", operatorsVisible=false)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=2, options={"fixed" = true}, nullable=true)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="Country", type="text", operatorsVisible=false)
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="Skype", type="text", operatorsVisible=false)
     */
    protected $skype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="ICQ", type="text", operatorsVisible=false)
     */
    protected $icq;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="Company", type="text", operatorsVisible=false)
     */
    protected $company;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     * @GRID\Column(title="Website", type="text", operatorsVisible=false)
     */
    protected $website;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     *
     * @Assert\Length(max="500", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $comment;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="object", nullable=true)
     * @var PixelSetting
     */
    protected $leadPixel;
    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="object", nullable=true)
     * @var PixelSetting
     */
    protected $customerPixel;
    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="object", nullable=true)
     * @var PixelSetting
     */
    protected $depositPixel;
    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="object", nullable=true)
     * @var PixelSetting
     */
    protected $gamePixel;

    /**
     * @ORM\OneToMany(targetEntity="BrandRecord", mappedBy="offerClick")
     */
    protected $brandRecords;

    /**
     * @ORM\OneToMany(targetEntity="PixelLog", mappedBy="user")
     */
    protected $pixelLog;

    public function __construct()
    {
        parent::__construct();
        $this->users = new ArrayCollection();
        $this->subordinates = new ArrayCollection();
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
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     * @return $this;
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @param float $balance
     * @return $this;
     */
    public function incBalance($balance)
    {
        $this->balance += $balance;
        return $this;
    }

    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param mixed $skype
     * @return $this
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIcq()
    {
        return $this->icq;
    }

    /**
     * @param mixed $icq
     * @return $this
     */
    public function setIcq($icq)
    {
        $this->icq = $icq;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     * @return $this
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return PixelSetting
     */
    public function getLeadPixel()
    {
        return $this->leadPixel;
    }

    /**
     * @param PixelSetting $leadPixel
     * @return $this
     */
    public function setLeadPixel(PixelSetting $leadPixel=null)
    {
        $this->leadPixel = $leadPixel;
        return $this;
    }

    /**
     * @return PixelSetting
     */
    public function getCustomerPixel()
    {
        return $this->customerPixel;
    }

    /**
     * @param PixelSetting $customerPixel
     * @return $this
     */
    public function setCustomerPixel(PixelSetting $customerPixel=null)
    {
        $this->customerPixel = $customerPixel;
        return $this;
    }

    /**
     * @return PixelSetting
     */
    public function getDepositPixel()
    {
        return $this->depositPixel;
    }

    /**
     * @param PixelSetting $depositPixel
     * @return $this
     */
    public function setDepositPixel(PixelSetting $depositPixel=null)
    {
        $this->depositPixel = $depositPixel;
        return $this;
    }

    /**
     * @return PixelSetting
     */
    public function getGamePixel()
    {
        return $this->gamePixel;
    }

    /**
     * @param PixelSetting $gamePixel
     * @return $this
     */
    public function setGamePixel(PixelSetting $gamePixel=null)
    {
        $this->gamePixel = $gamePixel;
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
     * Set manager
     *
     * @param \AppBundle\Entity\User $manager
     * @return $this
     */
    public function setManager(\AppBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Get manager
     *
     * @return \AppBundle\Entity\User
     */
    public function getManager()
    {
        return $this->manager;
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
     * Get referrer
     *
     * @return \AppBundle\Entity\User
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * Add subordinates
     *
     * @param \AppBundle\Entity\User $subordinate
     * @return Brand
     */
    public function addSubordinate(\AppBundle\Entity\User $subordinate)
    {
        $this->subordinates[] = $subordinate;

        return $this;
    }

    /**
     * Remove subordinates
     *
     * @param \AppBundle\Entity\User $subordinate
     */
    public function removeSubordinate(\AppBundle\Entity\User $subordinate)
    {
        $this->subordinates->removeElement($subordinate);
    }

    /**
     * Get subordinates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubordinates()
    {
        return $this->subordinates;
    }

    /**
     * Add commissionPlans
     *
     * @param \AppBundle\Entity\User $commissionPlan
     * @return Brand
     */
    public function addCommissionPlan(\AppBundle\Entity\User $commissionPlan)
    {
        $this->commissionPlans[] = $commissionPlan;

        return $this;
    }

    /**
     * Remove commissionplans
     *
     * @param \AppBundle\Entity\User $commissionPlan
     * @return $this
     */
    public function removeCommissionPlan(\AppBundle\Entity\User $commissionPlan)
    {
        $this->commissionPlans->removeElement($commissionPlan);
        return $this;
    }

    /**
     * Get commissionplans
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommissionPlans()
    {
        return $this->commissionPlans;
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
}
