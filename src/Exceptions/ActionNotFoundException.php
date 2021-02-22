<?php
namespace Piggly\Hooks\Exceptions;

/**
 * Creates a custom exception when method is not found in a object.
 *
 * @author 		Caique M Araujo <caique@piggly.com.br>
 * @link        https://github.com/itscaiqueck
 * @author 		Piggly <dev@piggly.com.br>
 * @link        https://github.com/piggly-dev
 *
 * @copyright 	2018
 * @license     ./LICENSE GNU General Public License v3.0
 * @package 	\Piggly\Framework\Hooking\Exceptions
 *
 * @version 	1.0.0
 */
class ActionNotFoundException extends \InvalidArgumentException
{
	public function __construct( $param, $code = 0, \Exception $previous = null )
	{
            $message = 'The parameter "'.$param.'" is invalid. It\'s not a object, class or function.';

            // Sends the exception
            parent::__construct($message, $code, $previous);
	}
}