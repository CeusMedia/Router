<?php
declare(strict_types=1);

namespace CeusMedia\RouterTest;

use CeusMedia\Router\Route;
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Log;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Router;
use CeusMedia\Router\ResolverException;
use CeusMedia\Router\Registry\Source\JsonFile as JsonFileRegistry;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Router
 */
class RouterTest extends TestCase
{
	protected function setUp(): void
	{
	}

	protected function tearUp(): void
	{
	}

	/**
	 * @covers    ::resolve
	 * @covers    \CeusMedia\Router\Resolver::resolve
	 * @throws ResolverException
	 */
	public function testResolve(): void
	{
		$jsonFile	= __DIR__.'/JsonFileTest.routes.json';
		$router	= new Router();
		$router->getRegistry()->addSource( new JsonFileRegistry( $jsonFile ) );
		$routes	= $router->getRoutes();

		Log::$level	= Log::LEVEL_ALL;
		Log::$file	= 'router.log';

		//  this CLI test fails -> argument matching for CLI is not separately handled
//		$route	= $routes[array_keys( $routes )[0]];
//		$actual	= $router->resolve( 'test a1 b2' );
//		self::assertEquals( $route, $actual );

		$router->setMethod( 'GET' );

		$route	= $routes[array_keys( $routes )[1]];
		$actual	= $router->resolve( '/test' );
		self::assertEquals( $route, $actual );

		$route	= $routes[array_keys( $routes )[2]];
		$route->setArguments( ['a' => 'a1'] );
		$actual	= $router->resolve( '/test/a1' );
		self::assertEquals( $route, $actual );

		$route	= $routes[array_keys( $routes )[3]];
		$route->setArguments( ['a' => 'a1', 'b' => 'b2'] );
		$actual	= $router->resolve( '/test/a1/b2' );
		self::assertEquals( $route, $actual );

		$actual	= $router->resolve( '/test/a1/b2/c3', FALSE );
		self::assertEquals( NULL, $actual );
	}

	/**
	 *	@covers	::resolve
	 *	@covers	\CeusMedia\Router\Resolver::resolve
	 */
	public function testResolveException(): void
	{
		self::expectException( ResolverException::class );
		$jsonFile	= __DIR__.'/JsonFileTest.routes.json';
		$router	= new Router();
		$router->getRegistry()->addSource( new JsonFileRegistry( $jsonFile ) );

		Log::$level	= Log::LEVEL_ALL;
		Log::$file	= 'router.log';

		//  this CLI test fails -> argument matching for CLI is not separately handled
//		$route	= $routes[array_keys( $routes )[0]];
//		$actual	= $router->resolve( 'test a1 b2 c3' );
//		self::assertEquals( $route, $actual );

		$router->setMethod( 'GET' );
		$router->resolve( '/test/a1/b2/c3' );
	}

	/**
	 *	Regression: loading routes rewrote the JSON file (autosave on every added route),
	 *	losing roles and meta.
	 *	@covers	::loadRoutesFromJsonFile
	 */
	public function testLoadRoutesFromJsonFile(): void
	{
		$tempFile	= (string) tempnam( sys_get_temp_dir(), 'RouterTest' );
		try{
			copy( __DIR__.'/JsonFileTest.meta.routes.json', $tempFile );
			$original	= file_get_contents( $tempFile );

			$router	= new Router();
			$router->loadRoutesFromJsonFile( $tempFile );
			self::assertCount( 3, $router->getRoutes() );
			self::assertSame( $original, file_get_contents( $tempFile ), 'File must not be rewritten on load' );

			//  autosave on adding a route keeps roles and meta of loaded routes
			$router->addRoute( new Route( '/new', 'GET' ) );
			$reloaded	= new Router();
			$reloaded->loadRoutesFromJsonFile( $tempFile );
			self::assertEquals( $router->getRoutes(), $reloaded->getRoutes() );

			$routes	= [];
			foreach( $reloaded->getRoutes() as $route )
				$routes[$route->getPattern()]	= $route;
			self::assertSame( 'auth', $routes['/auth/login']->getMeta( 'rateLimit' ) );
			self::assertSame( ['perMinute' => 10], $routes['/auth/login']->getMeta( 'limits' ) );
			self::assertSame( ['setup', 'full'], $routes['/account']->getRoles() );
		}
		finally{
			@unlink( $tempFile );
		}
	}

	/**
	 *	@covers	::create
	 */
	public function testCreate(): void
	{
		$instance	= Router::create();
		self::assertEquals( Router::class, get_class( $instance ) );
		self::assertEquals( Registry::class, get_class( $instance->getRegistry() ) );
	}

	/**
	 *	@covers	::addRoute
	 */
	public function testAddRoute(): void
	{
		$route		= new Route( '/test', 'head' );
		$instance	= Router::create();
		$instance->addRoute( $route );
		self::assertEquals( [$route], array_values( $instance->getRoutes() ) );
	}
}
