<?php
namespace AppBundle\Form;

use AppBundle\Entity\CommissionPlan;
use AppBundle\Services\Platform\CommissionPlan\CriteriaTypeAbstract;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommissionPlanType extends AbstractType {

    /** @var CriteriaTypeAbstract */
    private $criteriaType;
    public function __construct(CriteriaTypeAbstract $criteriaType)
    {
        $this->criteriaType = $criteriaType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->criteriaType->setParentBuilder($builder);
        $builder
            ->add('isActive', 'checkbox')
            ->add('strategy', 'choice', array(
                'choices' => array(
                    //CommissionPlan::TYPE_CPC => 'CPC',
                    CommissionPlan::TYPE_CPL => 'CPL',
                    CommissionPlan::TYPE_CPA => 'CPA',
                ),
                'multiple' => false,
            ))
            ->add('priority', 'integer')
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('payout', 'money')
            ->add('criteria', $this->criteriaType);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CommissionPlan',
        ));
    }

    public function getName()
    {
        return 'commission_plan';
    }
}