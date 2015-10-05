<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Brand;
use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\PixelLog;
use AppBundle\Form\OfferBannerType;
use AppBundle\Form\OfferType;
use AppBundle\Services\Platform\PlatformAbstract;
use AppBundle\Services\Platform\PlatformFactory;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Grid;
use Doctrine\ORM\QueryBuilder;
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
 *
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 * @Breadcrumb("Pixels", route={"name"="dashboard.pixel"})
 */
class PixelController extends Controller
{
    /**
     * List all pixels
     *
     * @Route("/Dashboard/Pixel", name="dashboard.pixel")
     * @Method({"GET"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function listAction(Request $request)
    {

    }

    /**
     * View a pixel
     *
     * @Breadcrumb("{offer}")
     * @Route("/Dashboard/Offer/{id}", requirements={"id": "\d+"},
     *          name="dashboard.pixel.view")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function viewAction(Request $request, Offer $offer)
    {
    }

    /**
     * Handle client lead
     *
     * @Route("/Pixel/Client/{type}/{id}", host="{host}",
     *          requirements={"host": ".+", "id": "\d+", "pixelType":"Lead|Customer|Deposit|Game"},
     *          name="pixel.client.lead"),
     * @Method({"GET"})
     */
    public function handleClientPixelAction(Request $request, $pixelType, $id)
    {
        /** @var PlatformFactory $platformFactory */
        $platformFactory = $this->container->get('PlatformFactory');
        /** @var PlatformAbstract $platform */
        $platform = $platformFactory->create();

        $record = $platform->getRecordByPixel($id, $pixelType);

        //Verify that commission plan is not yet set
        /** @var BrandRecord $brandRecord */
        $brandRecord = $platform->getBrandRecord($record, $request);
        if($brandRecord->getCommissionPlan()) {
            $this->redirect($request->getSchemeAndHttpHost() . '/img/pixel.gif');
        }

        //Handle commission
        $commission = $platform->handleCommission($brandRecord);
        if(!$commission) {
            $this->redirect($request->getSchemeAndHttpHost() . '/img/pixel.gif');
        }

        if(PixelLog::TYPE_SERVER== $brandRecord->getUser()->getPixelType()) {
            $brandRecord->setIsServerPixelPending(true);
            $this->getDoctrine()->getManager()->persist($brandRecord);
            $this->getDoctrine()->getManager()->flush();
            $this->redirect($request->getSchemeAndHttpHost() . '/img/pixel.gif');
        }

        //Redirect
        $url = $platform->getClientPixelURL($brandRecord);
        if(!$url) {
            $this->redirect($request->getSchemeAndHttpHost() . '/img/pixel.gif');
        }

        //Save pixel log
        $pixelLog = new PixelLog();
        $pixelLog->setAction(PixelLog::ACTION_GET);
        $pixelLog->setType(PixelLog::TYPE_CLIENT);
        $pixelLog->setUrl($url);
        $pixelLog->setIsSuccess(true);
        $pixelLog->setBrand($brandRecord->getBrand());
        $pixelLog->setUser($brandRecord->getUser());
        $pixelLog->setOffer($brandRecord->getOffer());
        $pixelLog->setBrandRecord($brandRecord);
        $this->getDoctrine()->getManager()->persist($pixelLog);
        $this->getDoctrine()->getManager()->flush();

        $this->redirect($url);
    }
}