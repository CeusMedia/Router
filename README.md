# Router

![Branch](https://img.shields.io/badge/Branch-0.5.x-blue?style=flat-square)
![Release](https://img.shields.io/badge/Release-0.5.0-blue?style=flat-square)
![PHP version](https://img.shields.io/badge/PHP-%5E8.1-blue?style=flat-square&color=777BB4)
![PHPStan level](https://img.shields.io/badge/PHPStan_level-max+strict-darkgreen?style=flat-square)

PHP router for HTTP requests.  
Branch for PHP 8 support.

## Examples
### Simplest: single JSON file

Route map as JSON file:
```
[
    {
        "controller": "Controller_Test",
        "action": "test",
        "pattern": "test :a (:b)",
        "method": "CLI"
    },
    {
        "controller": "Controller_Test",
        "action": "test",
        "pattern": "\/test\/:a\/(:b)",
        "method": "GET"
    }
]
```
As you can see, the router works for CLI and HTTP environments.

Controller class:
```
class Controller_Test
{
	public function test( string $a = NULL, ?string $b = NULL ): string
	{
		return 'Called: Controller_Test::test($a, $b)' );
	}
}
```

Router setup:
```
use CeusMedia\Router;

$source		= Router\Registry\Source\JsonFile::create()->setResource( 'test.json' );
$registry	= Router\Registry::create()->addSource( $source );
$router		= Router\Router::create()->setRegistry( $registry );
```

Resolving a route:
```
$path	= 'test a1 b2';
$route	= $router->resolve( $path );
```

You could execute the resolved route controller action like this:
```
$result	= \CeusMedia\Common\Alg\Obj\MethodFactory::staticCallClassMethod(
	$route->getController(),
	$route->getAction(),
	[],
	$route->getArguments()
);

```
### Using multiple JSON files in a folder
```
$source	= Router\Registry\Source\JsonFolder::create()->setResource( 'routes/' );
```

#### Using a cache on top

Invalidate cache if routes file has changed:
```
$invalidateCache = FALSE;
if( $invalidateCache ){
	$memcache = new Memcache();
	$memcache->connect( 'localhost', 11211 );
	$memcache->delete( 'CeusMediaRouterCliDemo1' );
}
```

Setup, related to cache invalidation request:
```
$sourceMemcache	= new Router\Registry\Source\Memcache( 'localhost:11211:CeusMediaRouterCliDemo1' );
$sourceMemcache->setOption( Router\Registry\SourceInterface::OPTION_AUTOSAVE, TRUE );
$sourceMemcache->setOption( Router\Registry\SourceInterface::OPTION_AUTOLOAD, !$invalidateCache );

...

$registry->addSource( $sourceMemcache );
$registry->addSource( $sourceJsonFile );
```