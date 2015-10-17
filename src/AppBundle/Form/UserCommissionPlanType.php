<?php
namespace AppBundle\Form;

use AppBundle\Entity\Brand;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserCommissionPlanType extends AbstractType
{
    /** @var Brand */
    protected $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $brand = $this->brand;
        $builder->add('commissionPlans', 'entity', array(
            'class' => 'AppBundle:CommissionPlan',
            'label' => 'Commission Plan',
            'multiple' => true,
            'query_builder' => function (EntityRepository $er) use ($brand) {
                return $er->createQueryBuilder('o')
                    ->andWhere('o.isActive = true')
                    ->andWhere('o.brand = :brand')
                    ->setParameter('brand', $brand);
            },
        ));
    }

    public function getName()
    {
        return 'app_user_commission_plan';
    }
}