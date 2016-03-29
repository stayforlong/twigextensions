<?php
namespace StayForLong\TwigExtensions\Extension;

use StayForLong\TwigExtensions\Node\Trans;
use Twig_Extension;
use Twig_SimpleFunction;

class FormErrors extends Twig_Extension
{
	const DOMAIN = "message";

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
		return array(
			new Twig_SimpleFunction(
				'errors_for',
				function ($name) {
					$arguments = func_get_args();

					if(empty($arguments[0])){
						throw new \Twig_Error("Empty first argument to define the attribute of error");
					}

					if(empty($arguments[1])){
						throw new \Twig_Error("Empty second argument to define the array errors");
					}

					$attribute = $arguments[0];
					$errors    = $arguments[1];
					$class     = !empty($arguments[2]) ? $arguments[2] : null;

					$message = $errors->first($attribute, ":" . self::DOMAIN);
					$message = Trans::trans_choice($message, 0);

					if (!empty($class) && !empty($message)) {
						$this->addErrroHtmlTag($message, $class);
					}

					return $message;

				}, array('is_safe' => array('html'))
			),
		);
	}

	private function addErrroHtmlTag(&$message, $class)
	{
		$message = '<p class="' . $class . '">' . $message . '</p>';
	}
}
