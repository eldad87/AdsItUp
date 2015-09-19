<?php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        // Dashboard
        $menu->addChild('Dashboard', array('route' => 'dashboard'));

        // Offer
        $menu->addChild('Offer')
            ->setAttribute('dropdown', true);
        $menu['Offer']->addChild('List', array('route' => 'dashboard.offer'))
                        ->setAttribute('icon', 'glyphicon glyphicon-list');
        $menu['Offer']->addChild('Add', array('route' => 'dashboard.offer.save'))
            ->setAttribute('icon', 'glyphicon glyphicon-plus');


        // Commission Plan
        $menu->addChild('Commission Plan')
            ->setAttribute('dropdown', true);
        $menu['Commission Plan']->addChild('List', array('route' => 'dashboard.commission_plan'))
            ->setAttribute('icon', 'glyphicon glyphicon-list');
        $menu['Commission Plan']->addChild('Add', array('route' => 'dashboard.commission_plan.save'))
            ->setAttribute('icon', 'glyphicon glyphicon-plus');

        return $menu;
    }

    public function userMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

        if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
            $menu->addChild('User', array('label' => 'Hello, ' . $username . '!'))
                ->setAttribute('dropdown', true);

            $menu['User']->addChild('Profile', array('route' => 'fos_user_profile_show'))
                ->setAttribute('icon', 'glyphicon glyphicon-user');
            $menu['User']->addChild('Logout', array('route' => 'fos_user_security_logout'))
                ->setAttribute('icon', 'glyphicon glyphicon-log-out');
        } else {
            $menu->addChild('Sign in', array('route' => 'fos_user_security_login'));
            $menu->addChild('Register', array('route' => 'fos_user_registration_register'));
        }

        return $menu;
    }
}