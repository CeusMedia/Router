<?php
(@include '../../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

/*  --  IMPORT  ------------------------------------------------------------  */
use CeusMedia\Common\Alg\Obj\MethodFactory as ObjectMethodFactory;
use CeusMedia\Common\Alg\Time\Clock;
use CeusMedia\Common\UI\DevOutput;
use CeusMedia\Router as Router;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\SourceInterface as RegistrySourceInterface;
//use CeusMedia\Router\Registry\Source\Memcache as RegistryMemcacheSource;
use CeusMedia\Router\Registry\Source\JsonFile as RegistryJsonFileSource;
//use CeusMedia\Router\Registry\Source\JsonFolder as RegistryJsonFolderSource;

require_once '../Controller/Test.php';

/*  --  INIT  --------------------------------------------------------------  */
new DevOutput;
error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

$filePathCollectedRoutes	= '../routes/test.json';

$sourceJsonFile		= RegistryJsonFileSource::create()
	->setResource( $filePathCollectedRoutes )
//	->setOption( RegistrySourceInterface::OPTION_AUTOSAVE, TRUE )
//	->setOption( RegistrySourceInterface::OPTION_AUTOLOAD, FALSE )
	;

$options	= array( 'a' => FALSE );
$router		= new Router\Router( $options );
$router->setMethod( 'GET' );

$registry	= $router->getRegistry();
$registry->addSource( $sourceJsonFile );

$paths	= [
	'failing',
	'/test',
	'/test/1',
	'/test/1/2',
];

remark( 'Routes:' );
foreach( $router->getRoutes() as $route ){
	if( 'CLI' === $route->getMethod() )
		continue;
	print_m( array(
		'Controller'	=> $route->getController(),
		'Action'		=> $route->getAction(),
		'Pattern'		=> $route->getPattern(),
		'Method'		=> $route->getMethod(),
	) );
}

foreach( $paths as $path ){
	remark( 'Checking path: "'.$path.'"' );
	try{
		/** @var \CeusMedia\Router\Route $result */
		$result	= $router->resolve( $path );
		remark( ' - Status: found' );
		remark( ' - Call: '.$result->getController().'::'.$result->getAction().'('.join( ', ', $result->getArguments() ).')' );

		$controller	= ObjectMethodFactory::staticCallClassMethod(
			$result->getController(),
			$result->getAction(),
			[],
			$result->getArguments()
		);
	}
	catch( Router\ResolverException $e ){
		remark( ' - Status: '.$e->getMessage() );
	}
	remark();
}
