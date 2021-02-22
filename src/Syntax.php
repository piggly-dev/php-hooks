<?php
namespace Piggly\Hooks;

use Piggly\Hooks\Exceptions\InvalidSyntaxException;

/**
 * Create and read tag syntax which contains:
 * tag, function name, priority and args data.
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
class Syntax 
{
	/**
	 * Create a tag syntax.
	 * 
	 * @param string $tag Tag name.
	 * @param string $name Function name.
	 * @param int $priority Priority.
	 * @param int $args Args to pass.
	 * @since 1.0.0
	 * @return array Which contains tag, name and priority keys.
	 */
	public static function create ( 
		string $tag, 
		string $name = null,
		int $priority = null,
		int $args = null
	) : array
	{
		$tag = [
			'tag' => $tag
		];

		if ( !empty($name) )
		{ $tag['name'] = $name; }

		if ( !is_null($args) )
		{ $tag['args'] = $args; }

		if ( !is_null($priority) )
		{ $tag['priority'] = $priority; }

		return $tag;
	}

	/**
	 * Parse a tag syntax to an array.
	 * 
	 * @param string $string syntax.
	 * @since 1.0.0
	 * @return array Which contains tag, name, args and priority keys.
	 * @throws InvalidSyntaxException when $syntax is invalid.
	 */
	public static function read ( 
		string $syntax 
	) : array
	{
		$tag = [];

		\preg_match(
			'/^(?:(?P<tag>[^\.\:\?]+))(?:\.(?P<name>[^\:\?]+))?(?:\?(?P<args>[\d]+))?(?:\:\:(?P<priority>[\d]+))?$/i',
			$syntax,
			$tag
		);

		if ( empty($tag) || !isset($tag['tag']) )
		{ throw new InvalidSyntaxException($syntax); }

		foreach ( $tag as $key => $value )
		{
			if ( is_numeric($key) || is_null($value) || $value === '' )
			{ unset($tag[$key]); }
			else if ( $key === 'priority' || $key === 'args' )
			{ $tag[$key] = intval($value); }
		}

		return $tag;
	}
}