<?php
namespace StayForLong\TwigExtensions\Extension;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserActive extends AbstractExtension
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
		  new TwigFunction(
			'user_active',
			function () {
				return Sentry::check();
			}
		  ),
		];
	}
}
