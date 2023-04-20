<?php
/**
 *	...
 *
 *	Copyright (c) 2016-2020 Christian Würker (ceusmedia.de)
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
 *	@copyright		2016-2020 Christian Würker
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
 *	@copyright		2016-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Factory
{
	/** @var	int|NULL				$defaultMode	Mode to set by default for new route */
	protected ?int $defaultMode			= NULL;

	/** @var	string|NULL				$defaultMethod	Request method  to set by default for new route */
	protected ?string $defaultMethod	= NULL;

	public function create( string $pattern, array $options = [] ): Route
	{
		$options	= array_merge( [
			'method'		=> $this->defaultMethod,
			'mode'			=> $this->defaultMode,
			'controller'	=> NULL,
			'action'		=> NULL,
			'roles'			=> [],
		], $options );

		$route	= new Route( $pattern, $options['method'], $options['mode'] );
		if( isset( $options['controller'] ) && strlen( trim( $options['controller'] ) ) > 0 )
			$route->setController( $options['controller'] );
		if( isset( $options['action'] ) && strlen( trim( $options['action'] ) ) > 0 )
			$route->setAction( $options['action'] );
		if( isset( $options['roles'] ) ){
			if( is_array( $options['roles'] ) && count( $options['roles'] ) > 0 )
				$route->setRoles( $options['roles'] );
			else if( is_string( $options['roles'] ) ){
				if( strlen( trim( $options['roles'] ) ) > 0 )
					if( preg_split( '/\s*,\s*/', $options['roles'] ) !== FALSE )
						$route->setRoles( preg_split( '/\s*,\s*/', $options['roles'] ) );
			}
		}
		if( isset( $options['priority'] ) && strlen( trim( $options['priority'] ) ) > 0 ){
			if( !is_int( $options['priority'] ) )
				$options['priority']	= Route::getPriorityFromKey( $options['priority'] );
			$route->setPriority( $options['priority'] );
		}
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
