<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class with the default authentication success handling logic.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class SSOAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface{
	public function __construct(
		private                 $options,
		private RouterInterface $router
	){
	}

	/**
	 * {@inheritdoc}
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token){
		$successl_url = $this->router->generate('index');
		if($request->query->has('success_url') && $request->query->get('success_url', '') != ''){
			$successl_url = $request->query->get('success_url');
		}

		return new RedirectResponse($successl_url);
	}
}
