<?php
namespace CeusMedia\Router\Route;

class PatternPart
{
	public string $key;

	public bool $optional	= FALSE;

	public bool $argument	= FALSE;

	public mixed $value		= NULL;

	public static function create( string $key, bool $optional = FALSE, bool $argument = FALSE, mixed $value = NULL ): self
	{
		$object				= new self();
		$object->key		= $key;
		$object->optional	= $optional;
		$object->argument	= $argument;
		$object->value		= $value;
		return $object;
	}
}