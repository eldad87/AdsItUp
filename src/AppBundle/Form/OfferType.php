<?php
namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text')
            ->add('description', 'textarea')
            ->add('destination', 'url')
            ->add('offerCategory', 'entity', array(
                'class' => 'AppBundle:OfferCategory',
                'label' => 'Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->andWhere('o.isActive = true');
                },
            ))

            ->add('isActive', 'checkbox');
    }

    public function getName()
    {
        return 'offer';
    }
}