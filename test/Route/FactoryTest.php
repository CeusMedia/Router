<?php
namespace CeusMedia\RouterTest\Route;

use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\Factory as RouteFactory;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Route\RouteFactory
 */
class FactoryTest extends TestCase
{
	protected RouteFactory $factory;

	protected function setUp(): void
	{
		$this->factory	= new RouteFactory();
	}

	public function testCreate(): void
	{
		$route	= $this->factory->create( '/' );
		self::assertSame( Route::class, get_class( $route ) );
		self::assertSame( '/', $route->getPattern() );
		self::assertSame( 'GET', $route->getMethod() );
		self::assertSame( Route::MODE_UNKNOWN, $route->getMode() );


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
		self::assertSame( Route::class, get_class( $route ) );
		self::assertSame( '/', $route->getPattern() );
		self::assertSame( 'POST', $route->getMethod() );
		self::assertSame( Route::MODE_CONTROLLER, $route->getMode() );
		self::assertSame( $controller, $route->getController() );
		self::assertSame( $action, $route->getAction() );
		self::assertSame( $roles, $route->getRoles() );
	}
}
