<?php
use Piggly\Hooks\Hook;

// ## Closures ##
Hook::filter('calculate.sum', function ( $number ) { return $number + 15; });
Hook::filter('calculate.sub', function ( $number ) { return $number - 10; });
Hook::filter('calculate.mul', function ( $number ) { return $number * 3; });
Hook::filter('calculate.div', function ( $number ) { return $number / 2; });

// -> Apply => Expects (((10+15)-10)*3)/2 = 22.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Remove
Hook::removeFilter('calculate.sub');

// -> Apply => Expects ((10+15)*3)/2 = 37.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Add 
Hook::filter('calculate.pow?2::1', function ( $number, $exp ) { return is_numeric($exp) ? pow($number, $exp) : $number; });

// -> Apply => Expects (((10^2)+15)*3)/2 = 172.5
$number = Hook::apply('calculate', 10, 2);
echo sprintf("Number: %s\n", $number);

// -> Apply by Name => Expects 10+15 = 25
$number = Hook::applyByName('calculate', 'sum', 10);
echo sprintf("Number: %s\n", $number);

// ## Functions ##
Hook::reset(); // Clean all hooks.

function fsum ( $number ) { return $number+15; }
function fsub ( $number ) { return $number - 10; }
function fmul ( $number ) { return $number * 3; }
function fdiv ( $number ) { return $number / 2; }
function fpow ( $number, $exp ) { return is_numeric($exp) ? pow($number, $exp) : $number; }

Hook::filter('calculate.sum', 'fsum');
Hook::filter('calculate.sub', 'fsub');
Hook::filter('calculate.mul', 'fmul');
Hook::filter('calculate.div', 'fdiv');

// -> Apply => Expects (((10+15)-10)*3)/2 = 22.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Remove
Hook::removeFilter('calculate.sub');

// -> Apply => Expects ((10+15)*3)/2 = 37.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Add 
Hook::filter('calculate.pow?2::1', 'fpow');

// -> Apply => Expects (((10^2)+15)*3)/2 = 172.5
$number = Hook::apply('calculate', 10, 2);
echo sprintf("Number: %s\n", $number);

// -> Apply by Name => Expects 10+15 = 25
$number = Hook::applyByName('calculate', 'sum', 10);
echo sprintf("Number: %s\n", $number);

// -> Apply => Expects (((10^2)+15)*3)/2 = 172.5
$number = Hook::applyOnce('calculate', 10, 2);
echo sprintf("Number: %s\n", $number);

// ## Static Objects ##
Hook::reset(); // Clean all hooks.

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

Hook::filter('calculate.sum', StaticOperations::class, 'fsum');
Hook::filter('calculate.sub', StaticOperations::class, 'fsub');
Hook::filter('calculate.mul', StaticOperations::class, 'fmul');
Hook::filter('calculate.div', StaticOperations::class, 'fdiv');

// -> Apply => Expects (((10+15)-10)*3)/2 = 22.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Remove
Hook::removeFilter('calculate.sub');

// -> Apply => Expects ((10+15)*3)/2 = 37.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Add 
Hook::filter('calculate.pow?2::1', StaticOperations::class, 'fpow');

// -> Apply => Expects (((10^2)+15)*3)/2 = 172.5
$number = Hook::apply('calculate', 10, 2);
echo sprintf("Number: %s\n", $number);

// -> Apply by Name => Expects 10+15 = 25
$number = Hook::applyByName('calculate', 'sum', 10);
echo sprintf("Number: %s\n", $number);

// ## Class Objects ##
Hook::reset(); // Clean all hooks.

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

$operations = new Operations();

Hook::filter('calculate.sum', $operations, 'fsum');
Hook::filter('calculate.sub', $operations, 'fsub');
Hook::filter('calculate.mul', $operations, 'fmul');
Hook::filter('calculate.div', $operations, 'fdiv');

// -> Apply => Expects (((10+15)-10)*3)/2 = 22.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Remove
Hook::removeFilter('calculate.sub');

// -> Apply => Expects ((10+15)*3)/2 = 37.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);

// -> Add 
Hook::filter('calculate.pow?2::1', $operations, 'fpow');

// -> Apply => Expects (((10^2)+15)*3)/2 = 172.5
$number = Hook::apply('calculate', 10, 2);
echo sprintf("Number: %s\n", $number);

// -> Apply by Name => Expects 10+15 = 25
$number = Hook::applyByName('calculate', 'sum', 10);
echo sprintf("Number: %s\n", $number);

// -> Expects 25 -> 15 -> 45 -> 22.5 -> 25 -> 75 -> 37.5 -> 100 -> 115 -> 345 -> 172.5 -> 25
echo sprintf("Operations: %s\n", implode(' -> ', $operations->getOperations()));
echo "- end filters -\n"; 