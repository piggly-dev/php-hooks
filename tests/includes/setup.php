<?php
/** Functions */
function fsum ( $number ) { return $number+15; }
function fsub ( $number ) { return $number - 10; }
function fmul ( $number ) { return $number * 3; }
function fdiv ( $number ) { return $number / 2; }
function fpow ( $number, $exp ) { return is_numeric($exp) ? pow($number, $exp) : $number; }

function line () { echo "I am line 02\n"; }
function message ( $message ) { echo sprintf("Message: %s\n", $message); }
function name ( $message ) { echo sprintf("Your name: %s\n", $message); }
function prog ( $message ) { echo sprintf("Progamming Language: %s\n", $message); }

namespace Piggly\Tests\Hooks;
require(dirname(__FILE__).'/../../vendor/autoload.php');

/** Static Objects */
class StaticOperations
{
	public static function fsum ( $number ) 
	{ return $number+15; }

	public static function fsub ( $number ) 
	{ return $number - 10; }

	public static function fmul ( $number ) 
	{ return $number * 3; }

	public static function fdiv ( $number ) 
	{ return $number / 2; }

	public static function fpow ( $number, $exp ) 
	{ return is_numeric($exp) ? pow($number, $exp) : $number; }
}

class StaticSentences
{
	public static function line()
	{ echo "I am line 03\n"; }

	public static function message($message)
	{ echo sprintf("Message: %s\n", $message); }

	public static function name($message)
	{ echo sprintf("Your name: %s\n", $message); }

	public static function prog($message)
	{ echo sprintf("Progamming Language: %s\n", $message); }
}

/** Objects */
class Operations
{
	protected $operations;

	public function __construct()
	{ $this->operations = []; }

	public function getOperations () : array
	{ return $this->operations; }

	public function fsum ( $number ) 
	{ return $this->operations[] = $number+15; }
	
	public function fsub ( $number ) 
	{ return $this->operations[] = $number - 10; }

	public function fmul ( $number ) 
	{ return $this->operations[] = $number * 3; }

	public function fdiv ( $number ) 
	{ return $this->operations[] = $number / 2; }

	public function fpow ( $number, $exp ) 
	{ return $this->operations[] = is_numeric($exp) ? pow($number, $exp) : $number; }
}

class Sentences
{
	protected $sentences;

	public function __construct()
	{ $this->sentences = []; }

	public function getSentences () : array
	{ return $this->sentences; }

	public function line()
	{ echo ($this->sentences[] = "I am line 04\n"); }

	public function message($message)
	{ echo ($this->sentences[] = sprintf("Message: %s\n", $message)); }

	public function name($message)
	{ echo ($this->sentences[] = sprintf("Your name: %s\n", $message)); }

	public function prog($message)
	{ echo ($this->sentences[] = sprintf("Progamming Language: %s\n", $message)); }
}