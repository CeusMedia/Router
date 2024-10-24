<?php
declare(strict_types=1);

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
 *	@package		CeusMedia_Router_Route
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2023 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */

namespace CeusMedia\Router\Route;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Route
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2023 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */class PatternPart
{
	public string $key;

	public bool $optional	= FALSE;

	public bool $argument	= FALSE;

	public mixed $value		= NULL;

	public static function create( string $key, bool $optional = FALSE, bool $argument = FALSE, mixed $value = NULL ): self
	{
		$object				= new self();
		$object->key		= $key;
		$object->optional	= $optional;
		$object->argument	= $argument;
		$object->value		= $value;
		return $object;
	}
}