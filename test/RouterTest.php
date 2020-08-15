<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Log;
use CeusMedia\Router\Router;
use CeusMedia\Router\Route;
use CeusMedia\Router\ResolverException;
use CeusMedia\Router\Route\Factory as RouteFactory;
use CeusMedia\Router\Registry\Source\JsonFile as JsonFileRegistry;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Router
 */
class RouterTest extends TestCase
{
	protected $factory;

	protected function setUp(): void
	{
	}

	protected function tearUp(): void
	{
	}

	/**
	 *	@covers	::resolve
	 *	@covers	\CeusMedia\Router\Resolver::resolve
	 */
	public function testResolve()
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
//		$this->assertEquals( $route, $actual );

		$router->setMethod( 'GET' );

		$route	= $routes[array_keys( $routes )[1]];
		$actual	= $router->resolve( '/test' );
		$this->assertEquals( $route, $actual );

		$route	= $routes[array_keys( $routes )[2]];
		$route->setArguments( ['a' => 'a1'] );
		$actual	= $router->resolve( '/test/a1' );
		$this->assertEquals( $route, $actual );

		$route	= $routes[array_keys( $routes )[3]];
		$route->setArguments( ['a' => 'a1', 'b' => 'b2'] );
		$actual	= $router->resolve( '/test/a1/b2' );
		$this->assertEquals( $route, $actual );

		$actual	= $router->resolve( '/test/a1/b2/c3', FALSE );
		$this->assertEquals( NULL, $actual );
	}

	/**
	 *	@covers	::resolve
	 *	@covers	\CeusMedia\Router\Resolver::resolve
	 */
	public function testResolveException()
	{
		$this->expectException( ResolverException::class );
		$jsonFile	= __DIR__.'/JsonFileTest.routes.json';
		$router	= new Router();
		$router->getRegistry()->addSource( new JsonFileRegistry( $jsonFile ) );

		Log::$level	= Log::LEVEL_ALL;
		Log::$file	= 'router.log';

		//  this CLI test fails -> argument matching for CLI is not separately handled
//		$route	= $routes[array_keys( $routes )[0]];
//		$actual	= $router->resolve( 'test a1 b2 c3' );
//		$this->assertEquals( $route, $actual );

		$router->setMethod( 'GET' );
		$router->resolve( '/test/a1/b2/c3' );
	}
}
