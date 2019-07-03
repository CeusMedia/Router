<?php
class RouteTest extends PHPUnit\Framework\TestCase{

	protected function setUp(): void
	{
		$this->route	= new CeusMedia\Router\Route( 'Test', 'test', 'test', 'GET' );
	}

	public function testIsMethod(): void
	{
		$this->assertTrue( $this->route->isMethod( "GET" ) );
		$this->assertTrue( $this->route->isMethod( "get" ) );
		$this->assertFalse( $this->route->isMethod( "POST" ) );

		$this->route->setMethod( "POST" );
		$this->assertTrue( $this->route->isMethod( "POST" ) );
		$this->assertTrue( $this->route->isMethod( "post" ) );
		$this->assertFalse( $this->route->isMethod( "GET" ) );

		$this->route->setMethod( "*" );
		$this->assertTrue( $this->route->isMethod( "GET" ) );
		$this->assertTrue( $this->route->isMethod( "get" ) );
		$this->assertTrue( $this->route->isMethod( "POST" ) );
		$this->assertTrue( $this->route->isMethod( "post" ) );
		$this->assertTrue( $this->route->isMethod( "PUT" ) );
		$this->assertTrue( $this->route->isMethod( "DELETE" ) );
		$this->assertTrue( $this->route->isMethod( "HEAD" ) );
		$this->assertTrue( $this->route->isMethod( "OPTION" ) );
	}
}
