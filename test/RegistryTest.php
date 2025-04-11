<?php
declare(strict_types=1);

namespace CeusMedia\RouterTest;

use CeusMedia\Router\Route;
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Registry
 */
class RegistryTest extends TestCase
{
	protected function setUp(): void
	{
	}

	protected function tearDown(): void
	{
	}

	/**
	 *	@covers	::add
	 */
	public function testAdd(): void
	{
		self::markTestIncomplete();
	}

	/**
	 *	@covers	::index
	 */
	public function testIndex(): void
	{
		self::markTestIncomplete();
	}

	/**
	 *	@covers	::indexByController
	 */
	public function testIndexByController(): void
	{
		$registry	= new Registry();
		$routeTest	= new Route( 'test', 'CLI', Route::MODE_EVENT );
		$memory		= new Registry\Source\Memory();
		$memory->routes	= [
			$routeTest,
		];
		self::assertEquals( 1, $memory->load( $registry ) );

		$index	= $registry->index();
		self::assertIsArray( $index );

		$firstRouteOfRegistry = current( array_values( $index ) );
		self::assertSame( $routeTest, $firstRouteOfRegistry );

		$index	= $registry->indexByController( 'test' );
		self::assertEquals( $routeTest, $firstRouteOfRegistry );
	}

	/**
	 *	@covers	::loadFromJsonFile
	 */
	public function testLoadFromJsonFile(): void
	{
		self::markTestIncomplete();
	}

	/**
	 *	@covers	::remove
	 */
	public function testRemove(): void
	{
		self::markTestIncomplete();
	}

	/**
	 *	@covers	::assembleJsonFileFromFolder
	 */
	public function testAssembleJsonFileFromFolder(): void
	{
		self::markTestIncomplete();
	}
}
