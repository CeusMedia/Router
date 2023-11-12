<?php
(@include '../../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

/*  --  IMPORT  ------------------------------------------------------------  */
use CeusMedia\Common\Alg\Obj\MethodFactory as ObjectMethodFactory;
use CeusMedia\Common\Alg\Time\Clock;
use CeusMedia\Common\UI\DevOutput;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\SourceInterface as RegistrySourceInterface;
use CeusMedia\Router\Registry\Source\Memcache as RegistryMemcacheSource;
use CeusMedia\Router\Registry\Source\JsonFile as RegistryJsonFileSource;
use CeusMedia\Router\Registry\Source\JsonFolder as RegistryJsonFolderSource;
use CeusMedia\Router\Router;
use CeusMedia\Router\Router\ResolverException as ResolverException;

require_once '../Controller/Test.php';

/*  --  INIT  --------------------------------------------------------------  */
new DevOutput;
error_reporting( E_ALL );

/*  --  CONFIG  ------------------------------------------------------------  */
$filePathCollectedRoutes	= 'all_collected_routes.json';
$folderPathSplitRoutes		= '../routes/';
$forceFreshLoad				= TRUE;

/*  --  RUN  ---------------------------------------------------------------  */
if( $forceFreshLoad ){
	@unlink( $filePathCollectedRoutes );
	$memcache = new Memcache();
	$memcache->connect( 'localhost', 11211 );
	$memcache->delete( 'CeusMediaRouterCliDemo1' );
}

$sourceMemcache	= new RegistryMemcacheSource( 'localhost:11211:CeusMediaRouterCliDemo1' );
$sourceMemcache->setOption( RegistrySourceInterface::OPTION_AUTOSAVE, TRUE );
$sourceMemcache->setOption( RegistrySourceInterface::OPTION_AUTOLOAD, !$forceFreshLoad );

$sourceJsonFile		= RegistryJsonFileSource::create()
	->setResource( $filePathCollectedRoutes )
	->setOption( RegistrySourceInterface::OPTION_AUTOSAVE, TRUE )
	->setOption( RegistrySourceInterface::OPTION_AUTOLOAD, !$forceFreshLoad );

$sourceJsonFolder	= RegistryJsonFolderSource::create()
	->setResource( $folderPathSplitRoutes );

$registry	= new Registry();
$registry->addSource( $sourceMemcache );
$registry->addSource( $sourceJsonFile );
$registry->addSource( $sourceJsonFolder );

$clock		= new Clock();

$router		= new Router();
$router->setRegistry( $registry );

remark( 'Routes:' );
foreach( $router->getRoutes() as $route ){
	if( 'CLI' === $route->getMethod() )
	//	print_m( $route->toArray() );
		print_m( array(
			'Pattern'		=> $route->getPattern(),
			'Method'		=> $route->getMethod(),
			'Controller'	=> $route->getController(),
			'Action'		=> $route->getAction(),
		) );
}

$paths	= [
	'test a1 b2',
	'test a1',
	'test',
//	'/test',
//	'/test/1',
//	'/test/1/2',
];

foreach( $paths as $path ){
	remark( 'Checking path: "'.$path.'"' );
	try{
		$result	= $router->resolve( $path );
		remark( ' - Status: found' );
		remark( ' - Call: '.$result->getController().'::'.$result->getAction().'('.join( ', ', $result->getArguments() ).')' );
		remark( ' - Arguments: '.json_encode( $result->getArguments() ) );

		$result	= ObjectMethodFactory::staticCallClassMethod(
			$result->getController(),
			$result->getAction(),
			[],
			$result->getArguments()
		);
		remark( $result );
	}
	catch( \Exception $e ){
		remark( ' - Status: '.$e->getMessage() );
	}
	remark();
}
remark( 'Time: '.$clock->stop( 3, 1 ).'ms'.PHP_EOL );
