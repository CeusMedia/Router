<?php
declare(strict_types=1);

namespace CeusMedia\RouterTest\Registry\Source;

use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\JsonFile as JsonFileSource;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Registry\Source\JsonFile
 */
class JsonFileTest extends TestCase
{
	protected string $jsonFile;

	protected function setUp(): void
	{
		$this->jsonFile	= __DIR__.'/../../JsonFileTest.routes.json';
	}

	protected function tearDown(): void
	{
	}

	public function testLoad(): void
	{
		$registry	= new Registry();
		$instance	= new JsonFileSource( 'invalidFileName' );
		self::assertEquals( -1, $instance->load( $registry ) );

		$instance	= new JsonFileSource( $this->jsonFile );
		self::assertEquals( 4, $instance->load( $registry ) );
	}

	public function testLoadRolesAndMeta(): void
	{
		$registry	= new Registry();
		$instance	= new JsonFileSource( __DIR__.'/../../JsonFileTest.meta.routes.json' );
		self::assertEquals( 3, $instance->load( $registry ) );

		$routes	= [];
		foreach( $registry->index() as $route )
			$routes[$route->getPattern()]	= $route;

		$login	= $routes['/auth/login'];
		self::assertTrue( $login->getMeta( 'public' ) );
		self::assertSame( 'auth', $login->getMeta( 'rateLimit' ) );
		self::assertSame( ['perMinute' => 10], $login->getMeta( 'limits' ), 'nested objects become arrays' );
		self::assertSame( [], $login->getRoles() );

		self::assertSame( ['setup', 'full'], $routes['/account']->getRoles(), 'roles as list' );
		self::assertSame( [], $routes['/account']->getMeta() );
		self::assertSame( ['admin', 'full'], $routes['/admin']->getRoles(), 'roles as comma separated string' );
	}

	public function testSave(): void
	{
		self::markTestIncomplete();
	}
}
