<?php
use PHPUnit\Framework\TestCase;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\Factory as RouteFactory;
use CeusMedia\Router\Resolver;

/**
 *	@coversDefaultClass	\CeusMedia\Router\Resolver
 */
class ResolverTest extends TestCase
{
	protected $factory;
	protected $registry;
	protected $resolver;

	protected function setUp(): void
	{
		$this->factory	= new RouteFactory();
		$this->factory->setDefaultMethod( 'GET' );
		$this->registry	= new Registry();
		$this->resolver	= new Resolver( $this->registry );
	}

	public function testResolve(){
		$this->markTestIncomplete();
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function testGetRoutePatternParts_SinglePathOnly(){
		$route		= $this->factory->create( 'test' );
		$creation	= $this->resolver->getRoutePatternParts( $route );
		$assertion	= array(
			(object) array(
				'key'		=> 'test',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			)
		);
		$this->assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function testGetRoutePatternParts_DoublePathOnly(){
		$route		= $this->factory->create( 'test/path' );
		$creation	= $this->resolver->getRoutePatternParts( $route );
		$assertion	= array(
			(object) array(
				'key'		=> 'test',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			),
			(object) array(
				'key'		=> 'path',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			)
		);
		$this->assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function testGetRoutePatternParts_OneOptionalParam(){
		$route		= $this->factory->create( 'test/(:param)' );
		$creation	= $this->resolver->getRoutePatternParts( $route );
		$assertion	= array(
			(object) array(
				'key'		=> 'test',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			),
			(object) array(
				'key'		=> 'param',
				'optional'	=> TRUE,
				'argument'	=> TRUE,
			)
		);
		$this->assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function testGetRoutePatternParts_TwoOptionalParams(){
		$route		= $this->factory->create( 'test/(:param1)/(:param2)' );
		$creation	= $this->resolver->getRoutePatternParts( $route );
		$assertion	= array(
			(object) array(
				'key'		=> 'test',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			),
			(object) array(
				'key'		=> 'param1',
				'optional'	=> TRUE,
				'argument'	=> TRUE,
			),
			(object) array(
				'key'		=> 'param2',
				'optional'	=> TRUE,
				'argument'	=> TRUE,
			)
		);
		$this->assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function testGetRoutePatternParts_MixedParams(){
		$route		= $this->factory->create( 'test/:param1/(:param2)' );
		$creation	= $this->resolver->getRoutePatternParts( $route );
		$assertion	= array(
			(object) array(
				'key'		=> 'test',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			),
			(object) array(
				'key'		=> 'param1',
				'optional'	=> FALSE,
				'argument'	=> TRUE,
			),
			(object) array(
				'key'		=> 'param2',
				'optional'	=> TRUE,
				'argument'	=> TRUE,
			)
		);
		$this->assertEquals( $assertion, $creation );
	}

	/**
	 *	@covers	::getRoutePatternParts
	 */
	public function testGetRoutePatternParts_MixedPathAndParams(){
		$route		= $this->factory->create( 'test/path/:param1/(:param2)' );
		$creation	= $this->resolver->getRoutePatternParts( $route );
		$assertion	= array(
			(object) array(
				'key'		=> 'test',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			),
			(object) array(
				'key'		=> 'path',
				'optional'	=> FALSE,
				'argument'	=> FALSE,
			),
			(object) array(
				'key'		=> 'param1',
				'optional'	=> FALSE,
				'argument'	=> TRUE,
			),
			(object) array(
				'key'		=> 'param2',
				'optional'	=> TRUE,
				'argument'	=> TRUE,
			)
		);
		$this->assertEquals( $assertion, $creation );
	}
}
