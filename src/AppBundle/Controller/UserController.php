<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Brand;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\User;
use AppBundle\Form\OfferBannerType;
use AppBundle\Form\OfferType;
use AppBundle\Form\UserCommissionPlanType;
use AppBundle\Form\UserPixelSettingType;
use AppBundle\Services\Platform\PlatformAbstract;
use AppBundle\Services\Platform\Spot\Pixel\PixelSettingType;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Grid;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 * @Breadcrumb("Users", route={"name"="dashboard.user"})
 */
class UserController extends AbstractController
{
    /**
     * List all offers
     *
     * @Route("/Dashboard/User", name="dashboard.user")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE_MANAGER')")
     */
    public function listAction(Request $request)
    {
        $source = new Entity('AppBundle:User');
        $grid = $this->get('grid');

        /** @var Grid $grid */
        $grid->setSource($source);
        $grid->setNoDataMessage(false);

        //Set permissions
        $qb = $source->getRepository()->createQueryBuilder($source->getTableAlias());
        $this->applyPermission($qb);
        $source->initQueryBuilder($qb);

        // Commission Plan
        $rowAction = new RowAction('Commission Plan', 'dashboard.user.commission_plan.save', false, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // Pixel
        $rowAction = new RowAction('Pixel', 'dashboard.user.pixel.save', false, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // Enable/Disable
        $rowAction = new RowAction('Enable/Disable', 'dashboard.user.enable_disable', true, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // Assign/UnAssign
        $rowAction = new RowAction('Assign/UnAssign', 'dashboard.user.assign_unassign', true, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // PaymentLog
        $rowAction = new RowAction('Payments Log', 'dashboard.payment', false, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
        /*$rowAction->setRouteParametersMapping(array('id' => 'user'));*/

        $grid->addRowAction($rowAction);


        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig');
    }

    /**
     * Enable/Disable a User
     *
     * @Breadcrumb("Save")
     * @Breadcrumb("{user}")
     * @Route("/Dashboard/User/EnableDisable/{id}", requirements={"id": "\d+"},
     *          name="dashboard.user.enable_disable")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE_MANAGER')")
     */
    public function enableDisableAction(Request $request, User $user)
    {
        $this->checkAccess($user);

        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');
        $user->setEnabled(!$user->isEnabled());

        $userManager->updateUser($user);
        return $this->redirectToRoute('dashboard.user');
    }

    /**
     * Assign/Un Assign a User
     *
     * @Breadcrumb("Save")
     * @Breadcrumb("{user}")
     * @Route("/Dashboard/User/AssignUnAssign/{id}", requirements={"id": "\d+"},
     *          name="dashboard.user.assign_unassign")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE_MANAGER')")
     */
    public function assignUnAssignAction(Request $request, User $user)
    {
        $this->checkAccess($user);

        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');

        if($user->getManager()) {
            $user->setManager(null);
        } else {
            $user->setManager($this->container->get('security.context')->getToken()->getUser());
        }
        $userManager->updateUser($user);
        return $this->redirectToRoute('dashboard.user');
    }

    /**
     * Add/Edit a Commission Plan for a user
     *
     * @Breadcrumb("Save")
     * @Breadcrumb("{user}")
     * @Route("/Dashboard/User/CommissionPlan/{id}", requirements={"id": "\d+"},
     *          name="dashboard.user.commission_plan.save")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE_MANAGER')")
     */
    public function commissionPlanAction(Request $request, User $user)
    {
        $this->checkAccess($user);

        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');

        $form = $this->createForm(new UserCommissionPlanType($user->getBrand()), $user);
        $form->handleRequest($request);
        if ($form->isValid() && $request->isMethod($request::METHOD_POST)) {
            $user = $form->getData();

            $userManager->updateUser($user);

            return $this->redirectToRoute('dashboard.user');
        }

        return $this->render('::save.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Add/Edit an Commission Plan
     *
     * @Breadcrumb("Save")
     * @Breadcrumb("{user}")
     * @Route("/Dashboard/User/Pixel/{id}", requirements={"id": "\d+"},
     *          name="dashboard.user.pixel.save")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE_MANAGER')")
     */
    public function pixelAction(Request $request, User $user)
    {
        $this->checkAccess($user);

        $em = $this->getDoctrine()->getManager();

        /** @var PlatformAbstract $platform */
        $platform = $this->get('PlatformFactory')->create();

        $form = $this->createForm(new UserPixelSettingType($platform->getPixelType()), $user);
        $form->handleRequest($request);
        //Ignore Ajax because it used to modify the form according to the commission strategy
        if ($form->isValid() && $request->isMethod($request::METHOD_POST) && !$request->isXmlHttpRequest()) {
            $commissionPlan = $form->getData();
            $commissionPlan->setBrand($this->get('Brand')->byHost());

            $em->persist($commissionPlan);
            $em->flush();

            return $this->redirectToRoute('dashboard.user');
        }

        return $this->render('AppBundle:Pixel:save.html.twig', array(
            'form' => $form->createView(),
        ));

    }
}