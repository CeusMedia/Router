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
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@package		CeusMedia_Router_Registry_Source
 *	@copyright		2007-2019 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
namespace CeusMedia\Router\Registry\Source;

use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\AbstractSource;
use CeusMedia\Router\Registry\Source\SourceInterface;
use CeusMedia\Router\Route\Factory as RouteFactory;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Registry_Source
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2019 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Memcache extends AbstractSource implements SourceInterface
{
	public function load( Registry $registry ): int
	{
		$counter	= 0;
		$serial		= $this->server->get( $this->cacheKey );
		if( $serial === FALSE || strlen( $serial ) < 3 )
			return -1;
		$object	= unserialize( $serial );
		if( $object instanceof Registry ){
			foreach( $object->index() as $route ){
				$registry->add( $route );
				$counter++;
			}
		}
		return $counter;
	}

	public function save( Registry $registry ): int
	{
		$this->server->set( $this->cacheKey, serialize( $registry ) );
		return 1;
	}

	public function setResource( string $resource ): SourceInterface
	{
		$matches	= array();
		$result		= preg_match( '/^([^:]+):([^:]+):(.+)$/U', $resource, $matches );
		if( $result === 0 )
			throw new \InvalidArgumentException( 'Invalid Memcache resource string: '.$resource );
		$server			= $matches[1];
		$port			= $matches[2];
		$this->cacheKey	= $matches[3];
		$this->resource	= $resource;
		$this->server = new \Memcache;
		$this->server->connect( $server, $port );

		return $this;
	}
}
