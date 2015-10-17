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
 * @Breadcrumb("Record", route={"name"="dashboard.record"})
 */
class BrandRecordController extends AbstractController
{
    /**
     * List all pixels
     *
     * @Route("/Dashboard/Record", name="dashboard.record")
     * @Method({"GET"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function listAction(Request $request)
    {
        $source = new Entity('AppBundle:BrandRecord');
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

        // View
        /*$rowAction = new RowAction('View', 'dashboard.pixel.view', false, '_self', array(), array('ROLE_AFFILIATE'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);*/


        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig');
    }

    /**
     * View a pixel
     *
     * @Breadcrumb("{offer}")
     * @Route("/Dashboard/Pixel/{id}", requirements={"id": "\d+"},
     *          name="dashboard.pixel.view")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function viewAction(Request $request, Offer $offer)
    {
        //Todo
    }
}