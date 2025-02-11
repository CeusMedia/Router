<?php
declare(strict_types=1);

namespace CeusMedia\RouterTest;

use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\Factory as RouteFactory;
use RangeException;
use TypeError;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Route
 */
class RouteTest extends TestCase
{
	protected RouteFactory $factory;

	protected function setUp(): void
	{
		$this->factory	= new RouteFactory();
		$this->factory->setDefaultMethod( 'GET' );
	}

	/**
	 *	@covers	::getAction
	 */
	public function testGetAction(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( '', $route->getAction() );

		$route->setAction( 'test');
		self::assertSame( 'test', $route->getAction() );

		$route->setAction( 'TEST2');
		self::assertSame( 'TEST2', $route->getAction() );
	}

	/**
	 *	@covers	::getController
	 */
	public function testGetController(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( '', $route->getController() );

		$route->setController( 'Test');
		self::assertSame( 'Test', $route->getController() );

		$route->setController( 'TEST2');
		self::assertSame( 'TEST2', $route->getController() );
	}

	/**
	 *	@covers	::getMethod
	 */
	public function testGetMethod(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( 'GET', $route->getMethod() );

		$route->setMethod( 'POST');
		self::assertSame( 'POST', $route->getMethod() );

		$route->setMethod( 'GET,POST');
		self::assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( 'POST|GET');
		self::assertSame( 'POST,GET', $route->getMethod() );
	}

	/**
	 *	@covers	::getMode
	 */
	public function testGetMode(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( Route::MODE_UNKNOWN, $route->getMode() );

		$route->setMode( Route::MODE_CONTROLLER );
		self::assertSame( Route::MODE_CONTROLLER, $route->getMode() );

		$route->setMode( Route::MODE_EVENT );
		self::assertSame( Route::MODE_EVENT, $route->getMode() );

		$route->setMode( Route::MODE_FORWARD );
		self::assertSame( Route::MODE_FORWARD, $route->getMode() );
	}

	/**
	*	@covers	::getArguments
	*	@covers	::setArguments
	 */
	public function testGetArguments(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( [], $route->getArguments() );

		$arguments	= ['a' => 'a1', 'b' => 'b2'];
		$route->setArguments( $arguments );
		self::assertSame( $arguments, $route->getArguments() );
	}

	/**
	*	@covers	::getOrigin
	*	@covers	::setOrigin
	 */
	public function testGetOrigin(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( NULL, $route->getOrigin() );

		$route->setOrigin( $route );
		self::assertSame( $route, $route->getOrigin() );
	}

	/**
	 *	@covers	::getPattern
	 */
	public function testGetPattern(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( 'test', $route->getPattern() );

		$pattern	= '/_(test|TEST)+_/';
		$route->setPattern( $pattern );
		self::assertSame( $pattern, $route->getPattern() );
	}

	/**
	 *	@covers	::getRoles
	 */
	public function testGetRoles(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertSame( array(), $route->getRoles() );

		$roles	= array( 'role1', 'role2' );
		$route->setRoles( $roles );
		self::assertSame( $roles, $route->getRoles() );
	}

