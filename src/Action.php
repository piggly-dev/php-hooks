<?php
namespace Piggly\Hooks;

use \Piggly\Hooks\Exceptions\ActionNotFoundException;
use \Piggly\Hooks\Exceptions\MethodNotFoundException;

/**
 * Constructs a action hook by parsing the action and index it, optionally,
 * with a custom name. The action detects all kinds formats as below:
 *
 * \Closure => Function as variable;
 * Object => Class as variable;
 * Static => Object Class name as string;
 * Function => Names as string;
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
class Action
{
	/**
	 * @var string Action name.
	 * @since 1.0.0
	 */
	protected $name;

	/**
	 * @var int Action priority.
	 * @since 1.0.0
	 */
	protected $priority = Hook::PRIORITY_DEFAULT;

	/**
	 * @var int Action accepted args.
	 * @since 1.0.0
	 */
	protected $args = Hook::ARGS_DEFAULT;

	/**
	 * @var int Action type.
	 * @since 1.0.0
	 */
	protected $type = Hook::HOOK_ACTION;

	/**
	 * @var array Action to be executed.
	 * @since 1.0.0
	 */
	protected $action;

	/**
	 * Creates the action instance.
	 *
	 * To set a object by reference, send it as the next parameter:
	 *
	 *	 __construct( $obj, $method, $params... )
	 *
	 * To set a static object, send it as the next parameter:
	 *
	 *	 __construct( $obj, $staticMethod, $params... )
	 *
	 * To set a function send it as the next parameter:
	 *
	 *	 __construct( $function, $params... )
	 *
	 * A function can by a Closure object or a simple string.
	 *
	 * @param array $args The object, static object, function and the parameters to send. This function will formate it.
	 * @since 1.0.0
	 * @return self
	 * @throws \MethodNotFoundException	When a method is not found in a object.
	 * @throws \ActionNotFoundException	When a action is not found to be executed.
	 */
	public function __construct ( $args )
	{
		$this->action = $this->parseAction( $args );
		return $this;
	}

	/**
	 * Set a name to this action. 
	 * By usign this, will be more easy find it.
	 *
	 * @param string $name Name to this hook.
	 * @since 1.0.0
	 * @return self
	 */
	public function name ( $name )
	{ $this->name = $name; return $this; }

	/**
	 * Get the action name.
	 *
	 * @return string|null Action name or NULL.
	 * @since 1.0.0
	 */
	public function getName ()
	{ return $this->name ?? null; }

	/**
	 * Set a priority to this action.
	 *
	 * @param int $priority Priority to this hook.
	 * @since 1.0.0
	 * @return self
	 */
	public function priority ( int $priority )
	{ $this->priority = $priority; return $this; }

	/**
	 * Get the action priority.
	 *
	 * @return int|null Accepted args
	 * @since 1.0.0
	 */
	public function getPriority () : ?int
	{ return $this->priority; }

	/**
	 * Set accepted args to this action.
	 *
	 * @param int $args Accepted args.
	 * @since 1.0.0
	 * @return self
	 */
	public function acceptArgs ( int $args )
	{ $this->args = $args; return $this; }

	/**
	 * Get accepted args.
	 *
	 * @return int|null Accepted args
	 * @since 1.0.0
	 */
	public function getAcceptedArgs () : ?int
	{ return $this->args; }

	/**
	 * Set type to this action.
	 *
	 * @param int $type Action type.
	 * @since 1.0.0
	 * @return self
	 */
	public function type ( int $type )
	{ $this->type = $type; return $this; }

	/**
	 * Get type.
	 *
	 * @return int|null Action type.
	 * @since 1.0.0
	 */
	public function getType () : ?int
	{ return $this->type; }

	/**
	 * Get the action function and method to execute.
	 *
	 * @return array Action mounted.
	 * @since 1.0.0
	 */
	public function getAction ()
	{ return $this->action; }

	/**
	 * Parses the action to a mounted array ready to be used.
	 *
	 * @param array	$action	The object, static object,
	 *						function and the parameters to
	 *						send. This function will formate it.
	 * @return array Action mounted.
	 * @since 1.0.0
	 */
	protected function parseAction ( $args )
	{
		// Creates action array
		$action = array();

		// If action is a anonymous function
		if ( $args[0] instanceof \Closure )
		{
			// Get the the function and removes it from args
			$action['fnc'] = $args[0];
			array_shift( $args );
		}
		// If the arg is a object,
		// accepts it as object by reference
		else if ( is_object( $args[0] )
					|| class_exists( $args[0] ) )
		{
			// Get the object and removes it from args
			$action['fnc'] = array();
			$action['fnc'][] = $args[0];
			array_shift( $args );

			if ( empty( $args ) )
			{ throw new MethodNotFoundException('(not set)', $action['fnc'][0]); }

			// Checks if the method exists in object
			if ( method_exists( $action['fnc'][0], $args[0] ) )
			{
				// Get the method and removes it from args
				$action['fnc'][] = $args[0];
				array_shift( $args );
			}
			else
			{ throw new MethodNotFoundException( $args[0], $action['fnc'][0] ); }
		}
		// Tries to accept it as function
		else if ( function_exists ( $args[0] ) )
		{
			// Get the the function and removes it from args
			$action['fnc'] = $args[0];
			array_shift( $args );
		}
		else
		{ throw new ActionNotFoundException( $args[0] ); }

		// Get the params
		if ( !empty ( $args ) )
		{ $action['params'] = $args; }
		else
		{ $action['params'] = array(); }

		return $action;
	}
}