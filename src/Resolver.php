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
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2020 Christian Würker
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
 *	@copyright		2016-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Resolver{

	/** @var	Registry		$registry		Registry instance */
	protected $registry;

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		Registry	$registry		Route registry object
	 *	@return		void
	 */
	public function __construct( Registry $registry )
	{
		$this->registry	= $registry;
	}

	/**
	 *	...
	 *	@static
	 *	@access		public
	 *	@param		Route		$route			Route object
	 *	@return		array		...
	 */
	static public function getRoutePatternParts( Route $route ): array
	{
		$parts	= array();
		if( $route->getMethod() === 'CLI' ){
			foreach( explode( " ", $route->getPattern() ) as $part ){
				$optional	= FALSE;
				$argument	= FALSE;
				if( preg_match( "@\(:.+\)@U", $part ) > 0 ){										//  optional argument found
					$part		= substr( $part, 1, -1 );											//  cut argument key
					$optional	= TRUE;																//  note that is optional
				}
				if( substr( $part, 0, 1 ) === ":" ){												//  argument found
					$part		= substr( $part, 1 );												//  cut argument key
					$argument	= TRUE;																//  note that is argument
				}
				$parts[]	= (object) array(														//  prepare route pattern part object
					'key'		=> $part,
					'optional'	=> $optional,
					'argument'	=> $argument,
				);
			}
		}
		else{
			foreach( explode( "/", $route->getPattern() ) as $part ){
				$optional	= FALSE;
				$argument	= FALSE;
				if( preg_match( "@\(:.+\)@U", $part ) > 0 ){										//  optional argument found
					$part		= substr( $part, 1, -1 );											//  cut argument key
					$optional	= TRUE;																//  note that is optional
				}
				if( substr( $part, 0, 1 ) === ":" ){												//  argument found
					$part		= substr( $part, 1 );												//  cut argument key
					$argument	= TRUE;																//  note that is argument
				}
				$parts[]	= (object) array(														//  prepare route pattern part object
					'key'		=> $part,
					'optional'	=> $optional,
					'argument'	=> $argument,
				);
			}
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
	public function hasRouteForPath( string $path, string $method = 'GET' ): bool
	{
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
	 *	@return		Route|null					Route object with inserted arguments
	 *	@throws		ResolverException			if path is not a resolvable route
	 */
	public function resolve( string $path, string $method = "GET", bool $strict = TRUE ): ?Route
	{
		Log::debug( 'Router Resolver: resolve' );
		Log::debug( '> path: '.$path );
		if( $method === 'CLI' ){
			$delimiter	= ' ';
		}
		else{
			$delimiter	= '/';
			$path		= '/'.$path;
			self::regExpReplaceInString( "@^/+@", "/", $path );
		}
		$partsPath	= explode( $delimiter, $path );													//  split path into parts
		Log::debug( '> path parts: '.json_encode( $partsPath ) );
		$method		= strtoupper( $method );
		foreach( $this->registry->index() as $route ){
			if( !$route->isMethod( $method ) )														//  method is not matching
				continue;

			$pattern	= $route->getPattern();
			if( $route->getMethod() === 'CLI' ){
				self::regExpReplaceInString( "@\(:[\S]+\)@", "(\S+)", $pattern );					//  insert mandatory argument pattern
				self::regExpReplaceInString( "@:[\S]+@", "\S+", $pattern );							//  insert mandatory argument pattern
			}
			else{
				self::regExpReplaceInString( "@(/\(:[^/]+\))@", "(/\S+)?", $pattern );				//  insert optional argument pattern
				self::regExpReplaceInString( "@(/:[^/(]+)@", "/\S+", $pattern );					//  insert mandatory argument pattern
				self::regExpReplaceInString( "/@/", "\@", $pattern );								//  excape @ to \@
				self::regExpReplaceInString( "@/$@", "/?", $pattern );								//  make ending slash optional
			}
			Log::debug( '> try pattern: '.$pattern );
			self::regExpReplaceInString( "@/$@", "", $path );
			if( preg_match( '@^'.$pattern.'$@U', $path ) === 0 )									//  path is not matching route pattern
				continue;

			$partsPattern	= self::getRoutePatternParts( $route );

			Log::debug( '> pattern parts: ', $partsPattern );

			if( count( $partsPath ) > count( $partsPattern ) )										//  path has more parts than route pattern
				continue;

			$matches	= TRUE;

			$nr	= 0;
			foreach( $partsPattern as $nr => $part ){
				if( empty( $part->argument ) && !empty( $part->key) ){								//  part is not an argument
					if( $partsPath[$nr] !== $part->key )
						break;
				}
				else if( empty( $part->optional ) && !isset( $partsPath[$nr] ) )					//  part is argument but mandatory and not set
					break;
				$part->value	= isset( $partsPath[$nr] ) ? $partsPath[$nr] : NULL;
			}

			if( $nr < count( $partsPattern ) - 1 )													//  loop has been broken
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
		return NULL;
	}

	//  --  PROTECTED  --  //

	protected static function regExpReplaceInString( string $regExp, string $replace, string & $string ): void
	{
		$result	= preg_replace( $regExp, $replace, $string );
		if( $result === NULL )
			throw new \InvalidArgumentException( 'Error on replace of regex in pattern' );
		$string	= $result;
	}
}
