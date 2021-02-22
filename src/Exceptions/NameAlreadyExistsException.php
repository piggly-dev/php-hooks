<?php
namespace Piggly\Hooks\Exceptions;

use Exception;
use InvalidArgumentException;

/**
 * Custom exception when function name already exists.
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
class NameAlreadyExistsException extends InvalidArgumentException
{
	/**
	 * Name already exists.
	 * 
	 * @param string $tag
	 * @param string $syntax
	 * @param Exception $previous
	 * @since 1.0.0
	 * @return self
	 */
	public function __construct( 
		string $tag, 
		string $name, 
		Exception $previous = null 
	)
	{
		parent::__construct(
			sprintf('Name `%s` already exists in tag `%s`.', $name, $tag), 
			4, 
			$previous
		);
	}
}