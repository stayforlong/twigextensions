<?php
namespace StayForLong\TwigExtensions\Extension;

use StayForLong\TwigExtensions\Node\Trans as Translation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Error\Error;

class FormErrors extends AbstractExtension
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
			new TwigFunction(
				'errors_for',
				function ($name) {
					$arguments = func_get_args();

					if(empty($arguments[0])){
						throw new Error("Empty first argument to define the attribute of error");
					}

					if(empty($arguments[1])){
						throw new Error("Empty second argument to define the array errors");
					}

					$attribute = $arguments[0];
					$errors    = $arguments[1];
					$class     = !empty($arguments[2]) ? $arguments[2] : null;

					$message = $errors->first($attribute, ":" . self::DOMAIN);
					$message = Translation::trans_choice($message, 0);

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
