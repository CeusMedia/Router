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
		self::expectException( ResolverException::class );
		$this->registry->add( new Route( 'test :a (:b)', 'CLI' ) );
		$this->resolver->resolve( 'test', 'CLI' );
	}

	public function test_resolve_exceptionWeb(): void
	{
		self::expectException( ResolverException::class );
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
	 *	Regression: static parts were used as regular expression, so a dot matched any character.
	 *	@covers	::resolve
	 */
	public function test_resolve_staticPartIsNotRegExp(): void
	{
		$this->registry->add( new Route( '/files/a.json', 'GET' ) );
		$this->registry->add( new Route( 'db.dump', 'CLI' ) );

		self::assertFalse( $this->resolver->hasRouteForPath( '/files/aXjson', 'GET' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( '/files/a.json', 'GET' ) );
		self::assertFalse( $this->resolver->hasRouteForPath( 'dbXdump', 'CLI' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( 'db.dump', 'CLI' ) );
	}

	/**
	 *	Regression: static parts containing regex characters were not resolvable.
	 *	@covers	::resolve
	 */
	public function test_resolve_staticPartWithRegExpCharacters(): void
	{
		$this->registry->add( new Route( '/a+b/(:c)', 'GET' ) );
		$this->registry->add( new Route( '/mail/user@example.org', 'GET' ) );

		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( '/a+b/c1', 'GET', FALSE );
		self::assertIsObject( $route, 'Route not resolved' );
		self::assertEquals( ['c' => 'c1'], $route->getArguments() );
		self::assertFalse( $this->resolver->hasRouteForPath( '/aab/c1', 'GET' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( '/mail/user@example.org', 'GET' ) );
	}

	/**
	 *	Regression: a mismatch on the last pattern part was not detected.
	 *	@covers	::resolve
	 */
	public function test_resolve_mismatchOnLastPart(): void
	{
		$this->registry->add( new Route( '/a/(:b)/c', 'GET' ) );

		/** @noinspection PhpUnhandledExceptionInspection */
		self::assertNull( $this->resolver->resolve( '/a/c', 'GET', FALSE ) );

		/** @noinspection PhpUnhandledExceptionInspection */
		$route	= $this->resolver->resolve( '/a/b1/c', 'GET', FALSE );
		self::assertIsObject( $route, 'Route not resolved' );
		self::assertEquals( ['b' => 'b1'], $route->getArguments() );
	}

	/**
	 *	@covers	::resolve
	 */
	public function test_resolve_endingSlashIsOptional(): void
	{
		$this->registry->add( new Route( '/list/', 'GET' ) );

		self::assertTrue( $this->resolver->hasRouteForPath( '/list/', 'GET' ) );
		self::assertTrue( $this->resolver->hasRouteForPath( '/list', 'GET' ) );
		self::assertFalse( $this->resolver->hasRouteForPath( '/lists', 'GET' ) );
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
