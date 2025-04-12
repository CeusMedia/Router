<?php
declare(strict_types=1);

namespace CeusMedia\RouterTest\Route;

use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Route\PatternPart as RoutePatternPart;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Route\PatternPart
 */
class PatternPartTest extends TestCase
{
	/**
	 *	@covers	::create
	 */
	public function testCreate(): void
	{
		$part	= RoutePatternPart::create( 'key1', TRUE, TRUE, 'mixed' );
		self::assertEquals( 'key1', $part->key );
		self::assertTrue( $part->optional );
		self::assertTrue( $part->argument );
		self::assertEquals( 'mixed', $part->value );

		$part	= RoutePatternPart::create( 'key2', FALSE, FALSE, M_PI );
		self::assertEquals( 'key2', $part->key );
		self::assertFalse( $part->optional );
		self::assertFalse( $part->argument );
		self::assertEquals( M_PI, $part->value );
	}
}