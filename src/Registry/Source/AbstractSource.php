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
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@package		CeusMedia_Router_Registry_Source
 *	@copyright		2016-2024 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */

namespace CeusMedia\Router\Registry\Source;

use CeusMedia\Router\Registry;
use InvalidArgumentException;
use RuntimeException;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Registry_Source
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2024 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
abstract class AbstractSource implements SourceInterface
{
	/** @var	array		$instances		List of ... */
	protected static array $instances		= [];

	/** @var	array		$options		Map of ... */
	protected array $options				= [];

	/** @var	string		$resource		... */
	protected string $resource				= '';

	/**
	 *	@param		string|NULL		$resource
	 *	@param		array			$options
	 *	@throws		InvalidArgumentException		if an option key is not of integer
	 */
	public function __construct( string $resource = NULL, array $options = [] )
	{
		if( strlen( trim( (string) $resource ) ) > 0 )
			$this->setResource( (string) $resource );
		$defaultOptions	= array( SourceInterface::OPTION_AUTOLOAD => TRUE );
		$mergedOptions	= $options + $defaultOptions;
		foreach( $mergedOptions as $key => $value ){
			if( !is_int( $key ) )
				throw new InvalidArgumentException( 'Option key must be integer' );
			$this->setOption( $key, $value );
		}
	}

	/**
	 *	@param		string|NULL		$resource
	 *	@param		array			$options
	 *	@return		SourceInterface
	 *	@throws		RuntimeException				if create is called on abstract class
	 *	@throws		InvalidArgumentException		if an option key is not of integer
	 */
	public static function create( string $resource = NULL, array $options = [] ): SourceInterface
	{
		$class	= get_called_class();
		if( AbstractSource::class === $class )
			throw new RuntimeException( 'Cannot create instance of abstract class' );
		return new $class( $resource, $options );
	}

	/**
	 *	@param		int			$key
	 *	@return		mixed|NULL
	 */
	public function getOption( int $key ): mixed
	{
		if( array_key_exists( $key, $this->options ) )
			return $this->options[$key];
		return NULL;
	}

	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 *	@return		string|NULL
	 */
	public function getResource(): ?string
	{
		return $this->resource;
	}

	/**
	 *	@param		Registry		$registry
	 *	@return		int
	 */
	abstract public function load( Registry $registry ): int;

	/**
	 *	@param		Registry		$registry
	 *	@return		int
	 */
	abstract public function save( Registry $registry ): int;

	/**
	 *	@param		int				$key
	 *	@param		mixed|NULL		$value
	 *	@return		$this
	 */
	public function setOption( int $key, mixed $value = NULL ): self
	{
		if( $value === NULL && array_key_exists( $key, $this->options ) )
			unset( $this->options[$key] );
		else
			$this->options[$key]	= $value;
		return $this;
	}

	/**
	 *	@param		string		$resource
	 *	@return		$this
	 */
	public function setResource( string $resource ): AbstractSource
	{
		$this->resource	= $resource;
		return $this;
	}
}
