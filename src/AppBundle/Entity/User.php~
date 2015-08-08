<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User AS BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="users")
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
     * Set brand
     *
     * @param \AppBundle\Entity\User $brand
     * @return User
     */
    public function setBrand(\AppBundle\Entity\User $brand = null)
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
}
