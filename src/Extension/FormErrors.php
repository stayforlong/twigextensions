<?php
namespace StayForLong\TwigExtensions\Extension;

use StayForLong\TwigExtensions\Node\Trans;
use Twig_Extension;
use Twig_SimpleFunction;

class FormErrors extends Twig_Extension
{
	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'errors_for';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions()
	{
		return [
		  new Twig_SimpleFunction(
			'errors_for',
			function ($name) {
				$arguments = func_get_args();

				$domain = "message";
				$attribute = $arguments[0];
				$errors = $arguments[1];
				$class = $arguments[2];

				$message = $errors->first($attribute, ':message');
				$message = Trans::trans_choice($message, 0);

				if(!empty($class) && !empty($message)){
					$this->addErrroHtmlTag($message, $class);
				}

				return $message;

			}, ['is_safe' => ['html']]
		  ),
		];
	}

	private function addErrroHtmlTag(&$message, $class){
		$message = '<p class="'.$class.'">'.$message.'</p>';
	}
}
