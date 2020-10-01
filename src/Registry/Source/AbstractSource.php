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

use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\SourceInterface;

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
abstract class AbstractSource
{
	/** @var	array		$instances		List of ... */
	protected static $instances	= array();

	/** @var	array		$options		Map of ... */
	protected $options			= array();

	/** @var	string		$resource		... */
	protected $resource;

	public function __construct( string $resource = NULL, array $options = array() )
	{
		if( strlen( trim( (string) $resource ) ) > 0 )
			$this->setResource( (string) $resource );
		$defaultOptions	= array( SourceInterface::OPTION_AUTOLOAD => TRUE );
		$mergedOptions	= $options + $defaultOptions;
		foreach( $mergedOptions as $key => $value ){
			if( !is_int( $key ) )
				throw new \InvalidArgumentException( 'Option key must be integer' );
			$this->setOption( $key, $value );
		}
	}

	public static function create( string $resource = NULL, array $options = array() ): AbstractSource
	{
		$class	= get_called_class();
		if( $class === self::CLASS )
			throw new \RuntimeException( 'Cannot create instance of abstract class' );
		return new $class( $resource, $options );
	}

	public function getOption( int $key )
	{
		if( array_key_exists( $key, $this->options ) )
			return $this->options[$key];
		return NULL;
	}

	public function getOptions(): array
	{
		return $this->options;
	}

	public function getResource(): ?string
	{
		return $this->resource;
	}

	abstract public function load( Registry $registry ): int;

	abstract public function save( Registry $registry ): int;

	public function setOption( int $key, $value = NULL ): self
	{
		if( $value === NULL && array_key_exists( $key, $this->options ) )
			unset( $this->options[$key] );
		else
			$this->options[$key]	= $value;
		return $this;
	}

	public function setResource( string $resource ): AbstractSource
	{
		$this->resource	= $resource;
		return $this;
	}
}
