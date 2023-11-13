<?php
/**
 *	...
 *
 *	Copyright (c) 2016-2023 Christian Würker (ceusmedia.de)
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
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@package		CeusMedia_Router_Registry_Source
 *	@copyright		2016-2023 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */

namespace CeusMedia\Router\Registry\Source;

use CeusMedia\Router\Registry;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Registry_Source
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2023 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
interface SourceInterface
{
	const OPTION_AUTOLOAD		= 1;
	const OPTION_AUTOSAVE		= 2;

	/**
	 *	@param		string|NULL		$resource
	 *	@return		SourceInterface
	 */
	public static function create( string $resource = NULL ): SourceInterface;

	/**
	 *	@param		string|NULL		$resource
	 */
	public function __construct( string $resource = NULL );

	/**
	 *	@param		int			$key
	 *	@return		mixed|NULL
	 */
	public function getOption( int $key ): mixed;

	/**
	 *	@return		string|NULL
	 */
	public function getResource(): ?string;

	/**
	 *	@param		Registry		$registry
	 *	@return		int
	 */
	public function load( Registry $registry ): int;

	/**
	 *	@param		int				$key
	 *	@param		mixed|NULL		$value
	 *	@return		mixed
	 */
	public function setOption( int $key, mixed $value = NULL ): mixed;

	/**
	 *	@param		string			$resource
	 *	@return		AbstractSource
	 */
	public function setResource( string $resource ): AbstractSource;

	/**
	 *	@param		Registry		$registry
	 *	@return		int
	 */
	public function save( Registry $registry ): int;
}
