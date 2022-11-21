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
 *	@package		CeusMedia_Router_Registry
 *	@copyright		2016-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
namespace CeusMedia\Router\Registry;

use CeusMedia\Router\Registry;
use CeusMedia\Router\Registry\Source\SourceInterface as SourceInterface;
use Exception;
use RuntimeException;

/**
 *	...
 *
 *	@category		Library
 *	@package		CeusMedia_Router_Registry
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2016-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Router
 */
class Source
{
	/** @var	array		$sources		List of ... */
	protected array $sources		= [];

	/**
	 *	...
	 *	@access		public
	 *	@param		SourceInterface		$source		...
	 *	@return		self
	 */
	public function addSource( SourceInterface $source ): self
	{
		$this->sources[] = $source;
		return $this;
	}

	public function load( Registry $registry, bool $strict = TRUE ): int
	{
		$nrLoadedSources	= 0;
		if( count( $this->sources ) === 0 )
			throw new RuntimeException( 'No registry sources set' );
		$loadSource	= NULL;
		for( $i=0; $i<count($this->sources); $i++ ){
			$result		= 0;
			$loadSource	= $this->sources[$i];
			try{
				if( $loadSource->getOption( SourceInterface::OPTION_AUTOLOAD ) ){
					$result 	= $loadSource->load( $registry );
					if( $result >= 0 ){
						for( $j=$i-1; $j>=0; $j-- ){
							$saveSource = $this->sources[$j];
							if( $saveSource->getOption( SourceInterface::OPTION_AUTOLOAD ) )
								if( $saveSource->getOption( SourceInterface::OPTION_AUTOSAVE ) )
									$saveSource->save( $registry );
						}
						break;
					}
				}
				$nrLoadedSources	+= $result;
			}
			catch( Exception $e ){
				if( $strict )
					throw new RuntimeException( 'Loading source failed', 0, $e );
			}
		}
		return $nrLoadedSources;
	}

	/**
	 *	Saves registry to all registered sources with autosave option enabled.
	 *	@access		public
	 *	@param		Registry		$registry		Registry to safe to sources
	 *	@return		integer			Number of changes sources
	 */
	public function save( Registry $registry ): int
	{
		$counter = 0;
		for( $i=0; $i<count($this->sources); $i++ ){
			$saveSource	= $this->sources[$i];
			if( $saveSource->getOption( SourceInterface::OPTION_AUTOSAVE ) ){
				$saveSource->save( $registry );
				$counter++;
			}
		}
		return $counter;
	}
}
