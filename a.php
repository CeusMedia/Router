<?php
namespace Test\AbstractSingleton;

abstract class A{
	public $v	= 'a';
	protected static $instances	= array();
	public static function getInstance(): A
	{
		$class	= get_called_class();
		print('called class: '.$class.PHP_EOL);
		print('self class: '.self::CLASS.PHP_EOL);
		if( $class === self::CLASS )
			throw new \RuntimeException( 'Cannot get instance of abstract class' );
		if( !isset( self::$instances[$class] ) )
			self::$instances[$class]	= new $class();
		return self::$instances[$class];
	}
}
class B extends A{
	public $v	= 'b';
}

$i1	= B::getInstance();
print('class: '.get_class($i1).PHP_EOL);
print('value: '.$i1->v.PHP_EOL);

$i2	= A::getInstance();
print('class: '.get_class($i2).PHP_EOL);
print('value: '.$i2->v.PHP_EOL);
