<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\Factory as RouteFactory;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Route
*/
class RouteTest extends TestCase
{
	protected function setUp()
	{
		$this->factory	= new RouteFactory();
		$this->factory->setDefaultMethod( 'GET' );
		$this->factory->setDefaultMode( Route::MODE_CONTROLLER );
	}

	/**
	 *	@covers	::getAction
	 */
	public function testGetAction()
	{
		$route	= $this->factory->create( 'test' );
		$this->assertSame( NULL, $route->getAction() );

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
		$this->assertSame( NULL, $route->getController() );

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
	 *	@covers	::getOrigin
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
		$route	= new Route( 'Test', 'test', 'test', 'GET' );
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
		$result	= $route->setController( 'GET' );
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
		$this->expectException( \RangeException::class );
		$route	= $this->factory->create( 'test' );
		$route->setMethod( 'invalid' );
	}

	/**
	 *	@covers	::setPattern
	 */
	public function testSetPattern()
	{
		$route	= new Route( 'Test', 'test', 'test', 'GET' );
		$result	= $route->setPattern( '123' );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );
		$this->assertSame( '123', $route->getPattern() );

		$route->setPattern( ' 123' );
		$this->assertSame( '123', $route->getPattern() );

		$route->setPattern( '123 ' );
		$this->assertSame( '123', $route->getPattern() );

		$route->setPattern( ' 1 2 3 ' );
		$this->assertSame( '123', $route->getPattern() );
	}

	/**
	 *	@covers	::setRoles
	 */
	public function testSetRoles()
	{
		$route	= new Route( 'Test', 'test', 'test', 'GET' );
		$result	= $route->setRoles( array() );
		$this->assertTrue( is_object( $result ) );
		$this->assertSame( Route::class, get_class( $result ) );
		$this->assertSame( array(), $route->getRoles() );

		$roles	= array( 'role1', 'role2' );
		$route->setRoles( $roles );
		$this->assertSame( $roles, $route->getRoles() );
	}
}
