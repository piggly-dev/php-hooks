<?php
namespace Piggly\Tests\Hooks;

use PHPUnit\Framework\TestCase;
use Piggly\Hooks\Exceptions\InvalidSyntaxException;
use Piggly\Hooks\Syntax;

class SyntaxTest extends TestCase
{
	/** @test */
	public function isReturningValidArray ()
	{
		$tags = Syntax::create('tagname', 'functioname', 12, 2);

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'name' => 'functioname',
				'args' => 2,
				'priority' => 12
			]
		);
	}

	/** @test */
	public function canReadTag ()
	{
		$tags = Syntax::read('tagname');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname'
			]
		);
	}

	/** @test */
	public function canReadTagAndPriority ()
	{
		$tags = Syntax::read('tagname::12');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'priority' => 12
			]
		);
	}

	/** @test */
	public function canReadTagAndArgs ()
	{
		$tags = Syntax::read('tagname?2');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'args' => 2
			]
		);
	}

	/** @test */
	public function canReadTagArgsAndPriority ()
	{
		$tags = Syntax::read('tagname?2::12');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'args' => 2,
				'priority' => 12
			]
		);
	}

	/** @test */
	public function canReadTagAndName ()
	{
		$tags = Syntax::read('tagname.functionname');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'name' => 'functionname'
			]
		);
	}

	/** @test */
	public function canReadTagNameAndPriority ()
	{
		$tags = Syntax::read('tagname.functionname::20');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'name' => 'functionname',
				'priority' => 20
			]
		);
	}

	/** @test */
	public function canReadTagNameAndArgs ()
	{
		$tags = Syntax::read('tagname.functionname?2');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'name' => 'functionname',
				'args' => 2
			]
		);
	}

	/** @test */
	public function canReadTagNameArgsAndPriority ()
	{
		$tags = Syntax::read('tagname.functionname?2::20');

		$this->assertSame(
			$tags,
			[
				'tag' => 'tagname',
				'name' => 'functionname',
				'args' => 2,
				'priority' => 20
			]
		);
	}

	/** @test */
	public function invalidSyntaxPriority ()
	{
		$this->expectException(InvalidSyntaxException::class);
		Syntax::read('tagname::invalidpriority');
	}

	/** @test */
	public function invalidSyntaxArgs ()
	{
		$this->expectException(InvalidSyntaxException::class);
		Syntax::read('tagname?notanarg');
	}

	/** @test */
	public function invalidSyntaxAny ()
	{
		$this->expectException(InvalidSyntaxException::class);
		Syntax::read('tag::name.invalid?name');
	}
}