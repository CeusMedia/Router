<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\JsonFile as JsonFileSource;
use CeusMedia\Router\Registry\Source\SourceInterface;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Source\JsonFile
 */
class JsonFileTest extends TestCase
{
	protected function setUp(): void
	{
		$this->jsonFile	= __DIR__.'/../../JsonFileTest.routes.json';
	}

	protected function tearDown(): void
	{
	}

	public function testLoad()
	{
		$registry	= new Registry();
		$instance	= new JsonFileSource( 'invalidFileName' );
		$this->assertEquals( -1, $instance->load( $registry ) );

		$instance	= new JsonFileSource( $this->jsonFile );
		$this->assertEquals( 4, $instance->load( $registry ) );
	}

	public function testSave()
	{
		$this->markTestIncomplete();
	}
}
