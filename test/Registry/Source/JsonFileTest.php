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

	public function testSave(): void
	{
		self::markTestIncomplete();
	}
}
