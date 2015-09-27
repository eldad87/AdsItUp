<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User AS BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @UniqueEntity(
 *     fields={"brand", "email"},
 *     errorPath="email",
 *     message="This email is already in use.",
 *     groups={"CustomRegistration", "CustomProfile"}
 * )
 */
class User extends BaseUser {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(min="2", max="254", groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Email(groups={"CustomRegistration", "CustomProfile"})
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="users")
     */
    protected $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     * @Assert\Length(max="20", min="10", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=2, options={"fixed" = true}, nullable=false)
     *
     * @Assert\NotBlank(groups={"CustomRegistration", "CustomProfile"})
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $skype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $icq;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $company;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $website;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     *
     * @Assert\Length(max="500", groups={"CustomRegistration", "CustomProfile"})
     */
    protected $comment;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
}
