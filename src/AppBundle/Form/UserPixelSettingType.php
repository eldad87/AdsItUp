<?php
namespace AppBundle\Form;

use AppBundle\Entity\Brand;
use AppBundle\Entity\PixelLog;
use AppBundle\Services\Platform\Pixel\PixelSetting;
use AppBundle\Services\Platform\Pixel\PixelSettingTypeAbstract;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserPixelSettingType extends AbstractType
{

    /** @var PixelSettingTypeAbstract */
    private $pixelSettingType;
    public function __construct(PixelSettingTypeAbstract $pixelSettingType)
    {
        $this->pixelSettingType = $pixelSettingType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('leadPixel', (clone $this->pixelSettingType->setPixelEvent(PixelSetting::EVENT_LEAD)))
            ->add('customerPixel', (clone $this->pixelSettingType->setPixelEvent(PixelSetting::EVENT_CUSTOMER)))
            ->add('depositPixel', (clone $this->pixelSettingType->setPixelEvent(PixelSetting::EVENT_DEPOSIT)))
            ->add('gamePixel', (clone $this->pixelSettingType->setPixelEvent(PixelSetting::EVENT_GAME)))
        ;
    }

    public function getName()
    {
        return 'user_pixel_setting';
    }
}