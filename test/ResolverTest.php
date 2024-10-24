<?php /** @noinspection PhpRedundantOptionalArgumentInspection */
declare(strict_types=1);

namespace CeusMedia\RouterTest;

use CeusMedia\Router\ResolverException;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\PatternPart;
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route\Factory as RouteFactory;
use CeusMedia\Router\Resolver;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Resolver
 */
class ResolverTest extends TestCase
{
	protected RouteFactory $factoryCli;
	protected RouteFactory $factoryWeb;
	protected Registry $registry;
	protected Resolver $resolver;

	public function test_hasRouteForPath(): void
	{
		$this->registry->add( new Route( 'test :a (:b)', 'CLI' ) );
		$this->registry->add( new Route( '/test/:a/(:b)', 'GET' ) );

		self::assertFalse( $this->resolver->hasRouteForPath( 'test', 'CLI' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( 'test a1', 'CLI' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( 'test a1 b1', 'CLI' ) );

		self::assertFalse( $this->resolver->hasRouteForPath( 'test', 'GET' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( 'test/a1', 'GET' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( 'test/a1/b1', 'GET' ) );
	}

	public function test_resolve_exceptionCli(): void
	{
		$this->expectException( ResolverException::class );
		$this->registry->add( new Route( 'test :a (:b)', 'CLI' ) );
		$this->resolver->resolve( 'test', 'CLI' );
	}

	public function test_resolve_exceptionWeb(): void
	{
		$this->expectException( ResolverException::class );
		$this->registry->add( new Route( '/test/:a/(:b)', 'GET' ) );
		$this->resolver->resolve( 'test', 'GET' );
	}

	public function test_resolve(): void
	{
		$this->registry->add( new Route( 'test :a (:b)', 'CLI' ) );
		$this->registry->add( new Route( '/test/:a/(:b)', 'GET' ) );

		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( 'test', 'CLI', FALSE );
		self::assertIsNotObject( $route, 'Route was resolvable' );

		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( 'test a1', 'CLI', FALSE );
		self::assertIsObject( $route, 'Route not resolved' );
		self::assertEquals( ['a' => 'a1', 'b' => NULL], $route->getArguments() );

		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( 'test a1 b1', 'CLI', FALSE );
		self::assertIsObject( $route );
		self::assertEquals( ['a' => 'a1', 'b' => 'b1'], $route->getArguments() );


		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( '/test', 'GET', FALSE );
		self::assertIsNotObject( $route, 'Route was resolvable' );

		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( '/test/a1', 'GET', FALSE );
		self::assertIsObject( $route, 'Route not resolved' );
		self::assertEquals( ['a' => 'a1', 'b' => NULL], $route->getArguments() );

		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( '/test/a1/b1', 'GET', FALSE );
		self::assertIsObject( $route, 'Route not resolved' );
		self::assertEquals( ['a' => 'a1', 'b' => 'b1'], $route->getArguments() );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_CLI_SinglePathOnly(): void
	{
		$route		= $this->factoryCli->create( 'test' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_CLI_OneOptionalParam(): void
	{
		$route		= $this->factoryCli->create( 'test (:param)' );
		$route->setMethod( 'CLI' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
			PatternPart::create( 'param', TRUE, TRUE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_Web_SinglePathOnly(): void
	{
		$route		= $this->factoryWeb->create( 'test' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_Web_DoublePathOnly(): void
	{
		$route		= $this->factoryWeb->create( 'test/path' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
			PatternPart::create( 'path', FALSE, FALSE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_Web_OneOptionalParam(): void
	{
		$route		= $this->factoryWeb->create( 'test/(:param)' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
			PatternPart::create( 'param', TRUE, TRUE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_Web_TwoOptionalParams(): void
	{
		$route		= $this->factoryWeb->create( 'test/(:param1)/(:param2)' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
			PatternPart::create( 'param1', TRUE, TRUE ),
			PatternPart::create( 'param2', TRUE, TRUE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_Web_MixedParams(): void
	{
		$route		= $this->factoryWeb->create( 'test/:param1/(:param2)' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
			PatternPart::create( 'param1', FALSE, TRUE ),
			PatternPart::create( 'param2', TRUE, TRUE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function test_getRoutePatternParts_Web_MixedPathAndParams(): void
	{
		$route		= $this->factoryWeb->create( 'test/path/:param1/(:param2)' );
		$creation	= Resolver::getRoutePatternParts( $route );
		$assertion	= [
			PatternPart::create( 'test', FALSE, FALSE ),
			PatternPart::create( 'path', FALSE, FALSE ),
			PatternPart::create( 'param1', FALSE, TRUE ),
			PatternPart::create( 'param2', TRUE, TRUE ),
		];
		self::assertEquals( $assertion, $creation );
	}

	protected function setUp(): void
	{
		$this->factoryCli	= new RouteFactory();
		$this->factoryCli->setDefaultMethod( 'CLI' );
		$this->factoryWeb	= new RouteFactory();
		$this->factoryWeb->setDefaultMethod( 'GET' );
		$this->registry	= new Registry();
		$this->resolver	= new Resolver( $this->registry );
	}
}
