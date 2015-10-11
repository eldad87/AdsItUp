<?php
namespace AppBundle\Menu;

use AppBundle\Services\AccessMap;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\DiExtraBundle\Metadata\ClassMetadata;
use JMS\DiExtraBundle\Metadata\DefaultNamingStrategy;
use JMS\DiExtraBundle\Metadata\Driver\AnnotationDriver;
use Knp\Menu\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContext;


class Builder extends ContainerAware
{
    /** @var Router */
    private $router;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var AnnotationDriver
     */
    private $metadataReader;

    /**
     * @var AccessMap
     */
    private $accessMap;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        if(!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $menu;
        }

        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        // Dashboard
        $menu->addChild('Dashboard', array('route' => 'dashboard'));

        if($this->container->get('security.authorization_checker')->isGranted('ROLE_AFFILIATE_MANAGER')) {
            // Dashboard
            $menu->addChild('User', array('route' => 'dashboard.user'));
        }

        if($this->container->get('security.authorization_checker')->isGranted('ROLE_AFFILIATE')) {
            // Offer
            $menu->addChild('Offer')
                ->setAttribute('dropdown', true);
            $menu['Offer']->addChild('List', array('route' => 'dashboard.offer'))
                ->setAttribute('icon', 'glyphicon glyphicon-list');
            $menu['Offer']->addChild('Add', array('route' => 'dashboard.offer.save'))
                ->setAttribute('icon', 'glyphicon glyphicon-plus');
            $menu['Offer']->addChild('Pixel', array('route' => 'dashboard.pixel'))
                ->setAttribute('icon', 'glyphicon glyphicon-screenshot');
            $menu['Offer']->addChild('Record', array('route' => 'dashboard.record'))
                ->setAttribute('icon', 'glyphicon glyphicon-paperclip');
        }

        if($this->container->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            // Commission Plan
            $menu->addChild('Commission Plan')
                ->setAttribute('dropdown', true);
            $menu['Commission Plan']->addChild('List', array('route' => 'dashboard.commission_plan'))
                ->setAttribute('icon', 'glyphicon glyphicon-list');
            $menu['Commission Plan']->addChild('Add', array('route' => 'dashboard.commission_plan.save'))
                ->setAttribute('icon', 'glyphicon glyphicon-plus');
        }
        //$this->filterMenu($menu);

        return $menu;
    }

    public function userMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

        if($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
            $menu->addChild('User', array('label' => 'Hello, ' . $username . '!'))
                ->setAttribute('dropdown', true);

            $menu['User']->addChild('Profile', array('route' => 'fos_user_profile_edit'))
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