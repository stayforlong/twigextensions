<?php
namespace StayForLong\TwigExtensions\TokenParser;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class Trans extends AbstractTokenParser
{
	const OPERATOR = "=";

	public function parse(Token $token)
	{
		$lineno = $token->getLine();

		$params = [ 'count' => null, 'arguments' => [] ];

		if ( !$this->parser->getStream()->test( Token::BLOCK_END_TYPE ) )
		{
			while ($this->parser->getStream()->test( Token::NAME_TYPE ) )
			{
				if ( $this->parser->getStream()->test( Token::PUNCTUATION_TYPE, 'with' ) )
				{
					$this->parser->getStream()->next();
					continue;
				}

				if ( $this->parser->getStream()->test( Token::NAME_TYPE ) )
				{
					switch ($this->parser->getCurrentToken()->getValue())
					{
						case 'domain':
							$this->parser->getStream()->next();
							$this->parser->getStream()->expect( Token::OPERATOR_TYPE, self::OPERATOR );
							$params['domain'] = $this->parser->getExpressionParser()->parseExpression();
							$this->parser->getStream()->next();
							break;
						case 'count':
							$this->parser->getStream()->next();
							$this->parser->getStream()->expect( Token::OPERATOR_TYPE, self::OPERATOR );
							$params['count'] = $this->parser->getExpressionParser()->parseExpression();
							$this->parser->getStream()->next();
							break;
					}

					$argument_offset = $this->parser->getCurrentToken()->getValue();

					$this->parser->getStream()->next();
					$this->parser->getStream()->expect( Token::OPERATOR_TYPE, self::OPERATOR );

					$params['arguments'][$argument_offset] = $this->parser->getExpressionParser()->parseExpression();
				}

				if ( $this->parser->getStream()->test( Token::PUNCTUATION_TYPE, ',' ) )
				{
					$this->parser->getStream()->next();
					continue;
				}
				break;
			}
		}

		$this->parser->getStream()->expect( Token::BLOCK_END_TYPE );

		$body = $this->parser->subparse( array( $this, 'decideBlockEnd' ), true );
		$this->parser->getStream()->expect( Token::BLOCK_END_TYPE );

		return new \StayForLong\TwigExtensions\Node\Trans( $params, $body, $lineno, $this->getTag() );
	}

	public function decideBlockEnd( $token )
	{
		return $token->test( 'endtrans' );
	}

	public function getTag()
	{
		return 'trans';
	}
}
