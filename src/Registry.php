<?php
declare(strict_types=1);

/**
 *	...
 *
 *	Copyright (c) 2016-2024 Christian Würker (ceusmedia.de)
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
 *	@copyright		2016-2024 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */

namespace CeusMedia\Router;

use CeusMedia\Router\Registry\Source as RegistrySource;
use CeusMedia\Router\Registry\Source\SourceInterface as RegistrySourceInterface;
use DomainException;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2024 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Registry
{
	const STATUS_NEW		= 0;
	const STATUS_CLEAN		= 1;
	const STATUS_LOADING	= 2;
	const STATUS_CHANGED	= 2;
	const STATUS_SAVING		= 3;

	/** @var	Route[]			$routes			List of registered routes */
	protected array $routes		= [];

	/** @var	integer			$status			Current status of registry */
	protected int $status		= 0;

	/** @var	RegistrySource	$source			List of registered routes */
	protected RegistrySource $source;

	public function __construct()
	{
		$this->source	= new RegistrySource();
	}

	/**
	 *	@return		self
	 */
	public static function create(): self
	{
		return new self();
	}

	/**
	 *	Adds route to route registry by route object.
	 *	@access		public
	 *	@param		Route		$route			Route object
	 *	@return		string		ID of added route
	 *	@throws		DomainException			if route is already registered by route ID
	 */
	public function add( Route $route ): string
	{
//		$this->loadFromSources();
		$routeId	= $route->getId();
		if( array_key_exists( $routeId, $this->routes ) ){
			throw new DomainException( sprintf(
				'A route for method and pattern is already registered: %1$s %2$s',
				$route->getMethod(),
				$route->getPattern()
			) );
		}
		$this->routes[$routeId]	= $route;
		$this->status = self::STATUS_CHANGED;
		$this->saveToSources();
		return $routeId;
	}

	public function addSource( RegistrySourceInterface $source ): self
	{
		$this->source->addSource( $source );
		$source->load( $this );
		return $this;
	}

	/**
	 *	Return routes map.
	 *	@access		public
	 *	@return		Route[]
	 */
	public function index(): array
	{
//		$this->loadFromSources();
		return $this->routes;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$controller		...
	 *	@return		array  		List of found routes
	 */
	public function indexByController( string $controller ): array
	{
		$this->loadFromSources();
		$routes		= [];
		foreach( $this->routes as $route ){
			if( $route->getController() === $controller ){
				$routes[]	= $route;
			}
		}
		return $routes;
	}

	/**
	 *	Removes a route from route registry by its ID.
	 *	@access		public
	 *	@param		string		$routeId		ID of route
	 *	@param		boolean		$strict			Throw exception if route ID is invalid
	 *	@return		boolean		TRUE is route existed and has been removed
	 *	@throws		DomainException				if route ID has not been found in registry (strict mode only)
	 */
	public function remove( string $routeId, bool $strict = TRUE ): bool
	{
//		$this->loadFromSources();
		if( array_key_exists( $routeId, $this->routes ) ){
			unset( $this->routes[$routeId] );
			$this->status = self::STATUS_CHANGED;
			$this->saveToSources();
			return TRUE;
		}
		if( $strict )
			throw new DomainException( 'No route found for this route ID' );
		return FALSE;
	}

	/*  --  PROTECTED  --  */

	protected function loadFromSources( bool $forceFreshLoad = FALSE ): bool
	{
		if( $this->status === self::STATUS_NEW || $forceFreshLoad ){
			$this->status	= self::STATUS_LOADING;
			$this->source->load( $this );
			$this->status	= self::STATUS_CLEAN;
			return TRUE;
		}
		return FALSE;
	}

	protected function saveToSources( bool $forceFreshSave = FALSE ): bool
	{
		if( $this->status === self::STATUS_CHANGED || $forceFreshSave ){
			$this->status	= self::STATUS_SAVING;
			$this->source->save( $this );
			$this->status	= self::STATUS_CLEAN;
			return TRUE;
		}
		return FALSE;
	}
}
