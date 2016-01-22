<?php
namespace StayForLong\TwigExtensions\Extension;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Twig_Extension;
use Twig_SimpleFunction;

class UserActive extends Twig_Extension
{
	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'user_active';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions()
	{
		return [
		  new Twig_SimpleFunction(
			'user_active',
			function () {
				return Sentry::check();
			}
		  ),
		];
	}
}
