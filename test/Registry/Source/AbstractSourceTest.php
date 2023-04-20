<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry\Source\AbstractSource;
use CeusMedia\Router\Registry\Source\JsonFile as JsonFileSource;
use CeusMedia\Router\Registry\Source\JsonFolder as JsonFolderSource;
use CeusMedia\Router\Registry\Source\SourceInterface;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Source\AbstractSource
 */
class AbstractSourceTest extends TestCase
{
	protected function setUp(): void
	{
	}

	protected function tearDown(): void
	{
	}

	public function testCreate()
	{
		$instance1	= JsonFileSource::create( 'resA' );
		$this->assertEquals( JsonFileSource::CLASS, get_class( $instance1 ) );
		$this->assertEquals( 'resA', $instance1->getResource() );

		$instance2	= JsonFolderSource::create( 'resB' );
		$this->assertEquals( JsonFolderSource::CLASS, get_class( $instance2 ) );
		$this->assertEquals( 'resB', $instance2->getResource() );

		$instance3	= JsonFileSource::create( 'resC' );
		$this->assertEquals( JsonFileSource::CLASS, get_class( $instance3 ) );
		$this->assertEquals( 'resC', $instance3->getResource() );

		$instance3	= JsonFileSource::create( 'resD', [
			SourceInterface::OPTION_AUTOLOAD	=> FALSE,
			SourceInterface::OPTION_AUTOSAVE	=> TRUE,
		] );
		$this->assertEquals( JsonFileSource::CLASS, get_class( $instance3 ) );
		$this->assertEquals( 'resD', $instance3->getResource() );
		$this->assertEquals( FALSE, $instance3->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		$this->assertEquals( TRUE, $instance3->getOption( SourceInterface::OPTION_AUTOSAVE ) );
	}

	public function testCreateException1()
	{
		$this->expectException( \RuntimeException::CLASS );
		AbstractSource::create();
	}

	public function testCreateException2()
	{
		$this->expectException( \InvalidArgumentException::CLASS );
		$instance3	= JsonFileSource::create( 'resD', [
			'invalid'	=> TRUE,
		] );
	}

	public function testGetOption()
	{
		$instance	= JsonFileSource::create( 'resA', [
			SourceInterface::OPTION_AUTOLOAD	=> TRUE,
			SourceInterface::OPTION_AUTOSAVE	=> TRUE,
		] );
		$this->assertEquals( TRUE, $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		$this->assertEquals( TRUE, $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );
		$this->assertEquals( NULL, $instance->getOption( 1024 ) );

		$instance	= JsonFileSource::create( 'resA', [
			SourceInterface::OPTION_AUTOLOAD	=> FALSE,
			SourceInterface::OPTION_AUTOSAVE	=> FALSE,
		] );
		$this->assertEquals( FALSE, $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		$this->assertEquals( FALSE, $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );

	}

	public function testSetOption()
	{
		$instance	= JsonFileSource::create( 'resA', [
			SourceInterface::OPTION_AUTOLOAD	=> TRUE,
			SourceInterface::OPTION_AUTOSAVE	=> TRUE,
		] );
		$instance->setOption( SourceInterface::OPTION_AUTOLOAD, FALSE );
		$instance->setOption( SourceInterface::OPTION_AUTOSAVE, FALSE );
		$this->assertEquals( FALSE, $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		$this->assertEquals( FALSE, $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );

		$instance->setOption( SourceInterface::OPTION_AUTOLOAD, NULL );
		$instance->setOption( SourceInterface::OPTION_AUTOSAVE, NULL );
		$this->assertEquals( NULL, $instance->getOption( SourceInterface::OPTION_AUTOLOAD ) );
		$this->assertEquals( NULL, $instance->getOption( SourceInterface::OPTION_AUTOSAVE ) );
	}
}
