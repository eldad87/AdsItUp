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
 * @ORM\Table(name="commission_plan")
 * @GRID\Source(columns="id, isActive, strategy, priority, description, payout")
 * @ORM\HasLifecycleCallbacks()
 */
class CommissionPlan {

	//const TYPE_CPC = 1;
	const TYPE_CPL = 2;
	const TYPE_CPA = 3;

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
	 * @ORM\Column(type="boolean")
	 *
	 * @GRID\Column(title="Is Active", type="boolean", operatorsVisible=false)
	 */
	protected $isActive;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"1", "2", "3"})
	 *
	 * @ORM\Column(type="integer")
	 *
	 * @GRID\Column(title="Strategy", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="CPC","2"="CPL","3"="CPA"})
	 */
	protected $strategy;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Range(
	 *      min = 0,
	 *      max = 100
	 * )
	 *
	 * @ORM\Column(type="smallint", length=255)
	 *
	 * @GRID\Column(title="Priority", type="number", operatorsVisible=false, defaultOperator="btw")
	 */
	protected $priority;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 50
	 * )
	 *
	 * @ORM\Column(type="string", length=255)
	 *
	 * @GRID\Column(title="Description", type="text", operatorsVisible=false, defaultOperator="like")
	 */
	protected $description;

	/**
	 * @Assert\NotBlank()
	 *
	 * @ORM\Column(type="object")
	 * @var CriteriaAbstract
	 */
	protected $criteria;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Range(
	 *      min = 0
	 * )
	 *
	 * @ORM\Column(type="decimal")
	 *
	 * @GRID\Column(title="Payout", operatorsVisible=false, defaultOperator="btw")
	 */
	protected $payout;

	/**
	 * @ORM\ManyToOne(targetEntity="Brand")
	 *
	 * @GRID\Column(field="brand.name", title="Brand", operatorsVisible=false, filter="select", selectFrom="query")
	 */
	protected $brand;

	/**
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="commissionPlans")
	 */
	protected $users;

	/**
	 * @ORM\OneToMany(targetEntity="BrandRecord", mappedBy="commissionPlan")
	 */
	protected $brandRecords;

	public function __construct()
	{
		$this->users = new ArrayCollection();
		$this->brandRecords = new ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return $this
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
	 * @return int
	 */
	public function getStrategy()
	{
		return $this->strategy;
	}

	/**
	 * @param int $strategy
	 * @return $this;
	 */
	public function setStrategy($strategy)
	{
		$this->strategy = $strategy;
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
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return $this
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param int $priority
	 * @return $this
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
		return $this;
	}

	/**
	 * @return CriteriaAbstract
	 */
	public function getCriteria()
	{
		return $this->criteria;
	}

	/**
	 * @param CriteriaAbstract $criteria
	 * @return $this
	 */
	public function setCriteria(CriteriaAbstract $criteria)
	{
		$this->criteria = $criteria;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getPayout()
	{
		return $this->payout;
	}

	/**
	 * @param float $payout
	 * @return $this
	 */
	public function setPayout($payout)
	{
		$this->payout = $payout;
		return $this;
	}

	/**
	 * @return Brand
	 */
	public function getBrand()
	{
		return $this->brand;
	}

	/**
	 * @param Brand $brand
	 * @return $this
	 */
	public function setBrand($brand)
	{
		$this->brand = $brand;
		return $this;
	}

	public function __toString()
	{
		return sprintf('%d %s', $this->getPriority(), $this->getDescription());
	}

	/**
	 * Add users
	 *
	 * @param \AppBundle\Entity\User $user
	 * @return Brand
	 */
	public function addUser(\AppBundle\Entity\User $user)
	{
		$this->users[] = $user;

		return $this;
	}

	/**
	 * Remove users
	 *
	 * @param \AppBundle\Entity\User $user
	 * @return $this
	 */
	public function removeUser(\AppBundle\Entity\User $user)
	{
		$this->users->removeElement($user);
		return $this;
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
}