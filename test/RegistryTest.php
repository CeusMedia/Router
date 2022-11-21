<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source as RegistrySource;
use CeusMedia\Router\Route;

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
	public function testAdd()
	{
		$this->markTestIncomplete();
	}

	/**
	 *	@covers	::index
	 */
	public function testIndex()
	{
		$this->markTestIncomplete();
	}

	/**
	 *	@covers	::indexByController
	 */
	public function testIndexByController()
	{
		$this->markTestIncomplete();
	}

	/**
	 *	@covers	::loadFromJsonFile
	 */
	public function testLoadFromJsonFile()
	{
		$this->markTestIncomplete();
	}

	/**
	 *	@covers	::remove
	 */
	public function testRemove()
	{
		$this->markTestIncomplete();
	}

	/**
	 *	@covers	::assembleJsonFileFromFolder
	 */
	public function testAssembleJsonFileFromFolder()
	{
		$this->markTestIncomplete();
	}
}
class RegistryMock extends Registry{
}
