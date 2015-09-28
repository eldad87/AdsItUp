<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * Display Dashboard
     *
     * @Route("/Dashboard", name="dashboard")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:Dashboard:index.html.twig');
    }
}
