<?php
namespace Piggly\Hooks\Exceptions;

/**
 * Creates a custom exception when tag is not found in the hook.
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
class TagNotFoundException extends \InvalidArgumentException
{
    public function __construct( $tag, $code = 0, \Exception $previous = null )
    {
        $message = 'The tag "'.$tag.'" doesn\'t exist in the hook.';

        // Sends the exception
        parent::__construct($message, $code, $previous);
    }
}