<?php
namespace Piggly\Tests\Hooks;

use PHPUnit\Framework\TestCase;
use Piggly\Hooks\Exceptions\ActionNotFoundException;
use Piggly\Hooks\Exceptions\MethodNotFoundException;
use Piggly\Hooks\Hook;

class HookTest extends TestCase
{
	/** @test */
	public function invalidFilter ()
	{
		$this->expectException(ActionNotFoundException::class);
		Hook::filter('calculate.sum', 'notfound');
	}

	/** @test */
	public function invalidFilterMethod ()
	{
		$this->expectException(MethodNotFoundException::class);
		Hook::filter('calculate.sum', StaticOperations::class, 'notfound');
	}

	/** @test */
	public function applyFilter ()
	{
		Hook::reset();

		Hook::filter('calculate.sum', 'fsum');
		Hook::filter('calculate.sub', 'fsub');
		Hook::filter('calculate.mul', 'fmul');
		Hook::filter('calculate.div', 'fdiv');
		
		$number = Hook::apply('calculate', 10);
		$this->assertSame($number, 22.5);
	}

	/** @test */
	public function removeFilter ()
	{
		Hook::removeFilter('calculate.sub');
		
		$number = Hook::apply('calculate', 10);
		$this->assertSame($number, 37.5);
	}

	/** @test */
	public function addFilter ()
	{
		Hook::filter('calculate.pow?2::1', 'fpow');
		
		$number = Hook::apply('calculate', 10, 2);
		$this->assertSame($number, 172.5);
	}

	/** @test */
	public function applyFilterByName ()
	{
		$number = Hook::applyByName('calculate', 'sum', 10);
		$this->assertSame($number, 25);
	}

	/** @test */
	public function applyFilterOnce ()
	{
		$number = Hook::applyOnce('calculate', 10, 2);
		$this->assertSame($number, 172.5);
	}

	/** @test */
	public function applyFilterNotFound ()
	{
		$number = Hook::apply('calculate', 10);
		$this->assertSame($number, 10);
	}
	/** @test */
	public function invalidAction ()
	{
		$this->expectException(ActionNotFoundException::class);
		Hook::action('calculate.sum', 'notfound');
	}

	/** @test */
	public function invalidActionMethod ()
	{
		$this->expectException(MethodNotFoundException::class);
		Hook::action('calculate.sum', StaticOperations::class, 'notfound');
	}
	
	/** @test */
	public function runAction ()
	{
		$this->expectOutputString(
			sprintf(
				"Your name: %s\nProgamming Language: %s\nI am line 02\nMessage: %s\n",
				'Alpha',
				'JS',
				'Peace and Love'
			)
		);

		Hook::reset();

		Hook::action('sentences.line', 'line');
		Hook::action('sentences.message', 'message');

		Hook::dispatch('sentences.name::1', 'name', 'Alpha');
		Hook::dispatch('sentences.prog::1', 'prog', 'JS');

		Hook::run('sentences', 'Peace and Love');
	}

	/** @test */
	public function removeAction ()
	{
		$this->expectOutputString(
			sprintf(
				"Your name: %s\nProgamming Language: %s\nMessage: %s\n",
				'Alpha',
				'JS',
				'Peace and Love'
			)
		);

		Hook::removeAction('sentences.line');
		Hook::run('sentences', 'Peace and Love');
	}

	/** @test */
	public function addAction ()
	{
		$this->expectOutputString(
			sprintf(
				"I am line 02\nYour name: %s\nProgamming Language: %s\nMessage: %s\n",
				'Alpha',
				'JS',
				'Peace and Love'
			)
		);

		Hook::action('sentences.line::0', 'line');
		Hook::run('sentences', 'Peace and Love');
	}

	/** @test */
	public function runActionByName ()
	{
		$this->expectOutputString(
			sprintf(
				"Message: %s\n",
				'Peace and Love'
			)
		);

		Hook::runByName('sentences', 'message', 'Peace and Love');
	}

	/** @test */
	public function runActionOnce ()
	{
		$this->expectOutputString(
			sprintf(
				"I am line 02\nYour name: %s\nProgamming Language: %s\nMessage: %s\n",
				'Alpha',
				'JS',
				'Peace and Love'
			)
		);

		Hook::runOnce('sentences', 'Peace and Love');
	}

	/** @test */
	public function runActionNotFound ()
	{
		$this->assertFalse(Hook::run('sentences', 'Peace and Love'));
	}
}