<?php
namespace AppBundle\Services\Platform\Spot\CommissionPlan;

use AppBundle\Entity\CommissionPlan;
use AppBundle\Services\Platform\CommissionPlan\CriteriaTypeAbstract;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CriteriaType extends CriteriaTypeAbstract {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'choice', array(
                'choices' => array(
                    'US' => 'US',
                    'CA' => 'CA',
                    'UK' => 'UK',
                    'SA' => 'SA',
                ),
                'multiple' => true,
            ))
            ->add('siteLanguage', 'choice', array(
                'choices' => array(
                    'US' => 'US',
                    'CA' => 'CA',
                    'UK' => 'UK',
                    'SA' => 'SA',
                ),
                'multiple' => true,
            ))
            ->add('siteLanguageSelected', 'choice', array(
                'choices' => array(
                    'EN' => 'EN',
                    'AR' => 'AR',
                ),
                'multiple' => true,
            ))
            ->add('saleStatus', 'choice', array(
                'choices' => array(
                    'noAnswer' => 'noAnswer',
                    'checkNumber' => 'checkNumber',
                    'callAgain' => 'callAgain',
                    'notIntrested' => 'notIntrested',
                    'notInterested' => 'notInterested',
                    'inTheMoney' => 'inTheMoney',
                    'noCall' => 'noCall',
                ),
                'multiple' => true,
            ))
            ->add('leadStatus', 'choice', array(
                'choices' => array(
                    'noAnswer' => 'noAnswer',
                    'checkNumber' => 'checkNumber',
                    'callAgain' => 'callAgain',
                    'notIntrested' => 'notIntrested',
                    'notInterested' => 'notInterested',
                    'inTheMoney' => 'inTheMoney',
                    'noCall' => 'noCall',
                ),
                'multiple' => true,
            ))
            ->add('minDepositAmount', 'money')
            ->add('totalPositionCount', 'integer')
        ;

        $formModifier = function(FormInterface $form, $strategy = null) {
            switch($strategy) {
                case CommissionPlan::TYPE_CPC:
                    $form->remove('country');
                    $form->remove('siteLanguage');
                    $form->remove('siteLanguageSelected');
                    $form->remove('saleStatus');
                    $form->remove('leadStatus');
                    $form->remove('minDepositAmount');
                    $form->remove('totalPositionCount');
                    break;
                case CommissionPlan::TYPE_CPL:
                    $form
                        ->add('country', 'choice', array(
                            'choices' => array(
                                'US' => 'US',
                                'CA' => 'CA',
                                'UK' => 'UK',
                                'SA' => 'SA',
                            ),
                            'multiple' => true,
                        ))
                        ->add('siteLanguage', 'choice', array(
                            'choices' => array(
                                'US' => 'US',
                                'CA' => 'CA',
                                'UK' => 'UK',
                                'SA' => 'SA',
                            ),
                            'multiple' => true,
                        ))
                        ->add('siteLanguageSelected', 'choice', array(
                            'choices' => array(
                                'EN' => 'EN',
                                'AR' => 'AR',
                            ),
                            'multiple' => true,
                        ))
                        ->add('saleStatus', 'choice', array(
                            'choices' => array(
                                'noAnswer' => 'noAnswer',
                                'checkNumber' => 'checkNumber',
                                'callAgain' => 'callAgain',
                                'notIntrested' => 'notIntrested',
                                'notInterested' => 'notInterested',
                                'inTheMoney' => 'inTheMoney',
                                'noCall' => 'noCall',
                            ),
                            'multiple' => true,
                        ))
                        ->add('leadStatus', 'choice', array(
                            'choices' => array(
                                'noAnswer' => 'noAnswer',
                                'checkNumber' => 'checkNumber',
                                'callAgain' => 'callAgain',
                                'notIntrested' => 'notIntrested',
                                'notInterested' => 'notInterested',
                                'inTheMoney' => 'inTheMoney',
                                'noCall' => 'noCall',
                            ),
                            'multiple' => true,
                        ));
                    $form
                        ->remove('minDepositAmount')
                        ->remove('totalPositionCount');
                    break;
                case CommissionPlan::TYPE_CPA:
                    $form
                        ->add('minDepositAmount')
                        ->add('totalPositionCount');
                    break;
            }
        };

        $this->parentBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var CommissionPlan $data */
                $data = $event->getData();
                $formModifier($event->getForm()->get('criteria'), $data->getStrategy());
            }
        );

        $this->parentBuilder->get('strategy')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $strategy = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent()->get('criteria'), $strategy);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Services\Platform\Spot\CommissionPlan\Criteria',
        ));
    }

    public function getName()
    {
        return 'commission_plan_condition';
    }
}