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

use DomainException;
use InvalidArgumentException;
use RangeException;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2022 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Route
{
	const MODE_UNKNOWN			= 0;
	const MODE_CONTROLLER		= 1;
	const MODE_EVENT			= 2;
	const MODE_FORWARD			= 3;

	const MODE_KEY_UNKNOWN		= 'unknown';
	const MODE_KEY_CONTROLLER	= 'controller';
	const MODE_KEY_EVENT		= 'event';
	const MODE_KEY_FORWARD		= 'forward';

	const MODES_BY_KEYS			= [
		self::MODE_KEY_UNKNOWN		=> self::MODE_UNKNOWN,
		self::MODE_KEY_CONTROLLER	=> self::MODE_CONTROLLER,
		self::MODE_KEY_EVENT		=> self::MODE_EVENT,
		self::MODE_KEY_FORWARD		=> self::MODE_FORWARD,
	];

	const MODE_KEYS				= [
		self::MODE_UNKNOWN		=> self::MODE_KEY_UNKNOWN,
		self::MODE_CONTROLLER	=> self::MODE_KEY_CONTROLLER,
		self::MODE_EVENT		=> self::MODE_KEY_EVENT,
		self::MODE_FORWARD		=> self::MODE_KEY_FORWARD,
	];

	const PRIORITY_EARLIEST		= 1;
	const PRIORITY_EARLIER		= 2;
	const PRIORITY_NORMAL		= 3;
	const PRIORITY_LATER		= 4;
	const PRIORITY_LATEST		= 5;

	const PRIORITY_KEY_EARLIEST		= 'earliest';
	const PRIORITY_KEY_EARLIER		= 'earlier';
	const PRIORITY_KEY_NORMAL		= 'normal';
	const PRIORITY_KEY_LATER		= 'later';
	const PRIORITY_KEY_LATEST		= 'latest';

	const PRIORITIES_BY_KEYS			= [
		self::PRIORITY_KEY_EARLIEST		=> self::PRIORITY_EARLIEST,
		self::PRIORITY_KEY_EARLIER		=> self::PRIORITY_EARLIER,
		self::PRIORITY_KEY_NORMAL		=> self::PRIORITY_NORMAL,
		self::PRIORITY_KEY_LATER		=> self::PRIORITY_LATER,
		self::PRIORITY_KEY_LATEST		=> self::PRIORITY_LATEST,
	];

	const PRIORITY_KEYS			= [
		self::PRIORITY_EARLIEST		=> self::PRIORITY_KEY_EARLIEST,
		self::PRIORITY_EARLIER		=> self::PRIORITY_KEY_EARLIER,
		self::PRIORITY_NORMAL		=> self::PRIORITY_KEY_NORMAL,
		self::PRIORITY_LATER		=> self::PRIORITY_KEY_LATER,
		self::PRIORITY_LATEST		=> self::PRIORITY_KEY_LATEST,
	];

	/** @var	string				$method				Request method of route */
	protected string $method		= 'GET';

	/** @var	integer				$mode				Mode of route: 1:controller, 2:event: 3: forward */
	protected int $mode				= self::MODE_UNKNOWN;

	/** @var	string				$pattern			... */
	protected string $pattern		= '';

	/** @var	string				$controller			Controller class attached to route */
	protected string $controller	= '';

	/** @var	string				$action				Action if controller attached to route */
	protected string $action		= '';

	/** @var	array				$arguments			Key in cache */
	protected array $arguments		= [];

	/** @var	array				$roles				List of role keys to limit route access to, empty means no limits */
	protected array $roles			= [];

	/** @var	?Route				$origin				... */
	protected ?Route $origin		= NULL;

	/** @var	int					$priority			Resolver priority: 1:earliest, 2:earlier, 3:normal, 4:later, 5:latest */
	protected int $priority			= self::PRIORITY_NORMAL;

	/** @var	string				$target				Target address for route of type "forward" */
	protected string $target		= '';

	/** @var	array				$supportedMethods	Allowed request methods */
	public array $supportedMethods		= [
		'CLI',
		'GET',
		'HEAD',
		'POST',
		'PUT',
		'DELETE',
		'OPTIONS',
	];

	public static function getModeFromKey( string $mode, bool $strict = TRUE ): int
	{
		$mode	= strtolower( $mode );
		if( array_key_exists( $mode, self::MODES_BY_KEYS ) )
			return self::MODES_BY_KEYS[$mode];
		if( $strict )
			throw new RangeException( 'Invalid mode key: '.$mode );
		return self::MODE_UNKNOWN;
	}

	public static function getModeKey( int $mode, bool $strict = TRUE ): string
	{
		if( array_key_exists( $mode, self::MODE_KEYS) )
			return self::MODE_KEYS[$mode];
		if( $strict )
			throw new RangeException( 'Invalid mode: '.$mode );
		return self::MODE_KEY_UNKNOWN;
	}

	public static function getPriorityFromKey( string $priority ): int
	{
		if( array_key_exists( strtolower( $priority ), self::PRIORITIES_BY_KEYS ) )
			return self::PRIORITIES_BY_KEYS[$priority];
		throw new RangeException( 'Invalid priority key: '.$priority );
	}

	/**
	 *	@param		int			$priority
	 *	@return		string
	 *	@throws		RangeException		if priority is not supported (= within [1-5])
	 */
	public static function getPriorityKey( int $priority ): string
	{
		if( array_key_exists( $priority, self::PRIORITY_KEYS ) )
			return self::PRIORITY_KEYS[$priority];
		throw new RangeException( 'Invalid priority: '.$priority );
	}

	public function __construct( string $pattern, string $method = NULL, int $mode = NULL )
	{
		if( !is_null( $method ) )
			$this->setMethod( $method );
		if( !is_null( $mode ) )
			$this->setMode( $mode );
		$this->setPattern( $pattern );
	}

	public function getAction(): string
	{
		return $this->action;
	}

	public function getArguments(): array
	{
		return $this->arguments;
	}

	public function getController(): string
	{
		return $this->controller;
	}

	public function getId(): string
	{
		return md5( $this->method.'::'.$this->pattern );
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function getMode(): int
	{
		return $this->mode;
	}

	public function getOrigin(): ?Route
	{
		return $this->origin;
	}

	public function getPattern(): string
	{
		return $this->pattern;
	}

	public function getPriority(): int
	{
		return $this->priority;
	}

	public function getRoles(): array
	{
		return $this->roles;
	}

	/**
	 *	Get target address for route of type "forward".
	 *	@access		public
	 *	@return		string
	 */
	public function getTarget(): string
	{
		return $this->target;
	}

	public function isMethod( string $method ): bool
	{
		if( $this->method === '*' )
			return TRUE;
		$methods	= explode( ',', $this->method );
		return in_array( strtoupper( $method ), $methods, TRUE );
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$action			Name of method to call on controller class
	 *	@return		Route
	 */
	public function setAction( string $action ): self
	{
		if( preg_match( '/^(_|[a-z0-9])+$/i', $action ) === 0 )
			throw new InvalidArgumentException( 'Action must be a valid method name' );
		$this->action		= $action;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		array		$arguments		Map of
	 *	@return		Route
	 */
	public function setArguments( array $arguments ): self
	{
		$this->arguments	= $arguments;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$controller		...
	 *	@return		Route
	 */
	public function setController( string $controller ): self
	{
		if( preg_match( '/^(_|\\\|[a-z0-9])+$/i', $controller ) === 0 )
			throw new InvalidArgumentException( 'Controller must be a valid class name' );
		$this->controller	= $controller;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$method			...
	 *	@return		Route
	 *	@throws		DomainException			if given method is invalid or not supported
	 */
	public function setMethod( string $method ): self
	{
		$validMethods	= [];
		$methods		= preg_split( '/\s*(,|\|)\s*/', strtoupper( trim( $method ) ) );
		if( $methods !== FALSE ){
			if( in_array( '*', $methods, TRUE ) )
				$validMethods	= array( '*' );
			else{
				foreach( $methods as $item ){
					if( strlen( trim( $item ) ) === 0 )
						continue;
					if( !in_array( $item, $this->supportedMethods, TRUE ) )
						throw new DomainException( 'Invalid method: '.$item );
					$validMethods[]	= $item;
				}
			}
			$this->method	= join( ',', $validMethods );
		}
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		int			$mode			Mode as constant value (int)
	 *	@return		Route
	 *	@throws		DomainException			if given mode value is no a valid constant value (int)
	 */
	public function setMode( int $mode ): self
	{
		self::getModeKey( $mode );
		$this->mode	= $mode;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		Route		$origin
	 *	@return		Route
	 */
	public function setOrigin( Route $origin ): self
	{
		$this->origin	= $origin;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$pattern
	 *	@return		Route
	 */
	public function setPattern( string $pattern ): self
	{
		if( $this->method !== 'CLI' && preg_match( '/\s/', $pattern ) > 0 )
			throw new InvalidArgumentException( 'Route pattern must not contain whitespace ('.$pattern.')' );
		$this->pattern		= $pattern;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		int			$priority
	 *	@return		Route
	 *	@throws		RangeException		if priority is not supported (= within [1-5])
	 */
	public function setPriority( int $priority ): self
	{
		self::getPriorityKey( $priority );
		$this->priority		= $priority;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		array		$roles
	 *	@return		Route
	 */
	public function setRoles( array $roles ): self
	{
		$this->roles		= $roles;
		return $this;
	}

	/**
	 *	Set target address for route of type "forward".
	 *	@access		public
	 *	@param		string		$target
	 *	@return		Route
	 */
	public function setTarget( string $target ): self
	{
		$this->target	= $target;
		return $this;
	}

	/**
	 *	Returns route as array.
	 *	@access		public
	 *	@return		array
	 */
	public function toArray(): array
	{
		return [
			'id'			=> $this->getId(),
			'mode'			=> $this->mode,
			'method'		=> $this->method,
			'pattern'		=> $this->pattern,
			'controller'	=> $this->controller,
			'action'		=> $this->action,
			'arguments'		=> $this->arguments,
			'roles'			=> $this->roles,
			'origin'		=> $this->origin,
			'target'		=> $this->target,
			'priority'		=> $this->priority,
		];
	}
}
