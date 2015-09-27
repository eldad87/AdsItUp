<?php

namespace AppBundle\UserBundle\Doctrine;

use AppBundle\Entity\Brand;
use AppBundle\Entity\User;
use AppBundle\Services\Brand AS BrandService;

use Doctrine\Common\Persistence\ObjectRepository;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FOS\UserBundle\Util\CanonicalizerInterface;
use FOS\UserBundle\Model\UserInterface;


class UserManager extends BaseUserManager
{
    /** @var Brand */
    protected $brand;
    /** @var BrandService */
    protected $brandService;

    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer,
                                CanonicalizerInterface $emailCanonicalizer, EntityManager $em, BrandService $brand, $class) {

        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $em, $class);
        $this->brandService = $brand;
    }

    public function setBrand(Brand $brand)
    {
        $this->brand = $brand;
    }

    private function getBrand()
    {
        if($this->brand) {
            return $this->brand;
        }

        return $this->brandService->byHost();
    }

    /**
     * Returns an empty user instance
     *
     * @return UserInterface
     */
    public function createUser()
    {
        /** @var User $user */
        $user = parent::createUser();
        $user->addRole('ROLE_AFFILIATE');
        $user->setBrand($this->getBrand());
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        $criteria['brand'] = $this->getBrand();
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->repository->findBy(array('brand'=>$this->getBrand()));
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->objectManager;
    }
}
