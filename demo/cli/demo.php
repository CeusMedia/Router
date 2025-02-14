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

require_once '../Controller/Test.php';

/*  --  INIT  --------------------------------------------------------------  */
new DevOutput;
error_reporting( E_ALL );

/*  --  CONFIG  ------------------------------------------------------------  */
$filePathCollectedRoutes	= 'all_collected_routes.json';
$folderPathSplitRoutes		= '../routes/';
$forceFreshLoad				= TRUE;

/*  --  RUN  ---------------------------------------------------------------  */
/** @noinspection PhpConditionAlreadyCheckedInspection */
/** @phpstan-ignore-next-line  */
$forceFreshLoad ??= FALSE;
/** @phpstan-ignore-next-line  */
if( $forceFreshLoad ){
	@unlink( $filePathCollectedRoutes );
	$memcache = new Memcache();
	$memcache->connect( 'localhost', 11211 );
	$memcache->delete( 'CeusMediaRouterCliDemo1' );
}

$sourceMemcache	= new RegistryMemcacheSource( 'localhost:11211:CeusMediaRouterCliDemo1' );
$sourceMemcache->setOption( RegistrySourceInterface::OPTION_AUTOSAVE, TRUE );
/** @phpstan-ignore-next-line  */
$sourceMemcache->setOption( RegistrySourceInterface::OPTION_AUTOLOAD, !$forceFreshLoad );

$sourceJsonFile		= RegistryJsonFileSource::create()
	->setResource( $filePathCollectedRoutes )
	->setOption( RegistrySourceInterface::OPTION_AUTOSAVE, TRUE )
	/** @phpstan-ignore-next-line  */
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
		/** @var \CeusMedia\Router\Route $route */
		$route	= $router->resolve( $path );
		remark( ' - Status: found' );
		remark( ' - Call: '.$route->getController().'::'.$route->getAction().'('.join( ', ', $route->getArguments() ).')' );
		remark( ' - Arguments: '.json_encode( $route->getArguments() ) );

		/** @var string $response */
		$response	= ObjectMethodFactory::staticCallClassMethod(
			$route->getController(),
			$route->getAction(),
			[],
			$route->getArguments()
		);
		remark( $response );
	}
	catch( \Exception $e ){
		remark( ' - Status: '.$e->getMessage() );
	}
	remark();
}
remark( 'Time: '.$clock->stop( 3, 1 ).'ms'.PHP_EOL );
