<?php

// src/EventListener/AccessDeniedListener.php
namespace App\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedListener implements EventSubscriberInterface{
	public static function getSubscribedEvents() : array{
		return [
			// the priority must be greater than the Security HTTP
			// ExceptionListener, to make sure it's called before
			// the default exception listener
			KernelEvents::EXCEPTION => ['onKernelException', 2],
		];
	}

	function __construct(RouterInterface $router){
		$this->router = $router;
	}

	public function onKernelException(ExceptionEvent $event) : void{
		$exception = $event->getThrowable();


		if(!$exception instanceof AccessDeniedException){
			return;
		}
		// ... perform some action (e.g. logging)
		// optionally set the custom response
		$response = new RedirectResponse($this->router->generate('login'));
		$event->setResponse(new Response(null, 403));

		if($exception->getAttributes()[0] === 'ROLE_USER'){
			$response = new RedirectResponse('/login');
			$event->setResponse($response);
		}

		$event->setResponse($response);
		// or stop propagation (prevents the next exception listeners from being called)
		$event->stopPropagation();
	}
}