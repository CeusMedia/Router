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
 *	@link			https://github.com/CeusMedia/Mail
 */
namespace CeusMedia\Router;
/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2016 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Registry{

	protected $routes	= array();

	public function add( \CeusMedia\Router\Route $route ){
		$routeId	= $route->getId();
		if( array_key_exists( $routeId, $this->routes ) )
			throw new \DomainException( 'A route for pattern and method is already registered' );
		$this->routes[$routeId]	= $route;
		return $routeId;
	}

	public function index(){
		return $this->routes;
	}

	public function load( $filePath ){
		$data	= \FS_File_JSON_Reader::load( $filePath );
		foreach( $data as $item ){
			$route	= new \CeusMedia\Router\Route(
				$item->controller,
				$item->action,
				$item->pattern,
				$item->method
			);
			$this->add( $route );
		}
	}

	public function remove( $routeId, $strict = TRUE ){
		if( array_key_exists( $routeId, $this->routes ) ){
			unset( $this->routes[$routeId] );
			return TRUE;
		}
		if( $strict )
			throw new \DomainException( 'No route found for this route ID' );
		return FALSE;
	}

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
