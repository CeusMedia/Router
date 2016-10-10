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
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2016 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Router{

	protected $registry;

	protected $options	= array();

	public function __construct( $options = array() ){
		$this->options	= array_merge( $this->options, $options );
		$this->registry	= new Registry();
		$this->resolver	= new Resolver( $this->registry );
	}

	public function add( $controller, $action = 'index', $pattern, $method = '*' ){
		$route	= new  Route( $controller, $action, $pattern, strtoupper( $method ) );
		return $this->registry->add( $route );
	}

	public function addRoute( Route $route ){
		return $this->registry->add( $route );
	}

	public function getRoutes(){
		return $this->registry->index();
	}

	public function loadRoutesFromJsonFile( $filePath ){
		$this->registry->loadFromJsonFile( $filePath );
	}

	public function resolve( $path, $method = "GET" ){
		return $this->resolver->resolve( $path, $method );
	}

	public function saveRoutes( $filePath ){
		$this->registry->save( $filePath );
	}
}
?>
