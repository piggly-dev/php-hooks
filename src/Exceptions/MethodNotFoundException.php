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
class MethodNotFoundException extends \InvalidArgumentException
{
    public function __construct( $method, $object, $code = 0, \Exception $previous = null )
    {
        if ( is_object ( $object ) )
        { $object =  get_class ( $object ); }

        $message = 'The method "'.$method.'" doesn\'t exist inside object "'.$object.'".';

        // Sends the exception
        parent::__construct($message, $code, $previous);
    }
}