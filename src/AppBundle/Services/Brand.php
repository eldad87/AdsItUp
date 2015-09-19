<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
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

    /**
     * Find Brand by host
     * @param null $host
     * @return Brand
     */
    public function byHost($host=null)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine')->getManager();
        /** @var Query $qb */
        $qb = $em->createQueryBuilder()
                ->select('brand,platform')
                ->from('AppBundle:Brand', 'brand')
                    ->innerJoin('brand.platform', 'platform')
                ->where('brand.host = :host')
                    ->setParameter('host', ($host ? $host : $this->request->getHost()))
            ->getQuery();
        return $qb->getSingleResult();
    }
}