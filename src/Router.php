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

use CeusMedia\Router\Registry\Source\JsonFile as JsonFileSource;
use CeusMedia\Router\Registry\Source\JsonFolder as JsonFolderSource;
use CeusMedia\Router\Registry\Source\SourceInterface;
use RuntimeException;

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
class Router
{
	/** @var	string|NULL		$method			Request method, set by setMethod */
	protected ?string $method;

	/** @var	Registry		$registry		Registry for routes and route sources */
	protected Registry $registry;

	/** @var	array			$options		Map of options, usable by inheriting classes */
	protected array $options	= [];

	/**
	 *	Static constructor.
	 *	@access		public
	 *	@param		array		$options	Map of options
	 *	@return		self
	 */
	public static function create( array $options = [] ): self
	{
		return new self( $options );
	}

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		array		$options	Map of options
	 *	@return		void
	 */
	public function __construct( array $options = [] )
	{
		$this->options	= $options + $this->options;
		$this->registry	= new Registry();
		$this->method	= PHP_SAPI === 'cli' ? 'CLI' : NULL;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		Route		$route			Route instance to add
	 *	@return		self 		This instance for method chaining
	 */
	public function addRoute( Route $route ): self
	{
		$this->registry->add( $route );
		return $this;
	}

	/**
	 *	Returns router registry object.
	 *	@access		public
	 *	@return		Registry 	Router registry object
	 */
	public function getRegistry(): Registry
	{
		return $this->registry;
	}

	/**
	 *	...
	 *	@access		public
	 *	@return		Route[] 		List of route instances
	 */
	public function getRoutes(): array
	{
		return $this->registry->index();
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$controller		...
	 *	@return		array 		List of route instances
	 */
	public function getRoutesByController( string $controller ): array
	{
		return $this->registry->indexByController( $controller );
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string			$filePath		Path to routes file
	 *	@param		string|NULL		$folderPath		Path to folder with routes files to assemble
	 *	@return		void
	 *	@deprecated use Registry::addSource( new JsonFile( $filePath ) ) instead
	 *	@example
	 *	use CeusMedia\Router\Router;
	 *	use CeusMedia\Router\Registry\Source\JsonFile;
	 *	$router	= new Router();
	 *	$router->getRegistry()->addSource( new JsonFile( $source ) );
	 */
	public function loadRoutesFromJsonFile( string $filePath, ?string $folderPath = NULL ): void
	{
		$sourceFile	= new JsonFileSource( $filePath );
		$sourceFile->setOption( SourceInterface::OPTION_AUTOSAVE, TRUE );
		$sourceFolder = new JsonFolderSource( $folderPath );
		$this->registry->addSource( $sourceFile );
		$this->registry->addSource( $sourceFolder );
//		$this->registry->loadFromJsonFile( $filePath, $folderPath );
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$path			...
	 *	@param		boolean		$strict			Flag: resolve in strict mode
	 *	@return		Route|NULL
	 *	@throws		ResolverException			if path is not a resolvable route
	 */
	public function resolve( string $path, bool $strict = TRUE ): ?Route
	{
		Log::debug( 'Router > resolve: path => '.$path );
		if( is_null( $this->method ) || strlen( $this->method ) === 0 )
			throw new RuntimeException( 'No method set' );
		$resolver	= new Resolver( $this->registry );
		return $resolver->resolve( $path, $this->method, $strict );
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$filePath		Path to routes file
	 *	@return		self 		This instance for method chaining
	 */
/*	public function saveRoutes( string $filePath ): self
	{
		$this->registry->save( $filePath );
		return $this;
	}*/

	/**
	 *	Returns router registry object.
	 *	@access		public
	 *	@param		Registry	$registry		Registry object to set
	 *	@return		self 		This instance for method chaining
	 */
	public function setRegistry( Registry $registry ): self
	{
		$this->registry	= $registry;
		return $this;
	}

	/**
	 *	Set HTTP method of call to resolve.
	 *	@access		public
	 *	@param		string		$method			HTTP method of call to resolve (GET|POST|PUT|DELETE)
	 *	@return		self 		This instance for method chaining
	 */
	public function setMethod( string $method ): self
	{
		$this->method	= $method;
		return $this;
	}
}
