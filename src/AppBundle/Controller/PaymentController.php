<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PaymentLog;
use AppBundle\Entity\User;
use AppBundle\Form\PaymentLogType;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Grid;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Source\Entity;

/**
 *
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 * @Breadcrumb("Payments", route={"name"="dashboard.payment"})
 */
class PaymentController extends AbstractController
{
    /**
     * List all payments
     *
     * @Route("/Dashboard/Payment/{id}", defaults={"id" = false}, requirements={"id": "\d+"}, name="dashboard.payment")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function listAction(Request $request, User $user=null)
    {
        /** @var User $user */
        $userViewer = $this->getUser();
        $this->checkAccess($user);

        $source = new Entity('AppBundle:PaymentLog');
        $source->manipulateQuery(
            function (QueryBuilder $query) use ($source, $userViewer, $user)
            {
                $this->applyPermission($query);
                if($user) {
                    $query->andWhere(sprintf('%s.user = :user', $source->getTableAlias()));
                    $query->setParameter('user', $user);
                }
            }
        );
        $grid = $this->get('grid');

        /** @var Grid $grid */
        $grid->setSource($source);
        $grid->setNoDataMessage(false);

        // Add
        $massAction = new MassAction('Add', function() use ($user) {
            return new RedirectResponse($this->generateUrl('dashboard.payment.add', array('id'=> ($user ? $user->getId() : null))));
        }, false, array(), array('ROLE_AFFILIATE_MANAGER'));
        $grid->addMassAction($massAction);

        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig');
    }

    /**
     * List all payments
     *
     * @Route("/Dashboard/Payment/Add/{id}", defaults={"id" = false}, requirements={"id": "\d+"},
     *          name="dashboard.payment.add")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE_MANAGER')")
     */
    public function addAction(Request $request, User $user=null)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PaymentLog $paymentLog */
        $paymentLog = new PaymentLog();
        $paymentLog->setUser($user);
        $paymentLog->setBrand($this->getUser()->getBrand());
        $paymentLog->setCreator($this->getUser());
        $paymentLog->setIsProcessed(false);
        $this->checkAccess($paymentLog);

        $form = $this->createForm(new PaymentLogType(function(QueryBuilder $query){return $this->applyPermission($query);}), $paymentLog);
        $form->handleRequest($request);

        if ($form->isValid() && $request->isMethod($request::METHOD_POST)) {
            $paymentLog = $form->getData();

            $em->persist($paymentLog);
            $em->flush();

            return $this->redirectToRoute('dashboard.payment');
        }

        return $this->render('::save.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}