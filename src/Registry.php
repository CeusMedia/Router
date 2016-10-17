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
 *	@copyright		2007-2016 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
namespace CeusMedia\Router;
/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@uses			FS_File_JSON_Reader
 *	@uses			FS_File_JSON_Writer
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2016 Christian Würker
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
	 *	@throws		DomainException			if route is already registered by route ID
	 */
	public function add( Route $route ){
		$routeId	= $route->getId();
		if( array_key_exists( $routeId, $this->routes ) )
			throw new \DomainException( 'A route for pattern and method is already registered' );
		$this->routes[$routeId]	= $route;
		return $routeId;
	}

	/**
	 *	Return routes map.
	 *	@access		public
	 *	@return		array
	 */
	public function index(){
		return $this->routes;
	}

	public function indexByController( $controller ){
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
	 *	@return		void
	 *	@throws		OutOfRangeException			if route set has no controller
	 *	@throws		OutOfRangeException			if route set has no action
	 *	@throws		OutOfRangeException			if route set has no pattern
	 *	@throws		OutOfRangeException			if route set has no method
	 */
	public function loadFromJsonFile( $filePath ){
		$data	= \FS_File_JSON_Reader::load( $filePath );
		foreach( $data as $item ){
			if( !isset( $item->controller ) )
				throw new \OutOfRangeException( 'Route set is missing controller' );
			if( !isset( $item->action ) )
				throw new \OutOfRangeException( 'Route set is missing action' );
			if( !isset( $item->pattern ) )
				throw new \OutOfRangeException( 'Route set is missing pattern' );
			if( !isset( $item->method ) )
				throw new \OutOfRangeException( 'Route set is missing method' );

			$route	= new Route(
				$item->controller,
				$item->action,
				$item->pattern,
				$item->method
			);
			$this->add( $route );
		}
	}

	/**
	 *	Removes a route from route registry by its ID.
	 *	@access		public
	 *	@param		string		$routeId		ID of route
	 *	@param		boolean		$strict			Throw exception if route ID is invalid
	 *	@return		boolean		TRUE is route existed and has been removed
	 *	@throws		DomainException				if route ID has not been found in registry (strict mode only)
	 */
	public function remove( $routeId, $strict = TRUE ){
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
	public function save( $filePath ){
		$data	= array();
		foreach( $this->index() as $route ){
			$data[]	= array(
				'controller'	=> $route->getController(),
				'action'		=> $route->getAction(),
				'pattern'		=> $route->getPattern(),
				'method'		=> $route->getMethod(),
			);
		}
		return \FS_File_JSON_Writer::save( $filePath, $data, TRUE );
	}
}
?>
