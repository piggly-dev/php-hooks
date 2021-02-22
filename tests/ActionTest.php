<?php
namespace Piggly\Tests\Hooks;

use PHPUnit\Framework\TestCase;
use Piggly\Hooks\Action;
use Piggly\Hooks\Exceptions\ActionNotFoundException;
use Piggly\Hooks\Exceptions\MethodNotFoundException;

class ActionTest extends TestCase
{
	/** @test */
	public function nonValidData ()
	{
		$this->expectException( ActionNotFoundException::class );
		new Action( [ 'doesNotExist' ] );
	}

	/** @test */
	public function validClosure ()
	{
		$closure = function ( $number ) { return $number + 15; };
		$action  = new Action([$closure]);

		$this->assertSame($closure, $action->getAction()['fnc']);
	}

	/** @test */
	public function validClosureWithParams ()
	{
		$closure = function ( $number ) { return $number + 15; };
		$expects = [ 'fnc' => $closure, 'params' =>  [10] ];
		$action  = new Action([$closure, 10]);

		$this->assertSame($expects, $action->getAction());
	}

	/** @test */
	public function emptyStaticObjectMethodSent ()
	{
		$this->expectException( MethodNotFoundException::class );
		new Action([StaticOperations::class]);
	} 

	/** @test */
	public function validStaticObject ()
	{
		$expects = [ 'fnc' => [StaticOperations::class, 'fsum'], 'params' => [] ];
		$action = new Action([StaticOperations::class, 'fsum']);
		$this->assertSame($expects, $action->getAction());
	} 

	/** @test */
	public function validStaticObjectWithParams ()
	{
		$expects = [ 'fnc' => [StaticOperations::class, 'fsum'], 'params' => [ 10 ] ];
		$action = new Action([StaticOperations::class, 'fsum', 10]);
		$this->assertSame($expects, $action->getAction());
	} 

	/** @test */
	public function invalidStaticObjectMethod ()
	{
		$this->expectException( MethodNotFoundException::class );
		new Action([StaticOperations::class, 'nonExist']);
	} 

	/** @test */
	public function emptyObjectMethodSent ()
	{
		$obj = new Operations();
		$this->expectException( MethodNotFoundException::class );
		new Action([$obj]);
	} 

	/** @test */
	public function validObject ()
	{
		$obj = new Operations();
		$expects = [ 'fnc' => [$obj, 'fsum'], 'params' => [] ];
		$action = new Action([$obj, 'fsum']);
		$this->assertSame($expects, $action->getAction());
	} 

	/** @test */
	public function validObjectWithParams ()
	{
		$obj = new Operations();
		$expects = [ 'fnc' => [$obj, 'fsum'], 'params' => [10] ];
		$action = new Action([$obj, 'fsum', 10]);
		$this->assertSame($expects, $action->getAction());
	} 

	/** @test */
	public function invalidObjectMethod ()
	{
		$obj = new Operations();
		$this->expectException( MethodNotFoundException::class );
		new Action([$obj, 'nonExist']);
	} 

	/** @test */
	public function validFunction ()
	{
		$expects = [ 'fnc' => 'fsum', 'params' => [] ];
		$action  = new Action(['fsum']);
		$this->assertSame($expects, $action->getAction()); 
	} 

	/** @test */
	public function validFunctionWithParams ()
	{
		$expects = [ 'fnc' => 'fsum', 'params' => [ 10 ] ];
		$action  = new Action(['fsum', 10]);
		$this->assertSame($expects, $action->getAction()); 
	} 
}