<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Brand;
use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\PixelLog;
use AppBundle\Entity\User;
use AppBundle\Form\OfferBannerType;
use AppBundle\Form\OfferType;
use AppBundle\Services\Platform\Pixel\PixelSetting;
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
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 * @Breadcrumb("Pixels", route={"name"="dashboard.pixel"})
 */
class PixelController extends AbstractController
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
        $source = new Entity('AppBundle:PixelLog');
        /** @var Brand $brand */
        $brand = $this->get('Brand')->byHost();

        /** @var User $user */
        $user = $this->getUser();
        $source->manipulateQuery(
            function (QueryBuilder $query) use ($source, $brand, $user)
            {
                $this->applyPermission($query);
            }
        );
        $grid = $this->get('grid');


        /** @var Grid $grid */
        $grid->setSource($source);

        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig');
    }

    /**
     * Handle client lead
     *
     * @Route("/Pixel/{origin}/{event}/{id}", host="{host}",
     *          requirements={"host": ".+", "id": "\d+", "type":"Client|Server", "event":"Lead|Customer|Deposit|Game"},
     *          name="pixel.client.lead"),
     * @Method({"GET"})
     */
    public function handlePixelAction(Request $request, $origin, $event, $id)
    {
        /** @var PlatformFactory $platformFactory */
        $platformFactory = $this->container->get('PlatformFactory');
        /** @var PlatformAbstract $platform */
        $platform = $platformFactory->create();

        switch($origin) {
            case 'Client':
                $origin = PixelSetting::ORIGIN_TYPE_CLIENT;
                break;
            case 'Server':
                $origin = PixelSetting::ORIGIN_TYPE_SERVER;
                break;
            default:
                return new Response(406);
        }

        switch($event) {
            case 'Lead':
                $event = PixelSetting::EVENT_LEAD;
                break;
            case 'Customer':
                $event = PixelSetting::EVENT_CUSTOMER;
                break;
            case 'Deposit':
                $event = PixelSetting::EVENT_DEPOSIT;
                break;
            case 'Game':
                $event = PixelSetting::EVENT_GAME;
                break;
        }

        $record = $platform->getRecordByPixel($id, $event);
        if(!$record) {
            // BrandRecord not found
            return $platform->getPixelResponseBrandRecordNotFound($origin);
        }

        return $platform->handlePixelAction($origin, $record, $event);
    }
}