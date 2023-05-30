<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

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

use CeusMedia\Common\FS\File\JSON\Reader as JsonFileReader;
use CeusMedia\Common\FS\File\JSON\Writer as JsonFileWriter;
use CeusMedia\Router\Registry;
use CeusMedia\Router\Route;
use CeusMedia\Router\Route\Factory as RouteFactory;
use OutOfRangeException;
//use RuntimeException;

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
class JsonFile extends AbstractSource implements SourceInterface
{
	public function load( Registry $registry ): int
	{
		if( !file_exists( $this->resource ) )
			return -1;
//			throw new RuntimeException( 'JSON file "'.$this->resource.'" is not existing' );
		$counter	= 0;
		$data		= JsonFileReader::load( $this->resource );
		$factory	= new RouteFactory();
		foreach( (array) $data as $item ){
			if( !isset( $item->pattern ) )
				throw new OutOfRangeException( 'Route set is missing pattern' );
			$options	= array(
				'method'		=> $item->method ?? NULL,
				'controller'	=> $item->controller ?? NULL,
				'action'		=> $item->action ?? NULL,
			);
			if( isset( $item->mode ) && strlen( trim( $item->mode ) ) > 0 )
				$options['mode']	= Route::getModeFromKey( $item->mode );
			if( isset( $item->roles ) && strlen( trim( $item->roles ) ) > 0 )
				$options['roles']	= preg_split( "/, */", trim( $item->roles ) );
			if( isset( $item->priority ) && strlen( trim( $item->priority ) ) > 0 )
				$options['priority']	= Route::getPriorityFromKey( $item->priority );
			$registry->add( $factory->create( $item->pattern, $options ) );
			$counter++;
		}
		return $counter;
	}

	public function save( Registry $registry ): int
	{
		$data	= [];
		foreach( $registry->index() as $route ){
			$mode	= Route::getModeKey( $route->getMode() );
			$item	= [];
			if( strlen( $mode ) > 0 )
				$item['mode']	= $mode;
			$item['controller']	= $route->getController();
			$item['action']		= $route->getAction();
			$item['pattern']	= $route->getPattern();
			$item['method']		= $route->getMethod();
			if( $route->getPriority() !== Route::PRIORITY_NORMAL )
				$item['priority']	= Route::getPriorityKey( $route->getPriority() );
			$data[]	= $item;
		}
		return JsonFileWriter::save( $this->resource, $data, TRUE );
	}
}
