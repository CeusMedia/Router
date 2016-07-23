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
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2016 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Mail
 */
namespace CeusMedia\Router;
/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2016 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Resolver{

	protected $registry;

	public function __construct( \CeusMedia\Router\Registry $registry ){
		$this->registry	= $registry;
	}

	static public function getRoutePatternParts( $route ){
		$parts	= array();
		foreach( explode( "/", $route->getPattern() ) as $part ){
			$optional	= FALSE;
			$argument	= FALSE;
			if( preg_match( "@\(:.+\)@", $part ) ){													//  optional argument found
				$part		= substr( $part, 1, -1 );												//  cut argument key
				$optional	= TRUE;																	//  note that is optional
			}
			if( substr( $part, 0, 1 ) === ":" ){													//  argument found
				$part		= substr( $part, 1 );													//  cut argument key
				$argument	= TRUE;																	//  note that is argument
			}
			$parts[]	= (object) array(															//  prepare route pattern part object
				'key'		=> $part,
				'optional'	=> $optional,
				'argument'	=> $argument,
			);
		}
		return $parts;
	}

	public function resolve( $path, $method = "GET" ){
		$method	= strtoupper( $method );
		foreach( $this->registry->index() as $route ){
			if( !$route->isMethod( $method ) )														//  method is not matching
				continue;

			$pattern	= preg_replace( "@/(\(:[^/]+\))@", "/(\S+)?", $route->getPattern() );		//  insert optional argument pattern
			$pattern	= preg_replace( "@/(:[^/]+)@", "/\S+", $pattern );							//  insert mandatory argument pattern
			$pattern	= preg_replace( "@/@", "/?", $pattern );									//  make ending slash optional
			if( !preg_match( '@^'.$pattern.'$@U', $path ) )											//  path is not matching route pattern
				continue;

			$partsPattern	= self::getRoutePatternParts( $route );

			$partsPath	= explode( "/", $path );													//  split path into parts
			if( count( $partsPath ) > count( $partsPattern ) )										//  path has more parts than route pattern
				continue;

			$matches	= TRUE;
			for( $i=0; $i<count( $partsPattern ); $i++ ){
				if( !$partsPattern[$i]->argument ){													//  part is not an argument
					if( $partsPath[$i] !== $partsPattern[$i]->key )
						break;
				}
				else if( !$partsPattern[$i]->optional && !isset( $partsPath[$i] ) )					//  part is argument but mandatory and not set
					break;
				$partsPattern[$i]->value	= isset( $partsPath[$i] ) ? $partsPath[$i] : NULL;
			}
			if( $i < count( $partsPattern ) - 1 )													//  loop has been broken
				continue;

			$arguments	= array();
			foreach( $partsPattern as $part )
				if( $part->argument )
					$arguments[$part->key]	= $part->value;

			$match	= (object) array(
				'controller'	=> $route->getController(),
				'action'		=> $route->getAction(),
				'method'		=> $method,
				'arguments'		=> $arguments,
			);
			return $match;
		}
		return FALSE;
	}

}
?>
