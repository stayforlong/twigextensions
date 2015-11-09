<?php
namespace StayForLong\TwigExtensions\Node;

use Twig_Node;
use Twig_Compiler;
use Illuminate\Translation\Translator as LaravelTranslator;

/**
 * The Twig node "T" to be used in translations.
 */
class Trans extends Twig_Node
{
	/**
	 * Function name to actually call in the parsed template.
	 */
	const TRANS_FUNCTION_NAME = 'StayForLong\TwigExtensions\Node\Trans::translate';

	/**
	 * String to be used to prefix argument index to produce placeholder.
	 */
	const ARGUMENT_INDEX_PREFIX = '';

	/**
	 * @param array $params
	 * @param Twig_Node $body
	 * @param int $lineno
	 * @param string $tag
	 */
	public function __construct($params, Twig_Node $body, $lineno, $tag = null)
    {
		$nodes = array(
			'body' => $body,
		);

		if ( isset( $params['count'] ) )
		{
			$nodes['count'] = $params['count'];
			unset( $params['count'] );
		}

		$params['argument_offsets'] = [];
		foreach ( $params['arguments'] as $offset => $argument )
		{
			$nodes['argument_' . $offset] = $argument;
			$params['argument_offsets'][] = $offset;
		}
		unset( $params['arguments'] );

		parent::__construct( $nodes, $params, $lineno, $tag );
    }

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler $compiler
	 */
	function compile( Twig_Compiler $compiler )
	{
		$argument_offsets = $this->getAttribute( 'argument_offsets' );

		$compiler->write( '$translate_arguments = array(' );
		foreach ( $argument_offsets as $key => $offset )
		{
			$compiler->raw( "'".$offset."'" .'=>');
			$compiler->subcompile( $this->getNode( 'argument_' . $offset ) );
			$compiler->raw( ', ' );
		}
		$compiler->raw( ");\n" );

		if ( $this->hasNode( 'count' ) )
		{
			$compiler->write( '$translate_count = ' )->subcompile( $this->getNode( 'count' ) )->raw( ";\n" );
		}
		else
		{
			$compiler->write( "\$translate_count = null;\n" );
		}

		if ( $this->hasNode( 'domain' ) )
		{
			$compiler->write( '$translate_domain = ' )->subcompile( $this->getNode( 'domain' ) )->raw( ";\n" );
		}
		else
		{
			$compiler->write( "\$translate_domain = 'messages';\n" );
		}

		$compiler
			->write( "ob_start(null, 740);\n" )
			->subcompile( $this->getNode( 'body' ) )
			->write( "\$translate_literal = ob_get_clean();\n" )
			->write( 'echo ' . self::TRANS_FUNCTION_NAME . '( $translate_literal, $translate_arguments, $translate_count, $translate_domain );' . "\n" );
	}

	static public function translate( $literal, $arguments = [], $count = 0, $domain = "messages", $locale = null )
	{
		if ( empty( $literal ) )
		{
			trigger_error( 'Empty string found in {% trans %} block', E_USER_WARNING );
			return '';
		}

		$indexed_arguments = [];
		while ( true )
		{
			foreach ( $arguments as $key => $argument )
			{
				if ( !is_array( $argument ) )
				{
					$indexed_arguments[self::ARGUMENT_INDEX_PREFIX . $key] = $argument;
					continue;
				}
				foreach ( $argument as $subargument )
				{
					$indexed_arguments[self::ARGUMENT_INDEX_PREFIX . $key] = $subargument;
				}
				continue 2;
			}
			break;
		}

		$literal = trim($literal);
		$translated_text = self::trans_choice($literal, $count, $indexed_arguments, $domain, $locale);

		unset($indexed_arguments);

		return $translated_text;
	}

	static public function trans_choice($id, $number, array $parameters = [], $domain = 'messages', $locale = null)
	{
		$id = "$domain.$id";
		$message = app('translator')->choice($id, $number, $parameters, $locale);

		if(preg_match("/$domain/", $message))
		{
		   $message = str_replace( $domain.".", "", $message);
		}

		return $message;
	}
}