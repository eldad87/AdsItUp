<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use AppBundle\Services\Brand;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener responsible for adding the default user role and brand at registration
 */
class FOSRegistrationListener implements EventSubscriberInterface
{
	/** @var Brand */
	protected $brand;

	public function setBrand(Brand $brand)
	{
		$this->brand = $brand;
	}

	public static function getSubscribedEvents()
	{
		return array(
			FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
		);
	}

	public function onRegistrationSuccess(FormEvent $event)
	{
		/** @var $user User */
		$user = $event->getForm()->getData();

		$user->addRole('ROLE_AFFILIATE');
		$user->setBrand($this->brand->byHost());
	}
}