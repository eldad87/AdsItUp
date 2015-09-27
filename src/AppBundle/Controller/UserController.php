<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Brand;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\User;
use AppBundle\Form\OfferBannerType;
use AppBundle\Form\OfferType;
use AppBundle\Form\UserCommissionPlanType;
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
class UserController extends Controller
{
    /**
     * List all offers
     *
     * @Route("/Dashboard/User", name="dashboard.user")
     * @Method({"GET"})
     * @Security("has_role('ROLE_AFFILIATE_MANAGER')")
     */
    public function listAction(Request $request)
    {
        $source = new Entity('AppBundle:User');

        /** @var User $user */
        $user = $this->getUser();
        /** @var Brand $brand */
        $brand = $this->get('Brand')->byHost();

        /** @var AuthorizationCheckerInterface $ac */
        $ac = $this->container->get('security.authorization_checker');
        $source->manipulateQuery(
            function (QueryBuilder $query) use ($source, $brand, $user, $ac)
            {
                $query->andWhere(sprintf('%s.brand = :brand', $source->getTableAlias()));
                if(!$ac->isGranted('ROLE_BRAND')) {
                    $query->andWhere(sprintf('(%s.manager = :user OR %s.manager IS NULL
                    OR %s.id = :user)', $source->getTableAlias(), $source->getTableAlias(), $source->getTableAlias()));
                    $query->setParameter('user', $user);
                }
                $query->setParameter('brand', $brand);

            }
        );
        $grid = $this->get('grid');


        /** @var Grid $grid */
        $grid->setSource($source);

        // Commission Plan
        $rowAction = new RowAction('Commission Plan', 'dashboard.user.commission_plan.save', false, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // Enable/Disable
        $rowAction = new RowAction('Enable/Disable', 'dashboard.user.enable_disable', false, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction);

        // Assign/UnAssign
        $rowAction = new RowAction('Assign/UnAssign', 'dashboard.user.assign_unassign', false, '_self', array(), array('ROLE_AFFILIATE_MANAGER'));
        $rowAction->setRouteParameters(array('id'));
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
        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');
        if($user->getBrand()->getId() != $this->get('Brand')->byHost()->getId()) {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }

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
        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');
        if($user->getBrand()->getId() != $this->get('Brand')->byHost()->getId()) {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }

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
        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');
        if($user->getBrand()->getId() != $this->get('Brand')->byHost()->getId()) {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }

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
}