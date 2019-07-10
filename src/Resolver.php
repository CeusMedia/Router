<?php
/**
 *	...
 *
 *	Copyright (c) 2007-2019 Christian Würker (ceusmedia.de)
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
 *	@copyright		2007-2019 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
namespace CeusMedia\Router;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2019 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Resolver{

	protected $registry;

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		Registry	$registry		Route registry object
	 *	@return		void
	 */
	public function __construct( Registry $registry ){
		$this->registry	= $registry;
	}

	/**
	 *	...
	 *	@static
	 *	@access		public
	 *	@param		Route		$route			Route object
	 *	@return		array		...
	 */
	static public function getRoutePatternParts( Route $route ){
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

	/**
	 *	Indicates whether a route can be resolved by path and method.
	 *	@access		public
	 *	@param		string		$path			...
	 *	@param		string		$method			HTTP method (GET|POST|PUT|DELETE)
	 *	@return		boolean
	 */
	public function hasRouteForPath( $path, $method = 'GET' ){
		return (bool) $this->resolve( $path, $method, FALSE );
	}

	/**
	 *	Tries to resolve a given path on a given HTTP method into a route.
	 *	Iterates registered routes and matches given path against route pattern, if HTTP method applies.
	 *	Having a matching route, this route will be returned augmented by parsed arguments.
	 *	Otherwise a custom exception will be thrown having strict mode enabled (default).
	 *	In non-strict mode the returned value will be FALSE.
	 *	@access		public
	 *	@param		string		$path			Path to resolve
	 *	@param		string		$method			HTTP method used
	 *	@param		boolean		$strict			Throw exception if not resolvable, otherwise return FALSE
	 *	@return		Route						Route object with inserted arguments
	 *	@throws		ResolverException			if path is not a resolvable route
	 */
	public function resolve( $path, $method = "GET", $strict = TRUE ){
		$path	= preg_replace( "@^/+@", "/", '/'.$path );
		$method	= strtoupper( $method );
		foreach( $this->registry->index() as $route ){
			if( !$route->isMethod( $method ) )														//  method is not matching
				continue;

			$pattern	= preg_replace( "@(/\(:[^/]+\))@", "(/\S+)?", $route->getPattern() );		//  insert optional argument pattern
			$pattern	= preg_replace( "@(/:[^/(]+)@", "/\S+", $pattern );							//  insert mandatory argument pattern
			$pattern	= preg_replace( "@/$@", "/?", $pattern );									//  make ending slash optional
			$pattern	= preg_replace( "/@/", "\@", $pattern );
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

			$resolvedRoute	= clone( $route );														//  avoid changes in original router registry object
			$resolvedRoute->setArguments( $arguments );												//  augment route by values for arguments in pattern
			$resolvedRoute->setMethod( $method );													//  specify used HTTP method
//			$resolvedRoute->setOrigin( ... );														//  set previous route
			return $resolvedRoute;																	//  return augmented route object clone
		}
		if( $strict )
			throw new ResolverException( 'Route is not resolvable' );
		return FALSE;
	}
}
?>
