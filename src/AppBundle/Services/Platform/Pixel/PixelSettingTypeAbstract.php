<?php
namespace AppBundle\Services\Platform\Pixel;

use AppBundle\Entity\CommissionPlan;
use AppBundle\Services\Platform\CommissionPlan\CriteriaTypeAbstract;
use AppBundle\Services\Platform\Pixel\PixelSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class PixelSettingTypeAbstract extends AbstractType {
    /** @var PixelSetting::EVENT_* */
    protected $pixelEvent;

    /**
     * @param PixelSetting::EVENT_* $pixelEvent
     * @return $this
     */
    public function setPixelEvent($pixelEvent)
    {
        $this->pixelEvent = $pixelEvent;
        return $this;
    }
}