	/**
	 *	@covers	::isMethod
	 */
	public function testIsMethod(): void
	{
		$route	= $this->factory->create( 'test' );
		self::assertTrue( $route->isMethod( 'GET' ) );
		self::assertTrue( $route->isMethod( 'get' ) );
		self::assertFalse( $route->isMethod( 'POST' ) );

		$route->setMethod( 'POST' );
		self::assertTrue( $route->isMethod( 'POST' ) );
		self::assertTrue( $route->isMethod( 'post' ) );
		self::assertFalse( $route->isMethod( 'GET' ) );

		$route->setMethod( '*' );
		self::assertTrue( $route->isMethod( 'GET' ) );
		self::assertTrue( $route->isMethod( 'get' ) );
		self::assertTrue( $route->isMethod( 'POST' ) );
		self::assertTrue( $route->isMethod( 'post' ) );
		self::assertTrue( $route->isMethod( 'PUT' ) );
		self::assertTrue( $route->isMethod( 'DELETE' ) );
		self::assertTrue( $route->isMethod( 'HEAD' ) );
		self::assertTrue( $route->isMethod( 'OPTIONS' ) );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetAction(): void
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setController( 'SomethingElse' );
		self::assertIsObject( $result );
		self::assertSame( Route::class, get_class( $result ) );

		$route->setAction( 'TEST2');
		self::assertSame( 'TEST2', $route->getAction() );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnEmpty(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( '' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace1(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( ' ' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace2(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( ' methodName' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace3(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( 'methodName ' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace4(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( 'method Name' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsInvalidCharacter1(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( 'method-name' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetController(): void
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setController( 'SomethingElse' );
		self::assertIsObject( $result );
		self::assertSame( Route::class, get_class( $result ) );

		$route->setController( 'TEST2');
		self::assertSame( 'TEST2', $route->getController() );

		$route->setController( '\\Namespace\\ClassName' );
		self::assertSame( '\\Namespace\\ClassName', $route->getController() );

		$route->setController( '\\Namespace\\Package\\ClassName' );
		self::assertSame( '\\Namespace\\Package\\ClassName', $route->getController() );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnEmpty(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( '' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace1(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( ' ' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace2(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( ' ClassName' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace3(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( 'ClassName ' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace4(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( 'Class Name' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsInvalidCharacter1(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( 'class-name' );
	}

	/**
	 *	@covers	::setMethod
	 */
	public function testSetMethod(): void
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setMethod( 'GET' );
		self::assertIsObject( $result );
		self::assertSame( Route::class, get_class( $result ) );

		$route->setMethod( 'POST' );
		self::assertSame( 'POST', $route->getMethod() );

		$route->setMethod( ' POST' );
		self::assertSame( 'POST', $route->getMethod() );

		$route->setMethod( 'POST ' );
		self::assertSame( 'POST', $route->getMethod() );

		$route->setMethod( ',POST,' );
		self::assertSame( 'POST', $route->getMethod() );

		$route->setMethod( 'GET,POST' );
		self::assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( ',GET,POST,' );
		self::assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( ' GET , POST ' );
		self::assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( 'GET|POST' );
		self::assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( '|GET|POST|' );
		self::assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( ' GET | POST ' );
		self::assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( '*' );
		self::assertSame( '*', $route->getMethod() );

		$route->setMethod( ',*,' );
		self::assertSame( '*', $route->getMethod() );

		$route->setMethod( '|*|' );
		self::assertSame( '*', $route->getMethod() );

		$route->setMethod( '*,POST' );
		self::assertSame( '*', $route->getMethod() );

		$route->setMethod( ',*|POST|GET,' );
		self::assertSame( '*', $route->getMethod() );
	}

	/**
	 *	@covers	::setMethod
	 */
	public function testSetMethodExceptionOnInvalidMethod(): void
	{
		self::expectException( DomainException::class );
		$route	= $this->factory->create( 'test' );
		$route->setMethod( 'invalid' );
	}

	/**
	 *	@covers	::setMode
	 */
	public function testSetMode(): void
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setMode( Route::MODE_UNKNOWN );
		self::assertIsObject( $result );
		self::assertSame( Route::class, get_class( $result ) );
		self::assertSame( Route::MODE_UNKNOWN, $route->getMode() );

		$route->setMode( Route::MODE_CONTROLLER );
		self::assertSame( Route::MODE_CONTROLLER, $route->getMode() );

		$route->setMode( Route::MODE_EVENT );
		self::assertSame( Route::MODE_EVENT, $route->getMode() );

		$route->setMode( Route::MODE_FORWARD );
		self::assertSame( Route::MODE_FORWARD, $route->getMode() );
	}

	/**
	 *	@covers	::setMode
	 */
	public function testSetMode_invalidToUnknown(): void
	{
		self::expectException( RangeException::class );
		$route	= $this->factory->create( 'test' );
		$route->setMode( -15 );
	}

	/**
	 *	@covers	::setPattern
	 */
	public function testSetPattern(): void
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setPattern( '123' );
		self::assertIsObject( $result );
		self::assertSame( Route::class, get_class( $result ) );
		self::assertSame( '123', $route->getPattern() );

		$result	= $route->setPattern( '/path/to/some/action[/:action]' );
		self::assertIsObject( $result );
		self::assertSame( Route::class, get_class( $result ) );
		self::assertSame( '/path/to/some/action[/:action]', $route->getPattern() );

	}

	/**
	 *	@covers	::setPattern
	 */
	public function testSetPatternException1(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setPattern( ' 123' );
	}

	/**
	 *	@covers	::setPattern
	 */
	public function testSetPatternException2(): void
	{
		self::expectException( InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setPattern( '1 2 3' );
	}

	/**
	 *	@covers	::setRoles
	 */
	public function testSetRoles(): void
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setRoles( array() );
		self::assertIsObject( $result );
		self::assertSame( Route::class, get_class( $result ) );
		self::assertSame( array(), $route->getRoles() );

		$roles	= array( 'role1', 'role2' );
		$route->setRoles( $roles );
		self::assertSame( $roles, $route->getRoles() );
	}

	/**
	 *	@covers	::getModeFromKey
	 */
	public function testGetModeFromKey(): void
	{
		$expected = Route::MODE_CONTROLLER;
		self::assertEquals( $expected, Route::getModeFromKey( 'controller' ) );
		self::assertEquals( $expected, Route::getModeFromKey( 'Controller' ) );
		self::assertEquals( $expected, Route::getModeFromKey( 'CONTROLLER' ) );

		$expected = Route::MODE_EVENT;
		self::assertEquals( $expected, Route::getModeFromKey( 'event' ) );
		self::assertEquals( $expected, Route::getModeFromKey( 'Event' ) );
		self::assertEquals( $expected, Route::getModeFromKey( 'EVENT' ) );

		$expected = Route::MODE_FORWARD;
		self::assertEquals( $expected, Route::getModeFromKey( 'forward' ) );
		self::assertEquals( $expected, Route::getModeFromKey( 'Forward' ) );
		self::assertEquals( $expected, Route::getModeFromKey( 'FORWARD' ) );
	}

	/**
	 *	@covers	::getModeFromKey
	 */
	public function testGetModeFromKeyException(): void
	{
		self::expectException( RangeException::class );
		Route::getModeFromKey( 'invalid' );
	}
}

