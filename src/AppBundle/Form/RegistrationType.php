<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('lastName', 'text')
            ->add('phone', 'text')
            ->add('country', 'country')
            ->add('skype', 'text', array('required'=>false))
            ->add('icq', 'text', array('required'=>false))
            ->add('company', 'text', array('required'=>false))
            ->add('website', 'text', array('required'=>false))
            ->add('comment', 'textarea', array('required'=>false));
        $builder->remove('username');
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}