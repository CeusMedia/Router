<?php
namespace CeusMedia\Router;

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

	public static $level	= 0;

	public static $file;

	public static function add( $levelOrLevelKey, string $message, $data = NULL ): bool
	{
		return static::_logByLevelOrLevelKey( $levelOrLevelKey, $message, $data );
	}

	public static function debug( string $message, $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_DEBUG, $message, $data );
	}

	public static function error( string $message, $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_ERROR, $message, $data );
	}

	public static function info( string $message, $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_INFO, $message, $data );
	}

	public static function warn( string $message, $data = NULL ): bool
	{
		return static::_logByLevel( self::LEVEL_WARN, $message, $data );
	}

	protected static function _logByLevel( int $level, string $message, $data = NULL ): bool
	{
		if( !( self::$level & $level ) )
			return FALSE;
		if( !self::$file )
			throw new \RuntimeException( 'No log file set' );
		$date	= new \DateTime( 'now', new \DateTimeZone( 'Europe/Berlin' ) );
		$entry	= vsprintf( '%s %s %s', array(
			$date->format( DATE_ATOM ),
			strtoupper( self::LEVEL_KEYS_BY_LEVEL[$level] ),
			$message
		) );
		error_log( $entry.PHP_EOL, 3, self::$file );
		if( $data )
			error_log( print_r( $data, TRUE ).PHP_EOL, 3, self::$file );
		return TRUE;
	}

	protected static function _logByLevelOrLevelKey( $level, string $message, $data = NULL ): bool
	{
		if( is_string( $level ) ){
			if( !in_array( $level, self::LEVEL_KEYS ) )
				throw new \DomainException( 'Invalid log level key' );
			$level	= self::LEVELS_BY_KEY[$level];
		}
		if( !is_int( $level ) )
			throw new \InvalidArgumentException( 'Invalid log level' );
		if( !in_array( $level, self::LEVELS ) )
			throw new \DomainException( 'Invalid log level' );

		return static::_logByLevel( $level, $message, $data );
	}
}
