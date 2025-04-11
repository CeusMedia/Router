<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

/**
 *	Memory route registry adapter.
 *	Usable for testing or high performance use cases.
 *
 *	Copyright (c) 2024-2025 Christian Würker (ceusmedia.de)
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
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@package		CeusMedia_Router_Registry_Source
 *	@copyright		2024-2025 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */

namespace CeusMedia\Router\Registry\Source;

use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;

/**
 *	Memory route registry adapter.
 *	Usable for testing or high performance use cases.
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Registry_Source
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2024-2025 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Memory extends AbstractSource implements SourceInterface
{
	/** @var array<Route> $routes */
	public array $routes	= [];

	/**
	 *	Register routes from list of routes from this class member 'routes'.
	 *	@param		Registry	$registry
	 *	@return		int
	 */
	public function load( Registry $registry ): int
	{
		foreach( $this->routes as $route )
			$registry->add( $route );
		return count( $this->routes );
	}

	/**
	 *	Stores all routes in from all registered sources into memory route storage.
	 *	@param		Registry		$registry
	 *	@return		int
	 */
	public function save( Registry $registry ): int
	{
		$this->routes	= [];
		foreach( $registry->index() as $route )
			$this->routes[]	= $route;
		return count( $this->routes );
	}
}