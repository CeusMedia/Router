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
 *	@link			https://github.com/CeusMedia/Router
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
class Route{

	protected $method;
	protected $pattern;
	protected $controller;
	protected $action;
	protected $arguments		= array();
	protected $origin;

	public function __construct( $controller, $action, $pattern, $method = "GET" ){
		$this->setController( $controller );
		$this->setAction( $action );
		$this->setPattern( $pattern );
		$this->setMethod( $method );
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
		return md5( $this->pattern.'@'.$this->method );
	}

	public function getMethod(){
		return $this->method;
	}

	public function getOrigin(){
		return $this->origin;
	}

	public function getPattern(){
		return $this->pattern;
	}

	public function isMethod( $method ){
		if( $this->method === '*' || $this->method === strtoupper( $method ) )
			return TRUE;
		return FALSE;
	}

	public function setAction( $action ){
		$this->action		= $action;
	}

	public function setArguments( $map ){
		$this->arguments	= $map;
	}

	public function setController( $controller ){
		$this->controller	= $controller;
	}

	public function setMethod( $method ){
		$this->method		= strtoupper( $method );
	}

	public function setOrigin( Route $origin ){
		$this->origin	= $origin;
	}

	public function setPattern( $pattern ){
		$this->pattern		= $pattern;
	}
}
?>
