<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Registry
 */
class RegistryTest extends TestCase
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

	/**
	 *	@covers	::getModeFromString
	 */
	public function testGetModeFromString(){
		$registry	= new RegistryMock();

		$expected = Route::MODE_CONTROLLER;
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'controller' ) );
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'Controller' ) );
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'CONTROLLER' ) );

		$expected = Route::MODE_EVENT;
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'event' ) );
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'Event' ) );
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'EVENT' ) );

		$expected = Route::MODE_FORWARD;
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'forward' ) );
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'Forward' ) );
		$this->assertEquals( $expected, $registry->getModeFromString_public( 'FORWARD' ) );
	}

	/**
	 *	@covers	::getModeFromString
	 *	@expectedException \RangeException
	 */
	public function testGetModeFromStringException(){
		$registry	= new RegistryMock();
		$registry->getModeFromString_public( 'invalid' );
	}
}
class RegistryMock extends Registry{
	public function getModeFromString_public( $mode ): int
	{
		return $this->getModeFromString( $mode );
	}
}
