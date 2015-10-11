<?php
namespace AppBundle\Services\Platform\Spot\Pixel;

use AppBundle\Entity\CommissionPlan;
use AppBundle\Services\Platform\CommissionPlan\CriteriaTypeAbstract;
use AppBundle\Services\Platform\Pixel\PixelSetting;
use AppBundle\Services\Platform\Pixel\PixelSettingTypeAbstract;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PixelSettingType extends PixelSettingTypeAbstract {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destinationType', 'choice', array(
                'choices' =>
                    (
                        $this->pixelEvent == PixelSetting::EVENT_GAME
                        ? array(PixelSetting::DESTINATION_TYPE_SERVER => 'server')
                        : array(PixelSetting::DESTINATION_TYPE_CLIENT => 'Client', PixelSetting::DESTINATION_TYPE_SERVER => 'server' )
                    ),
                'multiple' => false,
            ))
            ->add('fireCondition', 'choice', array(
                'choices' => array(
                    PixelSetting::FIRE_CONDITION_ANY => 'Any',
                    PixelSetting::FIRE_CONDITION_MATCH_A_COMMISSION_PLAN => 'Match a Commission Plan',
                    PixelSetting::FIRE_CONDITION_ON_QUALIFICATION => 'On Qualification'
                ),
                'multiple' => false,
            ))
            ->add('url', 'integer')
        ;


        /**
         * Set GET action for Client-Pixel
         */
        $formModifier = function (FormInterface $form, $destinationType = null) {
            switch($destinationType) {
                case PixelSetting::DESTINATION_TYPE_CLIENT:
                    $form->add('action', 'choice', array(
                        'choices' => array(
                            PixelSetting::ACTION_GET    => 'GET',
                        ),
                        'multiple' => false,
                    ));
                    break;
                case PixelSetting::DESTINATION_TYPE_SERVER:
                default:
                    $form->add('action', 'choice', array(
                        'choices' => array(
                            PixelSetting::ACTION_GET    => 'GET',
                            PixelSetting::ACTION_POST   => 'POST',
                        ),
                        'multiple' => false,
                    ));
                    break;
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), ($data ? $data->getDestinationType() : null));
            }
        );

        $builder->get('destinationType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $destinationType = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $destinationType);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Services\Platform\Pixel\PixelSetting',
        ));
    }

    public function getName()
    {
        return 'spot_pixel_setting';
    }
}