<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Registry
 */
class RegistryTest extends TestCase
{
	protected function setUp(): void
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
}
class RegistryMock extends Registry{
}
