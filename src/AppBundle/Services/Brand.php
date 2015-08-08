<?php
namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


class Brand extends ContainerAware
{

    /** @var Request  */
    protected $request;

    public function setRequest(RequestStack $request_stack)
    {
        $this->request = $request_stack->getCurrentRequest();
    }

    public function byHost()
    {
        $em = $this->container->get('doctrine')->getManager();
        return $em->getRepository('AppBundle:Brand')->findOneBy(array(
            'host' => $this->request->getHost()
        ));
    }
}