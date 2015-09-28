<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener responsible for adding the default user role and brand at registration
 */
class FOSRegistrationListener implements EventSubscriberInterface
{
	/** @var AbstractManagerRegistry */
	protected $doctrine;

	public function __construct(AbstractManagerRegistry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public static function getSubscribedEvents()
	{
		return array(
			FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInit',
		);
	}

	public function onRegistrationInit(GetResponseUserEvent $event)
	{
		/** @var $user User */
		$user = $event->getUser();
		/** @var Request $request */
		$request = $event->getRequest();
		$referrerId = $request->query->getInt('referrerId', false);
		if(!$referrerId) {
			$referrerId = $request->getSession()->get('referrerId', false);
		}
		if($referrerId) {
			$request->getSession()->set('referrerId', $referrerId);
			$refUser = $this->doctrine->getManager()->getReference('AppBundle\Entity\User', $referrerId);
			$user->setReferrer($refUser);
		}
		$user->setBalance(0);
	}
}