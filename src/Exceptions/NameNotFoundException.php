<?php
namespace Piggly\Hooks\Exceptions;

/**
 * Creates a custom exception when name is not found in the hook.
 *
 * @author 	Caique M Araujo <caique@piggly.com.br>
 * @link        https://github.com/itscaiqueck
 * @author 	Piggly <dev@piggly.com.br>
 * @link        https://github.com/piggly-dev
 *
 * @copyright 	2018
 * @license     ./LICENSE GNU General Public License v3.0
 * @package 	\Piggly\Framework\Hooking\Exceptions
 *
 * @version 	1.0.0
 */
class NameNotFoundException extends \InvalidArgumentException
{
    public function __construct( $name, $tag, $code = 0, \Exception $previous = null )
    {
        $message = 'The name "'.$name.'" doesn\'t exist in the hook tag "'.$tag.'".';

        // Sends the exception
        parent::__construct($message, $code, $previous);
    }
}