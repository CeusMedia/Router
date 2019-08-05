<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Resolver
 */
class ResolverTest extends TestCase
{
	protected function setUp()
	{
	}

	public function testAdd(){
		$this->markTestIncomplete();
	}

	public function testIndex(){
		$this->markTestIncomplete();
	}

	public function testIndexByController(){
		$this->markTestIncomplete();
	}

	public function testLoadFromJsonFile(){
		$this->markTestIncomplete();
	}

	public function testRemove(){
		$this->markTestIncomplete();
	}

	public function testAssembleJsonFileFromFolder(){
		$this->markTestIncomplete();
	}

	public function testGetModeFromString(){
		$registry	= new RegistryMock();

		$expected = Route::MODE_CONTROLLER;
		$actual	= $registry->getModeFromString_public( 'controller' );
		$this->assertEquals( $expected, $actual );

		$actual	= $registry->getModeFromString_public( 'Controller' );
		$this->assertEquals( $expected, $actual );

		$actual	= $registry->getModeFromString_public( 'CONTROLLER' );
		$this->assertEquals( $expected, $actual );

//		$this->markTestIncomplete();
	}
}
class RegistryMock extends Registry{
	public function getModeFromString_public( $mode ): int
	{
		return $this->getModeFromString( $mode );
	}
}
