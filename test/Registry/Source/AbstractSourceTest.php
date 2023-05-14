<?php
namespace CeusMedia\RouterTest\Registry\Source;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry\Source\AbstractSource;
use CeusMedia\Router\Registry\Source\JsonFile as JsonFileSource;
use CeusMedia\Router\Registry\Source\JsonFolder as JsonFolderSource;
use CeusMedia\Router\Registry\Source\SourceInterface;
use RuntimeException;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Registry\Source\AbstractSource
 */
class AbstractSourceTest extends TestCase
{
	protected function setUp(): void
	{
	}

	protected function tearDown(): void
	{
	}

	public function testCreate(): void
	{
		$instance1	= JsonFileSource::create( 'resA' );
		self::assertEquals( JsonFileSource::class, get_class( $instance1 ) );
		self::assertEquals( 'resA', $instance1->getResource() );

		$instance2	= JsonFolderSource::create( 'resB' );
		self::assertEquals( JsonFolderSource::class, get_class( $instance2 ) );
		self::assertEquals( 'resB', $instance2->getResource() );

		$instance3	= JsonFileSource::create( 'resC' );
		self::assertEquals( JsonFileSource::class, get_class( $instance3 ) );
		self::assertEquals( 'resC', $instance3->getResource() );

		$instance3	= JsonFileSource::create( 'resD', [
			SourceInterface::OPTION_AUTOLOAD	=> FALSE,
			SourceInterface::OPTION_AUTOSAVE	=> TRUE,
		] );
		self::assertEquals( JsonFileSource::class, get_class( $instance3 ) );
		self::assertEquals( 'resD', $instance3->getResource() );
		self::assertFalse( $instance3->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		self::assertTrue( $instance3->getOption( SourceInterface::OPTION_AUTOSAVE ) );
	}

	public function testCreateException1(): void
	{
		$this->expectException( RuntimeException::class );
		AbstractSource::create();
	}

	public function testCreateException_invalidOptionKey(): void
	{
		$this->expectException( InvalidArgumentException::class );
		JsonFileSource::create( 'resD', [
			'invalid'	=> TRUE,
		] );
	}

	public function testGetOption(): void
	{
		$instance	= JsonFileSource::create( 'resA', [
			SourceInterface::OPTION_AUTOLOAD	=> TRUE,
			SourceInterface::OPTION_AUTOSAVE	=> TRUE,
		] );
		self::assertTrue( $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		self::assertTrue( $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );
		self::assertEquals( NULL, $instance->getOption( 1024 ) );

		$instance	= JsonFileSource::create( 'resA', [
			SourceInterface::OPTION_AUTOLOAD	=> FALSE,
			SourceInterface::OPTION_AUTOSAVE	=> FALSE,
		] );
		self::assertFalse( $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		self::assertFalse( $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );

	}

	public function testSetOption(): void
	{
		$instance	= JsonFileSource::create( 'resA', [
			SourceInterface::OPTION_AUTOLOAD	=> TRUE,
			SourceInterface::OPTION_AUTOSAVE	=> TRUE,
		] );
		$instance->setOption( SourceInterface::OPTION_AUTOLOAD, FALSE );
		$instance->setOption( SourceInterface::OPTION_AUTOSAVE, FALSE );
		self::assertFalse( $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		self::assertFalse( $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );

		$instance->setOption( SourceInterface::OPTION_AUTOLOAD );
		$instance->setOption( SourceInterface::OPTION_AUTOSAVE );
		self::assertEquals( NULL, $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		self::assertEquals( NULL, $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );
	}
}
