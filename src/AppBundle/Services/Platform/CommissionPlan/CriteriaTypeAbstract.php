<?php
namespace AppBundle\Services\Platform\CommissionPlan;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class CriteriaTypeAbstract extends AbstractType {
    /** @var FormBuilderInterface */
    protected $parentBuilder;

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function setParentBuilder(FormBuilderInterface $builder)
    {
        $this->parentBuilder = $builder;
        return $this;
    }
}