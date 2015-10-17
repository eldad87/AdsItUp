<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Brand;
use AppBundle\Services\Platform\CommissionPlan\CriteriaAbstract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_log")
 * @GRID\Source(columns="id, amount, comment, user.username, createdAt")
 * @ORM\Table(indexes={@ORM\Index(name="created_at", columns={"brand_id", "created_at"}), @ORM\Index(name="created_a_user_idt", columns={"brand_id", "user_id", "created_at"})})
 * @ORM\HasLifecycleCallbacks()
 */
class PaymentLog {
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
	 * @Assert\Range(
	 *      min = 0
	 * )
	 *
	 * @ORM\Column(type="decimal")
	 *
	 * @GRID\Column(title="Amount", operatorsVisible=false, defaultOperator="btw")
	 */
	protected $amount;

	/**
	 * @Assert\NotBlank()
	 *
	 * @ORM\Column(type="text")
	 *
	 * @GRID\Column(title="Comment", type="text", operatorsVisible=false, role="ROLE_AFFILIATE_MANAGER")
	 */
	protected $comment;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="paymentLog")
	 *
	 * @GRID\Column(field="user.username", title="User", operatorsVisible=false, filter="select", selectFrom="query", role="ROLE_AFFILIATE_MANAGER")
	 */
	protected $user;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	protected $creator;

	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @GRID\Column(title="Created At", type="datetime", operatorsVisible=false, defaultOperator="btwe")
	 */
	protected $createdAt;

	/**
	 * @Assert\NotBlank()
	 *
	 * @ORM\ManyToOne(targetEntity="Brand", inversedBy="paymentLog")
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
	 * @return float
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param float $amount
	 * @return PaymentLog
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 * @return $this;
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
		return $this;
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

	public function __toString()
	{
		return (string) $this->getId();
	}

	/**
	 * Set user
	 *
	 * @param \AppBundle\Entity\User $user
	 * @return $this
	 */
	public function setUser(\AppBundle\Entity\User $user = null)
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * Get userCategory
	 *
	 * @return \AppBundle\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}


	/**
	 * Set creator
	 *
	 * @param \AppBundle\Entity\User $creator
	 * @return $this
	 */
	public function setCreator(\AppBundle\Entity\User $creator = null)
	{
		$this->creator = $creator;
		return $this;
	}

	/**
	 * Get creatorCategory
	 *
	 * @return \AppBundle\Entity\User
	 */
	public function getCreator()
	{
		return $this->creator;
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