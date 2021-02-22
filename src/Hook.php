<?php
namespace Piggly\Hooks;

use Piggly\Hooks\Exceptions\ActionNotFoundException;
use Piggly\Hooks\Exceptions\MethodNotFoundException;
use Piggly\Hooks\Exceptions\NameAlreadyExistsException;
use Piggly\Hooks\Exceptions\TagNotFoundException;
use Piggly\Hooks\Exceptions\NameNotFoundException;

/**
 * Controls the hooks that will be executed when a specific TAG has called.
 * It operates executing a normal function or a function inside a object passed
 * by reference.
 *
 * The execution is really simple. Let's suppose that exists a public function
 * called test(), to adds it inside a TAG we do the following:
 *
 * $hook->bind( 'header', 'test' );
 * # "header" is the hook name.
 *
 * Now, let's suppose that exists a public function with a N params called
 * testParams( $param1, $param2, ... ), to adds it inside a TAG we do the following:
 *
 * $hook->bind( 'header', 'testParams', $param1, $param2, ... );
 *
 * Lastly, let's suppose we have a object called Test with the start() method,
 * to adds it inside a TAG we do the following:
 * 
 * # Instansable Object
 * $test = new Test();
 * $hook->bind( 'header', $test, 'start' );
 *
 * # Static Object
 * $hook->bind( 'header', 'Test', 'start' );
 *
 * Using params in the methods:
 *
 * # Instansable Object
 * $test = new Test();
 * $hook->bind( 'header', $test, 'start', $param1, $param2, ... );
 *
 * # Static Object
 * $hook->bind( 'header', 'Session', 'start', $param1, $param2, ... );
 *
 * You also can set the priority of exection to a callback. To do this, just
 * add after TAG name a integer number to the respective priority:
 *
 * # Public and Global function
 * $hook->bind( 'header', 15, 'test' );
 *
 * # Instansable Object
 * $test = new Test();
 * $hook->bind( 'header', 20, $test, 'start' );
 *
 * # Static Object
 * $hook->bind( 'header', 5, 'Test', 'start' );
 *
 * Using params in the methods:
 *
 * # Public and Global function
 * $hook->bind( 'header', 11, 'testParams', $param1, $param2, ... );
 *
 * # Instansable Object
 * $test = new Test();
 * $hook->bind( 'header', 12, $test, 'start', $param1, $param2, ... );
 *
 * # Static Object
 * $hook->bind( 'header', 1, 'Session', 'start', $param1, $param2, ... );
 *
 * To finish, just execute the hooke and all the callbacks will be executed,
 * according to and order of priority.
 *
 * $hook->run( 'header' );
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
class Hook
{
	/**
	 * @var int Hook type as action.
	 * @since 1.0.0
	 */
	const HOOK_ACTION = 1;
	/**
	 * @var int Hook type as filter.
	 * @since 1.0.0
	 */
	const HOOK_FILTER = 2;
	/**
	 * @var int Hook type as dispatcher.
	 * @since 1.0.0
	 */
	const HOOK_DISPATCH = 3;

	/**
	 * @var integer Default priority.
	 * @since 1.0.0
	 */
	const PRIORITY_DEFAULT = 10;

	/**
	 * @var integer Default args.
	 * @since 1.0.0
	 */
	const ARGS_DEFAULT = 1;

	/**
	 * @var array Actions to be executed.
	 * @since 1.0.0
	 */
	protected $actions = [];

	/**
	 * @var array Filters to be executed.
	 * @since 1.0.0
	 */
	protected $filters = [];

	/**
	 * Not allowed to construct as public mode.
	 * @since 1.0.0
	 * @return void
	 */
	protected function __construct ()
	{}

	/**
	 * Not allowed to clone object.
	 * @since 1.0.0
	 * @return void
	 */
	protected function __clone ()
	{}

	/**
	 * Not allowed to serialize object.
	 * @since 1.0.0
	 * @return void
	 */
	protected function __wakeup ()
	{}

	/**
	 * Returns a singleton instance of this class.
	 * @since 1.0.0
	 * @return self
	 */
	protected static function getInstance ()
	{
		// Static instance
		static $instance;

		// If is null, creates a new instance
		if ( is_null ( $instance ) )
		{ $instance = new self(); }

		// Returns the static instance
		return $instance;
	}

	/**
	 * Add a new dispatch callback to $tag.
	 * 
	 * @see Hook::bind
	 * @param string|array $tagSyntax TAG where action will be added.
	 * @param mixed $args,... Action $args.
	 * @since 1.0.0
	 * @return Action
	 */
	public static function dispatch ( $tagSyntax, ...$args )
	{
		/** @var Hook */
		$instance = self::getInstance();

		return $instance->bind(
			self::HOOK_DISPATCH,
			$tagSyntax,
			$args
		);
	} 

	/**
	 * Add a new action callback to $tag.
	 * 
	 * @see Hook::bind
	 * @param string|array $tagSyntax TAG where action will be added.
	 * @param mixed $args,... Action $args.
	 * @since 1.0.0
	 * @return Action
	 */
	public static function action ( $tagSyntax, ...$args )
	{
		/** @var Hook */
		$instance = self::getInstance();

		return $instance->bind(
			self::HOOK_ACTION,
			$tagSyntax,
			$args
		);
	} 

	/**
	 * Add a new filter callback to $tag.
	 * 
	 * @see Hook::bind 
	 * @param string|array $tagSyntax TAG where action will be added.
	 * @param mixed $args,... Filter action $args.
	 * @since 1.0.0
	 * @return Action
	 */
	public static function filter ( $tagSyntax, ...$args )
	{
		/** @var Hook */
		$instance = self::getInstance();

		return $instance->bind(
			self::HOOK_FILTER,
			$tagSyntax,
			$args
		);
	}

	/**
	 * Remove a dispatcher by $tagSyntax.
	 * 
	 * @param string|array $tagSyntax TAG where action will be added.
	 * @since 1.0.0
	 * @return bool
	 */
	public static function removeDispatcher ( $tagSyntax ) : bool
	{
		/** @var Hook */
		$instance = self::getInstance();

		return $instance->remove(
			self::HOOK_DISPATCH,
			$tagSyntax
		);
	}

	/**
	 * Remove an action by $tagSyntax.
	 * 
	 * @param string|array $tagSyntax TAG where action will be added.
	 * @since 1.0.0
	 * @return bool
	 */
	public static function removeAction ( $tagSyntax ) : bool
	{
		/** @var Hook */
		$instance = self::getInstance();

		return $instance->remove(
			self::HOOK_ACTION,
			$tagSyntax
		);
	}

	/**
	 * Remove an filter by $tagSyntax.
	 * 
	 * @param string|array $tagSyntax TAG where action will be added.
	 * @since 1.0.0
	 * @return bool
	 */
	public static function removeFilter ( $tagSyntax ) : bool
	{
		/** @var Hook */
		$instance = self::getInstance();

		return $instance->remove(
			self::HOOK_FILTER,
			$tagSyntax
		);
	}

	/**
	 * Reset all actions and filters hooks.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public static function reset ()
	{
		/** @var Hook */
		$instance = self::getInstance();

		$instance->actions     = [];
		$instance->filters     = [];

		return;
	}

	/**
	 * Adds a new action to be executed in a hook TAG. By following a priority
	 * stack, the action will be executed.
	 *
	 * To set a priority different of default value, send it as the first
	 * parameter by using a integer var:
	 *
	 * bind( $tagSyntax, $priority, ... )
	 *
	 * To set a object by reference, send it as the next parameter, with or
	 * without the priority value:
	 *
	 * bind( $tagSyntax, $priority, $obj, $method, $params... )
	 * bind( $tagSyntax, $obj, $method, $params... )
	 *
	 * To set a static object, send it as the next parameter, with or
	 * without the priority value:
	 *
	 * bind( $tagSyntax, $priority, $static, $method, $params... )
	 * bind( $tagSyntax, $static, $method, $params... )
	 *
	 * To set a function send it as the next parameter, with or
	 * without the priority value:
	 *
	 * bind( $tagSyntax, $priority, $function, $params... )
	 * bind( $tagSyntax, $function, $params... )
	 *
	 * A function can be a Closure object or a simple string.
	 *
	 * @param int $type Bind type.
	 * @param string|array $tagSyntax TAG where action will be added.
	 * @param mixed $args,... You can set here the priority, object,
	 * 						  static object, function and the parameters
	 * 						  to send. This function will formate it.
	 * @since 1.0.0
	 * @return Action The action object.
	 * @throws MethodNotFoundException When a method is not found in a object.
	 * @throws ActionNotFoundException When a action is not found to be executed.
	 * @throws NameAlreadyExistsException If function name already exists.
	 */
	protected function bind ( int $type, $tagSyntax, ...$args )
	{
		// Parse tag syntax data
		$tagSyntax = is_array($tagSyntax) ? $tagSyntax : Syntax::read($tagSyntax);

		// Get tag syntax data
		$tag      = $tagSyntax['tag'];
		$name     = isset($tagSyntax['name']) ? $tagSyntax['name'] : null;
		$priority = isset($tagSyntax['priority']) ? $tagSyntax['priority'] : self::PRIORITY_DEFAULT;
		
		// Creates a new action
		$action = new Action ( $args[0] );

		// Add action priority
		$action->priority($priority)->type($type);

		if ( $type !== self::HOOK_DISPATCH )
		{
			$mArgs = isset($tagSyntax['args']) ? $tagSyntax['args'] : self::ARGS_DEFAULT;
			$action->acceptArgs($mArgs);
		}

		// Check if name is already set
		if ( !is_null($name) )
		{
			// Check if it is already set
			if ( $this->nameExists( $tag, $name ) )
			{ throw new NameAlreadyExistsException($tag, $name); }

			$action->name($name);
		}

		// Get actions or filters
		$accessing = $this->getType($type);

		// If TAG was not set, sets it
		if ( !isset ( $this->$accessing[$tag] ) )
		{ $this->$accessing[$tag] = []; }

		// If priority was not set, sets it
		if ( !isset ( $this->$accessing[$tag][$priority] ) )
		{ $this->$accessing[$tag][$priority] = []; }

		// Adds following the TAG and priority
		$this->$accessing[$tag][$priority][] = $action;

		return $action;
	}

	/**
	 * Apply filter by following a tag.
	 *
	 * @param string $tag Tag name to execute.
	 * @param mixed $value Default value to filter.
	 * @param mixed $args Additional parameters to tag.
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function apply ( string $tag, $value, ...$args )
	{
		// Add $value to args
		array_unshift($args, $value);

		/** @var Hook */
		$instance = self::getInstance();

		// Get filters
		$accessing = $instance->getType(self::HOOK_FILTER);

		if ( !isset ( $instance->$accessing[$tag] ) )
		{ return $value; }

		// Gets the filters by ordering it
		$hooks = $instance->sortHooks( self::HOOK_FILTER, $tag );

		foreach ( $hooks as $priority => $idx )
		{
			// To each filter in priority stack, executes
			foreach ( $idx as $id => $filter )
			{
				$call  = $filter->getAction();
				$args[0] = \call_user_func_array( 
					$call['fnc'], 
					array_slice($args, 0, $filter->getAcceptedArgs()) 
				);
			}
		}

		return $args[0];
	}

	/**
	 * Apply filter just one time in code.
	 *
	 * @param string $tag Tag name to execute.
	 * @param mixed $value Default value to filter.
	 * @param mixed $args Additional parameters to tag.
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function applyOnce ( string $tag, $value, ...$args )
	{
		// Apply
		$returning = self::apply($tag, $value, ...$args);

		/** @var Hook */
		self::getInstance()->remove(self::HOOK_FILTER, ['tag' => $tag]);

		return $returning;
	}

	/**
	 * Run a specific filter by following a tag.
	 *
	 * @param string $tag Tag name to execute.
	 * @param string $name Filter name to execute.
	 * @param mixed $value Default value to filter.
	 * @param mixed $args Additional parameters.
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function applyByName ( string $tag, string $name, $value, ...$args )
	{
		// Add $value to args
		array_unshift($args, $value);

		/** @var Hook */
		$instance = self::getInstance();

		// Get filters
		$accessing = $instance->getType(self::HOOK_FILTER);

		if ( !isset ( $instance->$accessing[$tag] ) )
		{ return $value; }

		// Gets the filters by ordering it
		$filters = $instance->$accessing[$tag];
		// Stores founds
		$found = 0;

		foreach ( $filters as $priority => $idx )
		{
			// To each action in priority stack, checks it
			foreach ( $idx as $id => $filter )
			{
				if ( $filter->getName() === $name )
				{
					$found++;
					$call = $filter->getAction();
					$args[0] = \call_user_func_array( 
						$call['fnc'],
						array_slice($args, 0, $filter->getAcceptedArgs()) 
					);

					break;
				}
			}
		}

		if ( $found === 0 )
		{ return $value; }

		return $args[0];
	}

	/**
	 * Run the actions by following a tag.
	 *
	 * @param string $tag Tag name to execute.
	 * @param mixed $args Additional parameters.
	 * @since 1.0.0
	 * @return mixed FALSE when $tag not found, VOID when found.
	 */
	public static function run ( string $tag, ...$args )
	{
		/** @var Hook */
		$instance = self::getInstance();

		// Get actions
		$accessing = $instance->getType( self::HOOK_ACTION );

		if ( ! isset ( $instance->$accessing[$tag] ) )
		{ return false; }

		// Gets the filters by ordering it
		$hooks = $instance->sortHooks( self::HOOK_ACTION, $tag );

		foreach ( $hooks as $priority => $idx )
		{
			// To each action in priority stack, executes
			foreach ( $idx as $id => $action )
			{
				$call = $action->getAction();
				\call_user_func_array( 
					$call['fnc'], 
					$action->getType() === self::HOOK_ACTION ? 
						array_slice($args, 0, $action->getAcceptedArgs()) : $call['params']
				);
			}
		}
	}

	/**
	 * Apply filter just one time in code.
	 *
	 * @param string $tag Tag name to execute.
	 * @param mixed $value Default value to filter.
	 * @param mixed $args Additional parameters to tag.
	 * @since 1.0.0
	 * @return mixed FALSE when $tag not found, VOID when found.
	 */
	public static function runOnce ( string $tag, ...$args )
	{
		// Run
		if ( self::run($tag, ...$args) === false )
		{ return false; }

		/** @var Hook */
		self::getInstance()->remove(self::HOOK_ACTION, ['tag' => $tag]);
	}

	/**
	 * Run a specific action by following a tag.
	 *
	 * @param string $tag Tag name to execute.
	 * @param string $name Action name to execute.
	 * @param mixed $args Additional parameters.
	 * @since 1.0.0
	 * @return mixed FALSE when $tag or $name not found, VOID when found.
	 */
	public static function runByName ( string $tag, string $name, ...$args )
	{
		/** @var Hook */
		$instance = self::getInstance();

		// Get actions
		$accessing = $instance->getType(self::HOOK_ACTION);

		if ( !isset ( $instance->$accessing[$tag] ) )
		{ return false; }

		// Gets the actions by ordering it
		$actions = $instance->$accessing[$tag];
		// Stores founds
		$found = 0;

		foreach ( $actions as $priority => $idx )
		{
			// To each action in priority stack, checks it
			foreach ( $idx as $id => $action )
			{
				if ( $action->getName() === $name )
				{
					$found++;
					$call = $action->getAction();
					\call_user_func_array( 
						$call['fnc'],
						$action->getType() === self::HOOK_ACTION ? 
							array_slice($args, 0, $action->getAcceptedArgs()) : $call['params']
					);

					return;
				}
			}
		}

		if ( $found === 0 )
		{ return false; }
	}

	/**
	 * Removes a tag from hook.
	 *
	 * @param int $type
	 * @param string|array $tagSyntax Tag syntax to remove.
	 * @since 1.0.0
	 * @return bool
	 */
	protected function remove ( int $type, $tagSyntax ) : bool
	{
		// Get actions or filters
		$accessing = $this->getType($type);
		
		// Parse tag syntax data
		$tagSyntax = is_array($tagSyntax) ? $tagSyntax : Syntax::read($tagSyntax);

		// Get tag syntax data
		$tag      = $tagSyntax['tag'];
		$name     = isset($tagSyntax['name']) ? $tagSyntax['name'] : null;

		if ( is_null($name) )
		{
			if ( isset ( $this->$accessing[$tag] ) )
			{ 
				unset ( $this->$accessing[$tag] ); 
				return true;
			}
		}
		else 
		{ return $this->removeByName($type, $tag, $name); }
		
		return false;
	}

	/**
	 * Removes a specific action from hook.
	 *
	 * @param int $type
	 * @param string $tag Tag name to remove.
	 * @param string $name Action name to remove.
	 * @since 1.0.0
	 * @return bool
	 */
	protected function removeByName ( int $type, string $tag, string $name ) : bool
	{
		// Get actions or filters
		$accessing = $this->getType($type);
		// Gets the actions by ordering it
		$actions = isset ( $this->$accessing[$tag] ) ? $this->$accessing[$tag] : null;
		// Stores founds
		$found = 0;

		// If there are actions
		if ( !is_null ( $actions ) )
		{
			foreach ( $actions as $priority => $idx )
			{
				// To each action in priority stack, checks it
				foreach ( $idx as $id => $action )
				{
					if ( $action->getName() === $name )
					{
						$found++;
						unset ( $this->$accessing[$tag][$priority][$id] );
					}
				}
			}
		}

		if ( $found === 0 )
		{ return false; }

		return true;
	}

	/**
	 * Order the priorities stack of a specific TAG, if the TAG is not set...
	 * then order all available.
	 *
	 * @param int $type
	 * @param string $tag TAG to be ordained.
	 * @return array The ordened TAG.
	 * @since 1.0.0
	 */
	protected function sortHooks ( int $type, $tag = null ) : array
	{
		// Get actions or filters
		$accessing = $this->getType($type);

		// If there is no TAG set
		if ( $tag === null )
		{
			// Sort each TAG
			foreach ( $this->$accessing as $key => $value )
			{ $this->sortHooks($key); }
		}
		else
		{
			// If the TAG exists, sort it, if not return an empty array
			if ( isset( $this->$accessing[$tag] ) )
			{ ksort ( $this->$accessing[$tag] ); }
			else
			{ return array(); }

			return $this->$accessing[$tag];
		}
	}

	/**
	 * Return if action $name already exists in $tag.
	 * 
	 * @param string $tag Tag name.
	 * @param string $name Action name.
	 * @since 1.0.0
	 * @return bool
	 */
	protected function nameExists ( string $tag, string $name ) : bool
	{
		// Gets the actions by ordering it
		$actions = isset ( $this->actions[$tag] ) ? $this->actions[$tag] : null;

		// If there are actions
		if ( !is_null ( $actions ) )
		{
			foreach ( $actions as $priority => $idx )
			{
				// To each action in priority stack, checks it
				foreach ( $idx as $id => $action )
				{
					if ( $action->getName() === $name )
					{ return true; }
				}
			}
		}

		return false;
	}

	/**
	 * Get array name based in $type.
	 * 
	 * @param int $type
	 * @param array $tag
	 * @since 1.0.0
	 * @return void
	 */
	protected function getType ( int $type )
	{ 
		switch ( $type )
		{
			case self::HOOK_ACTION:
				return 'actions';
			case self::HOOK_FILTER:
				return 'filters';
			case self::HOOK_DISPATCH:
				return 'actions';
		}
	}
}