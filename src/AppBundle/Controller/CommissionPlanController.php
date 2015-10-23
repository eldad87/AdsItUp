<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Brand;
use AppBundle\Entity\CommissionPlan;
use AppBundle\Form\CommissionPlanType;
use AppBundle\Services\Platform\PlatformAbstract;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Grid;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Source\Entity;

/**
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 * @Breadcrumb("Commission", route={"name"="dashboard.commission_plan"})
 */
class CommissionPlanController extends AbstractController
{
	/**
	 * List all Commission Plans
	 *
	 * @Route("/Dashboard/CommissionPlan", name="dashboard.commission_plan")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_BRAND')")
	 */
	public function listAction(Request $request)
	{
		$source = new Entity('AppBundle:CommissionPlan');
		/** @var Brand $brand */
		$brand = $this->get('Brand')->byHost();
		$grid = $this->get('grid');


		/** @var Grid $grid */
		$grid->setSource($source);
		$grid->setNoDataMessage(false);

		//Set permissions
		$qb = $source->getRepository()->createQueryBuilder($source->getTableAlias());
		$this->applyPermission($qb);
		$source->initQueryBuilder($qb);

		// Edit
		$rowAction = new RowAction('Edit', 'dashboard.commission_plan.save', false, '_self', array(), array('ROLE_BRAND'));
		$rowAction->setRouteParameters(array('id'));
		$grid->addRowAction($rowAction);

		// Add
		$massAction = new MassAction('Add', function() {
			return new RedirectResponse($this->generateUrl('dashboard.commission_plan.save'));
		}, false, array(), array('ROLE_BRAND'));
		$grid->addMassAction($massAction);


		$grid->isReadyForRedirect();

		return $grid->getGridResponse('::listGrid.html.twig');
	}

	/**
	 * Add/Edit an Commission Plan
	 *
	 * @Breadcrumb("Save")
	 * @Breadcrumb("{commissionPlan}")
	 * @Route("/Dashboard/CommissionPlan/Save/{id}", defaults={"id" = false}, requirements={"id": "\d+"},
	 *          name="dashboard.commission_plan.save")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_BRAND')")
	 */
	public function saveAction(Request $request, CommissionPlan $commissionPlan=null)
	{
		$em = $this->getDoctrine()->getManager();
		if($commissionPlan) {
			$this->checkAccess($commissionPlan);
		} else {
			$commissionPlan = new CommissionPlan();
		}

		/** @var PlatformAbstract $platform */
		$platform = $this->get('PlatformFactory')->create();

		$form = $this->createForm(new CommissionPlanType($platform->getCommissionPlanCriteriaType()), $commissionPlan);
		$form->handleRequest($request);
		//Ignore Ajax because it used to modify the form according to the commission strategy
		if ($form->isValid() && $request->isMethod($request::METHOD_POST) && !$request->isXmlHttpRequest()) {
			$commissionPlan = $form->getData();
			$commissionPlan->setBrand($this->get('Brand')->byHost());

			$em->persist($commissionPlan);
			$em->flush();

			return $this->redirectToRoute('dashboard.commission_plan');
		}

		return $this->render('AppBundle:CommissionPlan:save.html.twig', array(
			'form' => $form->createView(),
		));

	}
}