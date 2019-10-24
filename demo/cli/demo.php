<?php
(@include '../../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

/*  --  IMPORT  ------------------------------------------------------------  */
use \CeusMedia\Router\Registry;
use \CeusMedia\Router\Registry\Source\SourceInterface as RegistrySourceInterface;
use \CeusMedia\Router\Registry\Source\Memcache as RegistryMemcacheSource;
use \CeusMedia\Router\Registry\Source\JsonFile as RegistryJsonFileSource;
use \CeusMedia\Router\Registry\Source\JsonFolder as RegistryJsonFolderSource;

/*  --  INIT  --------------------------------------------------------------  */
new UI_DevOutput;
error_reporting( E_ALL );

/*  --  CONFIG  ------------------------------------------------------------  */
$filePathCollectedRoutes	= 'all_collected_routes.json';
$folderPathSplitRoutes		= 'routes/';
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

$sourceJsonFile		= RegistryJsonFileSource::getNewInstance()
	->setResource( $filePathCollectedRoutes )
	->setOption( RegistrySourceInterface::OPTION_AUTOSAVE, TRUE )
	->setOption( RegistrySourceInterface::OPTION_AUTOLOAD, !$forceFreshLoad );

$sourceJsonFolder	= RegistryJsonFolderSource::getNewInstance()
	->setResource( $folderPathSplitRoutes );

$registry	= new Registry();
$registry->addSource( $sourceMemcache );
$registry->addSource( $sourceJsonFile );
$registry->addSource( $sourceJsonFolder );

$watch	= new \Alg_Time_Clock();
$routes	= $registry->index();
remark( 'Time: '.$watch->stop( 3, 1 ).'ms'.PHP_EOL );
foreach( $routes as $route )
	print_m($route->toArray());
