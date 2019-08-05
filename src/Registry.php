<?php
/**
 *	...
 *
 *	Copyright (c) 2007-2016 Christian Würker (ceusmedia.de)
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2019 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
namespace CeusMedia\Router;

use CeusMedia\Router\Route\Factory as RouteFactory;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@uses			FS_File_JSON_Reader
 *	@uses			FS_File_JSON_Writer
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2019 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Registry{

	protected $routes	= array();

	/**
	 *	Adds route to route registry by route object.
	 *	@access		public
	 *	@param		Route		$route		Route object
	 *	@return		string		ID of added route
	 *	@throws		\DomainException			if route is already registered by route ID
	 */
	public function add( Route $route ): string
	{
		$routeId	= $route->getId();
		if( array_key_exists( $routeId, $this->routes ) ){
			throw new \DomainException( sprintf(
				'A route for method and pattern is already registered: %1$s %2$s',
				$route->getMethod(),
				$route->getPattern()
			) );
		}
		$this->routes[$routeId]	= $route;
		return $routeId;
	}

	/**
	 *	Return routes map.
	 *	@access		public
	 *	@return		array
	 */
	public function index(): array
	{
		return $this->routes;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$controller		...
	 *	@return		array  		List of found routes
	 */
	public function indexByController( $controller ): array
	{
		$routes		= array();
		foreach( $this->routes as $route ){
			if( $route->getController() === $controller ){
				$routes[]	= $route;
			}
		}
		return $routes;
	}

	/**
	 *	Adds a list of routes defined in a JSON file.
	 *	@access		public
	 *	@param		string		$filePath		Relative or absolute file path of JSON file to load
	 *	@param		string		$folderPath		Relative or absolute path to folder containing JSON files to assemble
	 *	@return		void
	 *	@throws		\OutOfRangeException			if route set has no controller
	 *	@throws		\OutOfRangeException			if route set has no action
	 *	@throws		\OutOfRangeException			if route set has no pattern
	 *	@throws		\OutOfRangeException			if route set has no method
	 */
	public function loadFromJsonFile( $filePath, $folderPath = NULL )
	{
		if( !file_exists( $filePath ) && $folderPath ){
			$this->assembleJsonFileFromFolder( $filePath, $folderPath );
		}
		$data	= \FS_File_JSON_Reader::load( $filePath );
		$factory	= new RouteFactory();
		foreach( $data as $item ){
			if( !isset( $item->pattern ) )
				throw new \OutOfRangeException( 'Route set is missing pattern' );
			$options	= array(
				'method'		=> isset( $item->method ) ? $item->method : NULL,
				'controller'	=> isset( $item->controller ) ? $item->controller : NULL,
				'action'		=> isset( $item->action ) ? $item->action : NULL,
			);
			if( isset( $item->mode ) && strlen( trim( $item->mode ) ) )
				$options['mode']	= $this->getModeFromString( $item->mode );
			if( isset( $item->roles ) && strlen( trim( $item->roles ) ) )
				$options['roles']	= preg_split( "/, */", trim( $item->roles ) );
			$this->add( $factory->create( $item->pattern, $options ) );
		}
	}

	/**
	 *	Removes a route from route registry by its ID.
	 *	@access		public
	 *	@param		string		$routeId		ID of route
	 *	@param		boolean		$strict			Throw exception if route ID is invalid
	 *	@return		boolean		TRUE is route existed and has been removed
	 *	@throws		\DomainException				if route ID has not been found in registry (strict mode only)
	 */
	public function remove( $routeId, $strict = TRUE ): bool
	{
		if( array_key_exists( $routeId, $this->routes ) ){
			unset( $this->routes[$routeId] );
			return TRUE;
		}
		if( $strict )
			throw new \DomainException( 'No route found for this route ID' );
		return FALSE;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$filePath		Relative or absolute file path of JSON file
	 * 	@return		integer		Number of bytes saved
	 */
	public function save( $filePath ): int
	{
		$data	= array();
		foreach( $this->index() as $route ){
			$data[]	= array(
				'mode'			=> $route->getMode(),
				'controller'	=> $route->getController(),
				'action'		=> $route->getAction(),
				'pattern'		=> $route->getPattern(),
				'method'		=> $route->getMethod(),
			);
		}
		return \FS_File_JSON_Writer::save( $filePath, $data, TRUE );
	}

	protected function assembleJsonFileFromFolder( string $filePath, string $folderPath ): int
	{
		if( !file_exists( $folderPath ) )
			throw new \RuntimeException( 'Folder "'.$folderPath.'" is not existing' );
		$list = array();
		$index	= new \FS_File_RegexFilter( $folderPath, '/\.json$/' );
		foreach( $index as $item ){
			$routes	= \FS_File_JSON_Reader::load( $item->getPathname() );
			foreach( $routes as $route ){
				$list[]	= $route;
			}
		}
		return \FS_File_JSON_Writer::save( $filePath, $list, TRUE );
	}

	protected function getModeFromString( $mode ): int
	{
		if( preg_match( '/^[a-z]+$/i', $mode ) ){
			$mode	= strtolower( $mode );
			if( $mode === 'controller' )
				$mode	= Route::MODE_CONTROLLER;
			else if( $mode === 'event' )
				$mode	= Route::MODE_EVENT;
			else if( $mode === 'forward' )
				$mode	= Route::MODE_FORWARD;
			else
				throw new RangeException( 'Invalid mode: '.$mode );
		}
		return $mode;
	}
}
?>
