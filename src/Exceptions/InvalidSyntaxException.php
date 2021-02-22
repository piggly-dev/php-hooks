<?php
namespace Piggly\Hooks\Exceptions;

use Exception;
use InvalidArgumentException;

/**
 * Custom exception when tag syntax is invalid and cannot be read.
 *
 * @author Caique M Araujo <caique@piggly.com.br>
 * @link https://github.com/caiquearaujo
 * @author Piggly <dev@piggly.com.br>
 * @link https://github.com/piggly-dev
 * @copyright 2021
 * @license ./LICENSE MIT
 * @package \Piggly\Hooks
 * @since 1.0.0
 * @version 1.0.0
 */
class InvalidSyntaxException extends InvalidArgumentException
{
	/**
	 * Invalid syntax found.
	 * 
	 * @param string $syntax
	 * @param Exception $previous
	 * @since 1.0.0
	 * @return self
	 */
	public function __construct( 
		string $syntax, 
		Exception $previous = null 
	)
	{
		parent::__construct(
			sprintf('Invalid syntax `%s` found.', $syntax), 
			2, 
			$previous
		);
	}
}