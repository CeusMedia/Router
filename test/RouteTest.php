<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\Factory as RouteFactory;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Route
 */
class RouteTest extends TestCase
{
	protected $factory;

	protected function setUp(): void
	{
		$this->factory	= new RouteFactory();
		$this->factory->setDefaultMethod( 'GET' );
	}

	/**
	 *	@covers	::getAction
	 */
	public function testGetAction()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( '', $route->getAction() );

		$route->setAction( 'test');
		$this->assertSame( 'test', $route->getAction() );

		$route->setAction( 'TEST2');
		$this->assertSame( 'TEST2', $route->getAction() );
	}

	/**
	 *	@covers	::getController
	 */
	public function testGetController()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( '', $route->getController() );

		$route->setController( 'Test');
		$this->assertSame( 'Test', $route->getController() );

		$route->setController( 'TEST2');
		$this->assertSame( 'TEST2', $route->getController() );
	}

	/**
	 *	@covers	::getMethod
	 */
	public function testGetMethod()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( 'GET', $route->getMethod() );

		$route->setMethod( 'POST');
		$this->assertSame( 'POST', $route->getMethod() );

		$route->setMethod( 'GET,POST');
		$this->assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( 'POST|GET');
		$this->assertSame( 'POST,GET', $route->getMethod() );
	}

	/**
	 *	@covers	::getMode
	 */
	public function testGetMode()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( Route::MODE_UNKNOWN, $route->getMode() );

		$route->setMode( Route::MODE_CONTROLLER );
		$this->assertSame( Route::MODE_CONTROLLER, $route->getMode() );

		$route->setMode( Route::MODE_EVENT );
		$this->assertSame( Route::MODE_EVENT, $route->getMode() );

		$route->setMode( Route::MODE_FORWARD );
		$this->assertSame( Route::MODE_FORWARD, $route->getMode() );
	}

	/**
	*	@covers	::getArguments
	*	@covers	::setArguments
	 */
	public function testGetArguments()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( [], $route->getArguments() );

		$arguments	= ['a' => 'a1', 'b' => 'b2'];
		$route->setArguments( $arguments );
		$this->assertSame( $arguments, $route->getArguments() );
	}

	/**
	*	@covers	::getOrigin
	*	@covers	::setOrigin
	 */
	public function testGetOrigin()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( NULL, $route->getOrigin() );

		$route->setOrigin( $route );
		$this->assertSame( $route, $route->getOrigin() );
	}

	/**
	 *	@covers	::getPattern
	 */
	public function testGetPattern()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( 'test', $route->getPattern() );

		$pattern	= '/_(test|TEST)+_/';
		$route->setPattern( $pattern );
		$this->assertSame( $pattern, $route->getPattern() );
	}

	/**
	 *	@covers	::getRoles
	 */
	public function testGetRoles()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( array(), $route->getRoles() );

		$roles	= array( 'role1', 'role2' );
		$route->setRoles( $roles );
		$this->assertSame( $roles, $route->getRoles() );
	}

	/**
	 *	@covers	::isMethod
	 */
	public function testIsMethod()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertTrue( $route->isMethod( 'GET' ) );
		$this->assertTrue( $route->isMethod( 'get' ) );
		$this->assertFalse( $route->isMethod( 'POST' ) );

		$route->setMethod( 'POST' );
		$this->assertTrue( $route->isMethod( 'POST' ) );
		$this->assertTrue( $route->isMethod( 'post' ) );
		$this->assertFalse( $route->isMethod( 'GET' ) );

		$route->setMethod( '*' );
		$this->assertTrue( $route->isMethod( 'GET' ) );
		$this->assertTrue( $route->isMethod( 'get' ) );
		$this->assertTrue( $route->isMethod( 'POST' ) );
		$this->assertTrue( $route->isMethod( 'post' ) );
		$this->assertTrue( $route->isMethod( 'PUT' ) );
		$this->assertTrue( $route->isMethod( 'DELETE' ) );
		$this->assertTrue( $route->isMethod( 'HEAD' ) );
		$this->assertTrue( $route->isMethod( 'OPTIONS' ) );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetAction()
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setController( 'SomethingElse' );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );

		$route->setAction( 'TEST2');
		$this->assertSame( 'TEST2', $route->getAction() );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnEmpty()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( '' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace1()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( ' ' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace2()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( ' methodName' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace3()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( 'methodName ' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsWhitespace4()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( 'method Name' );
	}

	/**
	 *	@covers	::setAction
	 */
	public function testSetActionExceptionOnContainsInvalidCharacter1()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setAction( 'method-name' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetController()
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setController( 'SomethingElse' );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );

		$route->setController( 'TEST2');
		$this->assertSame( 'TEST2', $route->getController() );

		$route->setController( '\\Namespace\\ClassName' );
		$this->assertSame( '\\Namespace\\ClassName', $route->getController() );

		$route->setController( '\\Namespace\\Package\\ClassName' );
		$this->assertSame( '\\Namespace\\Package\\ClassName', $route->getController() );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnEmpty()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( '' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace1()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( ' ' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace2()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( ' ClassName' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace3()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( 'ClassName ' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsWhitespace4()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( 'Class Name' );
	}

	/**
	 *	@covers	::setController
	 */
	public function testSetControllerExceptionOnContainsInvalidCharacter1()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setController( 'class-name' );
	}

	/**
	 *	@covers	::setMethod
	 */
	public function testSetMethod()
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setMethod( 'GET' );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );

		$route->setMethod( 'POST' );
		$this->assertSame( 'POST', $route->getMethod() );

		$route->setMethod( ' POST' );
		$this->assertSame( 'POST', $route->getMethod() );

		$route->setMethod( 'POST ' );
		$this->assertSame( 'POST', $route->getMethod() );

		$route->setMethod( ',POST,' );
		$this->assertSame( 'POST', $route->getMethod() );

		$route->setMethod( 'GET,POST' );
		$this->assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( ',GET,POST,' );
		$this->assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( ' GET , POST ' );
		$this->assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( 'GET|POST' );
		$this->assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( '|GET|POST|' );
		$this->assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( ' GET | POST ' );
		$this->assertSame( 'GET,POST', $route->getMethod() );

		$route->setMethod( '*' );
		$this->assertSame( '*', $route->getMethod() );

		$route->setMethod( ',*,' );
		$this->assertSame( '*', $route->getMethod() );

		$route->setMethod( '|*|' );
		$this->assertSame( '*', $route->getMethod() );

		$route->setMethod( '*,POST' );
		$this->assertSame( '*', $route->getMethod() );

		$route->setMethod( ',*|POST|GET,' );
		$this->assertSame( '*', $route->getMethod() );
	}

	/**
	 *	@covers	::setMethod
	 */
	public function testSetMethodExceptionOnInvalidMethod()
	{
		$this->expectException( \DomainException::class );
		$route	= $this->factory->create( 'test' );
		$route->setMethod( 'invalid' );
	}

	/**
	 *	@covers	::setMode
	 */
	public function testSetMode()
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setMode( Route::MODE_UNKNOWN );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );
		$this->assertSame( Route::MODE_UNKNOWN, $route->getMode() );

		$route->setMode( Route::MODE_CONTROLLER );
		$this->assertSame( Route::MODE_CONTROLLER, $route->getMode() );

		$route->setMode( Route::MODE_EVENT );
		$this->assertSame( Route::MODE_EVENT, $route->getMode() );

		$route->setMode( Route::MODE_FORWARD );
		$this->assertSame( Route::MODE_FORWARD, $route->getMode() );
	}

	/**
	 *	@covers	::setMode
	 */
	public function testSetModeExceptionOnInvalidMode()
	{
		$this->expectException( \TypeError::class );
		$route	= $this->factory->create( 'test' );
		$route->setMode( 'invalid' );
	}

	/**
	 *	@covers	::setPattern
	 */
	public function testSetPattern()
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setPattern( '123' );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );
		$this->assertSame( '123', $route->getPattern() );

		$result	= $route->setPattern( '/path/to/some/action[/:action]' );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );
		$this->assertSame( '/path/to/some/action[/:action]', $route->getPattern() );

	}

	/**
	 *	@covers	::setPattern
	 */
	public function testSetPatternException1()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setPattern( ' 123' );
	}

	/**
	 *	@covers	::setPattern
	 */
	public function testSetPatternException2()
	{
		$this->expectException( \InvalidArgumentException::class );
		$route	= $this->factory->create( 'test' );
		$route->setPattern( '1 2 3' );
	}

	/**
	 *	@covers	::setRoles
	 */
	public function testSetRoles()
	{
		$route	= $this->factory->create( 'test' );
		$result	= $route->setRoles( array() );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );
		$this->assertSame( array(), $route->getRoles() );

		$roles	= array( 'role1', 'role2' );
		$route->setRoles( $roles );
		$this->assertSame( $roles, $route->getRoles() );
	}

	/**
	 *	@covers	::getModeFromKey
	 */
	public function testGetModeFromKey(){
		$route	= $this->factory->create( 'test' );

		$expected = Route::MODE_CONTROLLER;
		$this->assertEquals( $expected, $route->getModeFromKey( 'controller' ) );
		$this->assertEquals( $expected, $route->getModeFromKey( 'Controller' ) );
		$this->assertEquals( $expected, $route->getModeFromKey( 'CONTROLLER' ) );

		$expected = Route::MODE_EVENT;
		$this->assertEquals( $expected, $route->getModeFromKey( 'event' ) );
		$this->assertEquals( $expected, $route->getModeFromKey( 'Event' ) );
		$this->assertEquals( $expected, $route->getModeFromKey( 'EVENT' ) );

		$expected = Route::MODE_FORWARD;
		$this->assertEquals( $expected, $route->getModeFromKey( 'forward' ) );
		$this->assertEquals( $expected, $route->getModeFromKey( 'Forward' ) );
		$this->assertEquals( $expected, $route->getModeFromKey( 'FORWARD' ) );
	}

	/**
	 *	@covers	::getModeFromKey
	 *	@expectedException \RangeException
	 */
	public function testGetModeFromKeyException(){
		$this->expectException( \RangeException::class );
		$route	= $this->factory->create( 'test' );
		$route->getModeFromKey( 'invalid' );
	}
}
class RouteMock extends Route{
}
