<?php
namespace AppBundle\Form;

use AppBundle\Entity\Brand;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PaymentLogType extends AbstractType
{
    /** @var \Closure */
    protected $permissionClosure;

    public function __construct(\Closure $permissionClosure)
    {
        $this->permissionClosure = $permissionClosure;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('amount', 'money', array('currency'=>'USD'))
        ->add('comment', 'text')
        ->add('user', 'entity', array(
            'class' => 'AppBundle:User',
            'label' => 'User',
            'multiple' => false,
            'query_builder' => function (EntityRepository $er) {
                $qb = $er->createQueryBuilder('u');
                $f = $this->permissionClosure;
                $f($qb);
                return $qb;

            },
        ));
    }

    public function getName()
    {
        return 'app_payment_log';
    }
}