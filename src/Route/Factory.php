<?php
/**
 *	...
 *
 *	Copyright (c) 2007-2019 Christian Würker (ceusmedia.de)
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
namespace CeusMedia\Router\Route;

use CeusMedia\Router\Route;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2019 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Factory{

	protected $defaultMode;
	protected $defaultMethod;

	public function create( string $pattern, array $options = array() ): Route
	{
		$options	= array_merge( array(
			'method'		=> $this->defaultMethod,
			'mode'			=> $this->defaultMode,
			'controller'	=> NULL,
			'action'		=> NULL,
			'roles'			=> array(),
		), $options );

		$route	= new Route( $pattern, $options['method'], $options['mode'] );
		if( !empty( $options['controller'] ) )
			$route->setController( $options['controller'] );
		if( !empty( $options['action'] ) )
			$route->setAction( $options['action'] );
		if( !empty( $options['roles'] ) )
			$route->setRoles( $options['roles'] );
		return $route;
	}

	public function setDefaultMethod( string $method ): self
	{
		$this->defaultMethod	= $method;
		return $this;
	}

	public function setDefaultMode( int $mode ): self
	{
		$this->defaultMode		= $mode;
		return $this;
	}
}
