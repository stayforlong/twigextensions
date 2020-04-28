<?php
namespace StayForLong\TwigExtensions\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Trans extends AbstractExtension
{
	public function getTokenParsers()
	{
		return array( new \StayForLong\TwigExtensions\TokenParser\Trans() );
	}

	public function getFilters()
	{
		return array(
			new TwigFilter('trans', 'trans', array()),
		);
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'trans';
	}

	public function trans( $literal, $arguments = [], $count = 0, $domain = "messages", $local = null )
	{
		$offset_arguments = array( );
		foreach ( $arguments as $offset => $argument )
		{
			$offset_arguments[intval( $offset )] = $argument;
		}
		ksort( $offset_arguments );

		return Trans::translate( $literal, $offset_arguments, $count, $domain, $local );
	}
}
