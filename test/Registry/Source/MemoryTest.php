<?php
declare(strict_types=1);

namespace CeusMedia\RouterTest\Registry\Source;

use CeusMedia\Router\Route;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\Memory as MemorySource;
use PHPUnit\Framework\TestCase;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Registry\Source\Memory
 */
class MemoryTest extends TestCase
{
	protected MemorySource $backend;
	protected Registry $registry;


	protected function setUp(): void
	{
		$this->registry	= new Registry();
		$this->backend	= new MemorySource( NULL, [
			1 => TRUE,
		] );
	}

	protected function tearDown(): void
	{
	}

	/**
	 *	@covers	::getOption
	 */
	public function testGetOption(): void
	{
		self::assertEquals( TRUE, $this->backend->getOption( 1 ) );
	}

	/**
	 *	@covers	::getOptions
	 */
	public function testGetOptions(): void
	{
		self::assertEquals( [1 => TRUE], $this->backend->getOptions() );
	}

	/**
	 *	@covers	::setOption
	 */
	public function testSetOption(): void
	{
		self::assertEquals( $this->backend, $this->backend->setOption( 1, FALSE ) );
		self::assertFalse( $this->backend->getOption( 1 ) );
	}

	/**
	 *	@covers	::load
	 */
	public function testLoad(): void
	{
		$route	= new Route('a' );
		self::assertEquals( [], $this->registry->index() );
		$this->backend->routes	= [$route];
		self::assertEquals( 1, $this->backend->load( $this->registry ) );
		self::assertEquals( [$route], array_values( $this->registry->index() ) );

		$key	= md5( 'GET::a' );
		self::assertEquals( [$key => $route], $this->registry->index() );
	}

	/**
	 *	@covers	::save
	 */
	public function testSave(): void
	{
		$route	= new Route('a' );
		$this->registry->add( $route );
		self::assertEquals( [$route], array_values( $this->registry->index() ) );
		self::assertEquals( [], $this->backend->routes );
		self::assertEquals( 1, $this->backend->save( $this->registry ) );
		self::assertEquals( [$route], array_values( $this->backend->routes ) );
	}
}
