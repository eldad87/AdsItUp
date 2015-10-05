<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Brand;
use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
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
        switch($pixelType) {
            case 'Lead':
                $pixelType = PlatformAbstract::PIXEL_TYPE_LEAD;
                break;
            case 'Customer':
                $pixelType = PlatformAbstract::PIXEL_TYPE_CUSTOMER;
                break;
            case 'Deposit':
                $pixelType = PlatformAbstract::PIXEL_TYPE_DEPOSIT;
                break;
            case 'Game':
                $pixelType = PlatformAbstract::PIXEL_TYPE_GAME;
                break;
        }

        /** @var PlatformFactory $platformFactory */
        $platformFactory = $this->container->get('PlatformFactory');
        /** @var PlatformAbstract $platform */
        $platform = $platformFactory->create();

        /** @var BrandRecord $brandRecord */
        $brandRecord = $platform->getBrandRecord($id, $pixelType, $request);

        /**
         * Check if need to grant commission
         *  Issue client pixel
         */
    }
}