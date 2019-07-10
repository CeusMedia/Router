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
class Route{

	const MODE_UNKNOWN		= 0;
	const MODE_CONTROLLER	= 1;
	const MODE_EVENT		= 2;
	const MODE_FORWARD		= 3;

	protected $method			= 'GET';
	protected $mode				= self::MODE_CONTROLLER;
	protected $pattern;
	protected $controller;
	protected $action;
	protected $arguments		= array();
	protected $roles			= array();
	protected $origin;

	public $supportedMethods	= array(
		'GET',
		'HEAD',
		'POST',
		'PUT',
		'DELETE',
		'OPTIONS',
	);

	public function __construct( string $pattern, string $method = NULL, int $mode = NULL ){
		$this->setPattern( $pattern );
		if( !is_null( $method ) )
			$this->setMethod( $method );
		if( !is_null( $mode ) )
			$this->setMode( $mode );
	}

	public function getAction(){
		return $this->action;
	}

	public function getArguments(){
		return $this->arguments;
	}

	public function getController(){
		return $this->controller;
	}

	public function getId(){
		return md5( $this->method.'::'.$this->pattern );
	}

	public function getMethod(){
		return $this->method;
	}

	public function getMode(){
		return $this->mode;
	}

	public function getOrigin(){
		return $this->origin;
	}

	public function getPattern(){
		return $this->pattern;
	}

	public function getRoles(){
		return $this->roles;
	}

	public function isMethod( string $method ){
		if( $this->method === '*' )
			return TRUE;
		$methods	= explode( ',', $this->method );
		if( in_array( strtoupper( $method ), $methods ) )
			return TRUE;
		return FALSE;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$action			Name of method to call on controller class
	 *	@return		self
	 */
	public function setAction( string $action ){
		if( !preg_match( '/^(_|[a-z0-9])+$/i', $action ) )
			throw new \InvalidArgumentException( 'Action must be a valid method name' );
		$this->action		= $action;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		array		$arguments		Map of 
	 *	@return		self
	 */
	public function setArguments( $arguments ){
		$this->arguments	= $arguments;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$controller		...
	 *	@return		self
	 */
	public function setController( $controller ){
		if( !preg_match( '/^(_|\\\|[a-z0-9])+$/i', $controller ) )
			throw new \InvalidArgumentException( 'Controller must be a valid class name' );
		$this->controller	= $controller;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$method			...
	 *	@return		self
	 *	@throws		RangeException				if given method is invalid or not supported
	 */
	public function setMethod( $method ): self
	{
		$validMethods	= array();
		$methods		= preg_split( '/\s*(,|\|)\s*/', strtoupper( trim( $method ) ) );
		if( in_array( '*', $methods ) )
			$validMethods	= array( '*' );
		else {
			foreach( $methods as $item ){
				if( !strlen( trim( $item ) ) )
					continue;
				if( !in_array( $item, $this->supportedMethods ) )
					throw new \RangeException( 'Invalid method: '.$item );
				$validMethods[]	= $item;
			}
		}
		$this->method		= join( ',', $validMethods );
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		int			$mode			Mode as constant value (int)
	 *	@return		self
	 *	@throws		InvalidArgumentException	if given mode value is no a valid constant value (int)
	 */
	public function setMode( $mode ): self
	{
		if( !preg_match( '/^[0-9]+$/', $mode ) )
			throw new InvalidArgumentException( 'Invalid mode: '.$mode );
		$this->mode	= $mode;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		Route		$route
	 *	@return		self
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
	 *	@return		self
	 */
	public function setPattern( $pattern ): self
	{
		$pattern			= str_replace( ' ', '', $pattern );
		$this->pattern		= $pattern;
		return $this;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		array		$roles
	 *	@return		self
	 */
	public function setRoles( $roles ): self
	{
		$this->roles		= $roles;
		return $this;
	}

	public function toArray(){
		return array(
			'id'			=> $this->getId(),
			'mode'			=> $this->mode,
			'method'		=> $this->method,
			'pattern'		=> $this->pattern,
			'controller'	=> $this->controller,
			'action'		=> $this->action,
			'arguments'		=> $this->arguments,
			'roles'			=> $this->roles,
			'origin'		=> $this->origin,
		);
	}
}
?>
