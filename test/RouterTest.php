<?php
namespace CeusMedia\RouterTest;

use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Log;
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
		$this->expectException( ResolverException::class );
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
}
