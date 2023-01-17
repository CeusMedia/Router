<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\Factory as RouteFactory;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Route\RouteFactory
 */
class FactoryTest extends TestCase
{
	protected $factory;

	protected function setUp(): void
	{
		$this->factory	= new RouteFactory();
	}

	public function testCreate()
	{
		$route	= $this->factory->create( '/' );
		$this->assertSame( Route::class, get_class( $route ) );
		$this->assertSame( '/', $route->getPattern() );
		$this->assertSame( 'GET', $route->getMethod() );
		$this->assertSame( Route::MODE_UNKNOWN, $route->getMode() );


		$controller	= 'Controller1';
		$action		= 'action1';
		$roles		= array( 'admin', 'manager' );
		$this->factory->setDefaultMode( Route::MODE_CONTROLLER );
		$this->factory->setDefaultMethod( 'POST' );
		$options	= [
			'controller'	=> $controller,
			'action'		=> $action,
			'roles'			=> $roles,
		];
		$route	= $this->factory->create( '/', $options );
		$this->assertSame( Route::class, get_class( $route ) );
		$this->assertSame( '/', $route->getPattern() );
		$this->assertSame( 'POST', $route->getMethod() );
		$this->assertSame( Route::MODE_CONTROLLER, $route->getMode() );
		$this->assertSame( $controller, $route->getController() );
		$this->assertSame( $action, $route->getAction() );
		$this->assertSame( $roles, $route->getRoles() );
	}
}
