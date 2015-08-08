<?php

namespace AppBundle\Controller;

use APY\DataGridBundle\Grid\Grid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Source\Entity;

class OfferController extends Controller
{
    /**
     * List all offers
     *
     * @Route("/Dashboard/Offer", name="dashboard.offer")
     * @Method({"GET", "POST"})
     */
    public function listAction(Request $request)
    {
        $source = new Entity('AppBundle:Offer');
        $grid = $this->get('grid');

        /** @var Grid $grid */
        $grid->setSource($source);
        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig');
    }

    /**
     * View an Offer
     *
     * @Route("/Dashboard/Offer/{id}", requirements={"id": "\d+"}, name="dashboard.offer.view")
     * @Method("GET")
     */
    public function viewAction(Request $request, $id)
    {

    }

    /**
     * Add/Edit an Offer
     *
     * @Route("/Dashboard/Offer/Save/{id}", defaults={"id" = false}, requirements={"id": "\d+"},
     *          name="dashboard.offer.save")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_BRAND')")
     */
    public function saveAction(Request $request)
    {

    }
}
