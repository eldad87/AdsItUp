<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Form\OfferBannerType;
use AppBundle\Form\OfferType;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Grid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Source\Entity;

/**
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 * @Breadcrumb("Offers", route={"name"="dashboard.offer"})
 */
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

        // Edit
        $rowAction = new RowAction('Edit', 'dashboard.offer.save', false, '_self', array(), array('ROLE_BRAND'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // View
        $rowAction = new RowAction('View', 'dashboard.offer.view', false, '_self', array(), array('ROLE_AFFILIATE'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // Add
        $massAction = new MassAction('Add', function() {
            return new RedirectResponse($this->generateUrl('dashboard.offer.save'));
        }, false, array(), array('ROLE_BRAND'));
        $grid->addMassAction($massAction);


        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig');
    }

    /**
     * View an Offer
     *
     * @Breadcrumb("{offer}")
     * @Route("/Dashboard/Offer/{id}", requirements={"id": "\d+"},
     *          name="dashboard.offer.view")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function viewAction(Request $request, Offer $offer)
    {
        if($offer->getBrand()->getId() != $this->get('Brand')->byHost()->getId()) {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }

        $form = null;
        if($this->isGranted('ROLE_BRAND')) {
            $offerBanner = new OfferBanner();
            $form = $this->createForm(new OfferBannerType(), $offerBanner);

            $form->handleRequest($request);
            if ($form->isValid() && $request->isMethod($request::METHOD_POST)) {

                /** @var OfferBanner $offerBanner */
                $offerBanner = $form->getData();
                $offerBanner->setIsActive(true);
                $offerBanner->setBrand($this->get('Brand')->byHost());
                $offerBanner->setOffer($offer);

                /** @var UploadedFile $file */
                $file = $offerBanner->getFile();

                //Move file
                $absPath = $this->get('kernel')->getRootDir()
                    . '/../web/upload/brand/'
                    . $offerBanner->getBrand()->getId()
                    . '/offer/'
                    . $offerBanner->getOffer()->getId()
                    . '/';
                $fileName = time() . '_' . str_ireplace(' ', '_', $file->getClientOriginalName());
                $file->move($absPath, $fileName);
                $offerBanner->setFile($fileName);

                //Set resolution
                $size = @getimagesize($absPath . $fileName);
                $offerBanner->setWidth($size[0]);
                $offerBanner->setHeight($size[1]);

                $em = $this->getDoctrine()->getManager();
                $em->persist($offerBanner);
                $em->flush();
            }
        }

        return $this->render('AppBundle:Dashboard:offer.view.html.twig', array(
                'offer' => $offer,
                'form'  => $form ? $form->createView() : null
            )
        );
    }

    /**
     * Add/Edit an Offer
     *
     * @Breadcrumb("Save")
     * @Breadcrumb("{offer}")
     * @Route("/Dashboard/Offer/Save/{id}", defaults={"id" = false}, requirements={"id": "\d+"},
     *          name="dashboard.offer.save")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_BRAND')")
     */
    public function saveAction(Request $request, Offer $offer=null)
    {
        $em = $this->getDoctrine()->getManager();

        if($offer) {
            if($offer->getBrand()->getId() != $this->get('Brand')->byHost()->getId()) {
                throw $this->createAccessDeniedException('Unable to access this page!');
            }
        } else {
            $offer = new Offer();
        }

        $form = $this->createForm(new OfferType(), $offer);
        $form->handleRequest($request);
        if ($form->isValid() && $request->isMethod($request::METHOD_POST)) {
            $offer = $form->getData();
            $offer->setBrand($this->get('Brand')->byHost());

            $em->persist($offer);
            $em->flush();

            return $this->redirectToRoute('dashboard.offer');
        }

        return $this->render('::save.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
