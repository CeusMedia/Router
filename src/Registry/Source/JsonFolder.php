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
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@package		CeusMedia_Router_Registry_Source
 *	@copyright		2016-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
namespace CeusMedia\Router\Registry\Source;

use CeusMedia\Common\FS\File\RegexFilter as FileRegexFilter;
use CeusMedia\Router\Registry;
use SplFileObject;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Registry_Source
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class JsonFolder extends AbstractSource implements SourceInterface
{
	public function load( Registry $registry ): int
	{
		if( !file_exists( $this->resource ) )
			return -1;
//			throw new \RuntimeException( 'Folder "'.$this->resource.'" is not existing' );
		$counter	= 0;
		$index	= new FileRegexFilter( $this->resource, '/\.json$/' );
		/** @var SplFileObject $item */
		foreach( $index as $item ){
			$source	= new JsonFile();
			$source->setResource( $item->getPathname() );
			$counter	+= $source->load( $registry );
		}
		return $counter;
	}

	public function save( Registry $registry ): int
	{
		return 0;
	}
}
