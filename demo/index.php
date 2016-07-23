<?php
(@include '../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

use \CeusMedia\Router as Router;

new UI_DevOutput;
error_reporting( E_ALL );

$options	= array( 'a' => FALSE );
$router = new Router\Router( $options );

/*$router->addRoute( new Router\Route(
	'Controller_Slide',
	'index',
	'slide',
	'*'
) );
$router->addRoute( new Router\Route(
	'Controller_Slide',
	'edit',
	'slide/edit/:sliderId/(:slideId)',
	'*'
) );
$router->saveRoutes( 'routes.json' );
*/

$router->loadRoutes( 'routes.json' );

$paths	= array(
	'failing',
	'slide',
	'slide/edit/1',
	'slide/edit/1/2-Hallo_Welt',
);

remark( 'Routes:' );
foreach( $router->getRoutes() as $route ){
	print_m( array(
		'Controller'	=> $route->getController(),
		'Action'		=> $route->getAction(),
		'Pattern'		=> $route->getPattern(),
		'Method'		=> $route->getMethod(),
	) );
}

foreach( $paths as $path ){
	remark( "Checking path: ".$path );
	$result	= $router->resolve( $path );
	if( $result ){
		remark( ' - Status: found' );
		remark( ' - Call: '.$result->controller.'::'.$result->action.'('.join( ', ', $result->arguments ).')' );
	}
	else{
		remark( ' - Status: not found' );
	}
	remark();
}
