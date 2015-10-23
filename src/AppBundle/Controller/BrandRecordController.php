<?php

namespace AppBundle\Controller;

use APY\DataGridBundle\Grid\Grid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 *
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 * @Breadcrumb("Record", route={"name"="dashboard.record"})
 */
class BrandRecordController extends AbstractController
{
    /**
     * List all pixels
     *
     * @Route("/Dashboard/Record", name="dashboard.record")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function listAction(Request $request)
    {
        $source = new Entity('AppBundle:BrandRecord');
        $grid = $this->get('grid');

        /** @var Grid $grid */
        $grid->setSource($source);
        $grid->setNoDataMessage(false);

        //Set permissions
        $qb = $source->getRepository()->createQueryBuilder($source->getTableAlias());
        $this->applyPermission($qb);
        $source->initQueryBuilder($qb);

        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig');
    }
}