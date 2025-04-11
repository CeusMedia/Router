<?php
declare(strict_types=1);

/**
 *	Factory for route objects.
 *
 *	Copyright (c) 2016-2025 Christian Würker (ceusmedia.de)
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
 *	along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Route
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2025 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */

namespace CeusMedia\Router\Route;

use CeusMedia\Router\Route;

/**
 *	Factory for route objects.
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Route
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2025 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Factory
{
	/** @var	int|NULL				$defaultMode	Mode to set by default for new route */
	protected ?int $defaultMode			= NULL;

	/** @var	string|NULL				$defaultMethod	Request method  to set by default for new route */
	protected ?string $defaultMethod	= NULL;

	/**
	 *	Creates a route object by a request pattern.
	 *	Will apply default method and mode, if set.
	 *	Given options will override defaults.
	 *	@param		string		$pattern
	 *	@param		array		$options
	 *	@return		Route
	 */
	public function create( string $pattern, array $options = [] ): Route
	{
		$options	= array_merge( [
			'method'		=> $this->defaultMethod,
			'mode'			=> $this->defaultMode,
			'controller'	=> NULL,
			'action'		=> NULL,
			'roles'			=> [],
			'priority'		=> NULL,
		], $options );

		$route	= new Route( $pattern, $options['method'], $options['mode'] );
		if( '' !== trim( $options['controller'] ?? '' ) )
			$route->setController( $options['controller'] );
		if( '' !== trim( $options['action'] ?? '' ) )
			$route->setAction( $options['action'] );

		if( is_string( $options['roles'] ) )
			$options['roles']	= preg_split( '/\s*,\s*/', $options['roles'] );
		$options['roles']	= array_map( 'trim', $options['roles'] ?? '' );
		$route->setRoles( $options['roles'] );

		if( '' !== trim( (string) ( $options['priority'] ?? '' ) ) ){
			if( !is_int( $options['priority'] ) )
				$options['priority']	= Route::getPriorityFromKey( $options['priority'] );
			$route->setPriority( $options['priority'] );
		}
		return $route;
	}

	/**
	 *	Set default method of created routes.
	 *	Either CLI or one of the HTTP methods, like GET,POST,PUT,DELETE,HEAD,OPTIONS or *
	 *	@param		string		$method		One of (CLI|GET|POST|PUT|DELETE|HEAD|OPTIONS|*)
	 *	@return		self
	 */
	public function setDefaultMethod( string $method ): self
	{
		$this->defaultMethod	= $method;
		return $this;
	}

	/**
	 *	Set default mode of created routes.
	 *	@param		int		$mode		One of Route::MODE_* (UNKNOWN|CONTROLLER|EVENT|FORWARD)
	 *	@return		self
	 */
	public function setDefaultMode( int $mode ): self
	{
		$this->defaultMode		= $mode;
		return $this;
	}
}
