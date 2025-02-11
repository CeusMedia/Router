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

use CeusMedia\Router\Route\PatternPart;
use InvalidArgumentException;

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
class Resolver
{
	/** @var	Registry		$registry		Registry instance */
	protected Registry $registry;

	/**
	 *	...
	 *	@static
	 *	@access		public
	 *	@param		Route		$route		Route object
	 *	@return		array
	 */
	public static function getRoutePatternParts( Route $route ): array
	{
		if( 'CLI' === $route->getMethod() ){
			$parts	= self::getRoutePatternPartsForCli( $route );
		}
		else{
			$parts	= self::getRoutePatternPartsForWeb( $route );
		}
		return $parts;
	}

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
	 *	Indicates whether a route can be resolved by path and method.
	 *	@access		public
	 *	@param		string		$path			...
	 *	@param		string		$method			HTTP method (GET|POST|PUT|DELETE)
	 *	@return		boolean
	 */
	public function hasRouteForPath( string $path, string $method = 'GET' ): bool
	{
		try{
			return (bool) $this->resolve( $path, $method, TRUE );
		}
		catch( ResolverException ){
			return FALSE;
		}
	}

	/**
	 *	Tries to resolve a given path on a given HTTP method into a route.
	 *	Iterates registered routes and matches given path against route pattern, if HTTP method applies.
	 *	Having a matching route, this route will be returned augmented by parsed arguments.
	 *	Otherwise, a custom exception will be thrown having strict mode enabled (default).
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
		foreach( self::orderRoutesByPriority( $this->registry->index() ) as $route ){
			if( !$route->isMethod( $method ) )														//  method is not matching
				continue;

			$pattern	= $route->getPattern();
			if( $route->getMethod() === 'CLI' ){
				self::regExpReplaceInString( "@\(:[\S]+\)@", "(\S+)?", $pattern );					//  insert mandatory argument pattern
				self::regExpReplaceInString( "@:[\S]+@", "\S+", $pattern );						//  insert mandatory argument pattern
				self::regExpReplaceInString( "@ @", "(\s+)?", $pattern );								//
			}
			else{
				self::regExpReplaceInString( "@(/\(:[^/]+\))@", "(/\S+)?", $pattern );				//  insert optional argument pattern
				self::regExpReplaceInString( "@(/:[^/(]+)@", "/\S+", $pattern );					//  insert mandatory argument pattern
				self::regExpReplaceInString( "/@/", "\@", $pattern );								//  escape @ to \@
				self::regExpReplaceInString( "@/$@", "/?", $pattern );								//  make ending slash optional
//				self::regExpReplaceInString( "@/$@", "", $path );
			}
			Log::debug( '> try pattern: '.$pattern );
			if( preg_match( '@^'.$pattern.'$@U', $path ) === 0 )									//  path is not matching route pattern
				continue;

			$partsPattern	= self::getRoutePatternParts( $route );
			Log::debug( '> pattern parts: ', $partsPattern );

			if( count( $partsPath ) > count( $partsPattern ) )										//  path has more parts than route pattern
				continue;

			$nr	= 0;
			/* @phpstan-ignore-next-line */
			foreach( $partsPattern as $nr => $part ){
				if( !$part->argument && 0 !== strlen( trim( $part->key ) ) ){								//  part is not an argument
					if( $partsPath[$nr] !== $part->key )
						break;
				}
				else if( $part->optional && !isset( $partsPath[$nr] ) )					//  part is argument but mandatory and not set
					break;
				$part->value	= $partsPath[$nr] ?? NULL;
			}

			if( $nr < count( $partsPattern ) - 1 )													//  loop has been broken
				continue;

			$arguments	= [];
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

	/**
	 *	@access		protected
	 *	@static
	 *	@param		Route		$route
	 *	@return		array<PatternPart>
	 */
	protected static function getRoutePatternPartsForCli( Route $route ): array
	{
		$parts	= [];
		foreach( explode( " ", $route->getPattern() ) as $part ){
			$item	= new PatternPart();
			if( preg_match( "@\(:.+\)@U", $part ) > 0 ){									//  optional argument found
				$part			= substr( $part, 1, -1 );							//  cut argument key
				$item->optional	= TRUE;															//  note that is optional
			}
			if( str_starts_with( $part, ":" ) ){												//  argument found
				$part			= substr( $part, 1 );										//  cut argument key
				$item->argument	= TRUE;															//  note that is argument
			}
			$item->key	= $part;
			$parts[]	= $item;																//  prepare route pattern part object
		}
		return $parts;
	}

	/**
	 *	@access		protected
	 *	@static
	 *	@param		Route		$route
	 *	@return		array<PatternPart>
	 */
	protected static function getRoutePatternPartsForWeb( Route $route ): array
	{
		$parts	= [];
		foreach( explode( "/", $route->getPattern() ) as $part ){
			$item	= new PatternPart();
			if( preg_match( "@\(:.+\)@U", $part ) > 0 ){									//  optional argument found
				$part			= substr( $part, 1, -1 );							//  cut argument key
				$item->optional	= TRUE;															//  note that is optional
			}
			if( str_starts_with( $part, ":" ) ){												//  argument found
				$part			= substr( $part, 1 );										//  cut argument key
				$item->argument	= TRUE;															//  note that is argument
			}
			$item->key	= $part;
			$parts[]	= $item;																//  prepare route pattern part object
		}
		return $parts;
	}

	/**
	 *	@access		protected
	 *	@static
	 *	@param		string		$regExp
	 *	@param		string		$replace
	 *	@param		string		$string
	 *	@return		void
	 */
	protected static function regExpReplaceInString( string $regExp, string $replace, string & $string ): void
	{
		$result	= preg_replace( $regExp, $replace, $string );
		if( $result === NULL )
			throw new InvalidArgumentException( 'Error on replace of regex in pattern' );
		$string	= $result;
	}

	/**
	 *	@access		protected
	 *	@static
	 *	@param		Route[]		$routes		List of registered routes
	 *	@return		Route[]		List of registered routes, ordered by priority
	 */
	protected static function orderRoutesByPriority( array $routes ): array
	{
		$map	= [];
		foreach( array_keys( Route::PRIORITY_KEYS ) as $priority )
			$map[$priority]	= [];

		foreach( $routes as $route )
			$map[$route->getPriority()][]	= $route;
//		return array_merge( ...array_filter( $map ) );

		$map	= array_filter( $map );
		return array_merge( ...$map );
	}
}
