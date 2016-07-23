<?php
if( !@include_once dirname( __DIR__ ).'/vendor/autoload.php' ){
	$path = dirname(__DIR__) . '/src/';
	require_once $path . 'Route.php';
	require_once $path . 'Router.php';
	require_once $path . 'Registry.php';
	require_once $path . 'Resolver.php';
}

//class Test_Case extends PHPUnit_Framework_TestCase{}

