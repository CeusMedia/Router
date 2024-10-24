<?php
declare(strict_types=1);

/**
 *	...
 *
 *	Copyright (c) 2016-2023 Christian Würker (ceusmedia.de)
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
 *	@copyright		2016-2023 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */

namespace CeusMedia\Router;

use DateTime;
use DateTimeZone;
use DomainException;
use RuntimeException;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2023 Christian Würker
 *	@license		https://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Log
{
	const LEVEL_NONE	= 0;
	const LEVEL_ERROR	= 1;
	const LEVEL_WARN	= 2;
	const LEVEL_INFO	= 4;
	const LEVEL_DEBUG	= 16;
	const LEVEL_ALL		= self::LEVEL_ERROR | self::LEVEL_WARN | self::LEVEL_INFO | self::LEVEL_DEBUG;

	const LEVELS	= [
		self::LEVEL_NONE,
		self::LEVEL_ERROR,
		self::LEVEL_WARN,
		self::LEVEL_INFO,
		self::LEVEL_DEBUG,
		self::LEVEL_ALL,
	];

	const LEVEL_KEY_NONE	= 'none';
	const LEVEL_KEY_ERROR	= 'error';
	const LEVEL_KEY_WARN	= 'warn';
	const LEVEL_KEY_INFO	= 'info';
	const LEVEL_KEY_DEBUG	= 'debug';
	const LEVEL_KEY_ALL		= 'all';

	const LEVEL_KEYS	= [
		self::LEVEL_KEY_NONE,
		self::LEVEL_KEY_ERROR,
		self::LEVEL_KEY_WARN,
		self::LEVEL_KEY_INFO,
		self::LEVEL_KEY_DEBUG,
		self::LEVEL_KEY_ALL,
	];

	const LEVEL_KEYS_BY_LEVEL	= [
		self::LEVEL_NONE	=> self::LEVEL_KEY_NONE,
		self::LEVEL_ERROR	=> self::LEVEL_KEY_ERROR,
		self::LEVEL_WARN	=> self::LEVEL_KEY_WARN,
		self::LEVEL_INFO	=> self::LEVEL_KEY_INFO,
		self::LEVEL_DEBUG	=> self::LEVEL_KEY_DEBUG,
		self::LEVEL_ALL		=> self::LEVEL_KEY_ALL,
	];

	const LEVELS_BY_KEY	= [
		self::LEVEL_KEY_NONE	=> self::LEVEL_NONE,
		self::LEVEL_KEY_ERROR	=> self::LEVEL_ERROR,
		self::LEVEL_KEY_WARN	=> self::LEVEL_WARN,
		self::LEVEL_KEY_INFO	=> self::LEVEL_INFO,
		self::LEVEL_KEY_DEBUG	=> self::LEVEL_DEBUG,
		self::LEVEL_KEY_ALL		=> self::LEVEL_ALL,
	];

	public static int $level	= 0;

	public static ?string $file	= NULL;

	/**
	 *	@param		int|string		$levelOrLevelKey
	 *	@param		string			$message
	 *	@param		mixed|NULL		$data
	 *	@return		bool
	 */
	public static function add( int|string $levelOrLevelKey, string $message, mixed $data = NULL ): bool
	{
		return static::_logByLevelOrLevelKey( $levelOrLevelKey, $message, $data );
	}

	/**
	 *	@param		string			$message
	 *	@param		mixed|NULL		$data
	 *	@return		bool
	 */
	public static function debug( string $message, mixed $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_DEBUG, $message, $data );
	}

	/**
	 *	@param		string			$message
	 *	@param		mixed|NULL		$data
	 *	@return		bool
	 */
	public static function error( string $message, mixed $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_ERROR, $message, $data );
	}

	/**
	 *	@param		string			$message
	 *	@param		mixed|NULL		$data
	 *	@return		bool
	 */
	public static function info( string $message, mixed $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_INFO, $message, $data );
	}

	/**
	 *	@param		string			$message
	 *	@param		mixed|NULL		$data
	 *	@return		bool
	 */
	public static function warn( string $message, mixed $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_WARN, $message, $data );
	}

	/**
	 *	@param		int				$level
	 *	@param		string			$message
	 *	@param		mixed|NULL		$data
	 *	@return		bool
	 */
	protected static function _logByLevel( int $level, string $message, mixed $data = NULL ): bool
	{
		if( ( self::$level & $level ) !== $level )
			return FALSE;
		if( is_null( self::$file ) || strlen( trim( self::$file ) ) === 0 )
			throw new RuntimeException( 'No log file set' );
		$date	= new DateTime( 'now', new DateTimeZone( 'Europe/Berlin' ) );
		$entry	= vsprintf( '%s %s %s', array(
			$date->format( DATE_ATOM ),
			strtoupper( self::LEVEL_KEYS_BY_LEVEL[$level] ),
			$message
		) );
		error_log( $entry.PHP_EOL, 3, self::$file );
		if( NULL !== $data )
			error_log( print_r( $data, TRUE ).PHP_EOL, 3, self::$file );
		return TRUE;
	}

	/**
	 *	@param		int|string		$level
	 *	@param		string			$message
	 *	@param		mixed|NULL		$data
	 *	@return		bool
	 */
	protected static function _logByLevelOrLevelKey( int|string $level, string $message, mixed $data = NULL ): bool
	{
		if( is_string( $level ) ){
			if( !in_array( $level, self::LEVEL_KEYS, TRUE ) )
				throw new DomainException( 'Invalid log level key' );
			$level	= self::LEVELS_BY_KEY[$level];
		}
		if( !in_array( $level, self::LEVELS, TRUE ) )
			throw new DomainException( 'Invalid log level' );

		return static::_logByLevel( $level, $message, $data );
	}
}
