<?php

namespace Aero\Supports;

# 30 Seconds To Mars - Stranger In A Strange Land

use Aero\
{
	Database\Migrate,
	Interfaces\Instance
};

use Throwable;
use Exception AS Error;

final class Lerma extends Migrate #implements Instance
{
	public const 
		FETCH_NUM		= 1,
		FETCH_ASSOC		= 2,
		FETCH_OBJ		= 4,
		FETCH_BIND		= 663,
		FETCH_COLUMN	= 265,
		FETCH_KEY_PAIR	= 307,
		FETCH_NAMED		= 173,
		FETCH_UNIQUE	= 333,
		FETCH_GROUP		= 428,
		FETCH_FUNC		= 586,
		FETCH_CLASS		= 977,
		FETCH_CLASSTYPE	= 473;
	
	/* public static function select( array $execute, callable $callable )
	{
		static::load( __METHOD__, ( $execute ?: NULL ), $callable );
	}
	public static function insert( array $execute, callable $callable )
	{
		
	}
	public static function create( array $execute, callable $callable )
	{
		
	}
	public static function delete( array $execute, callable $callable )
	{
		
	} */
	public static function __callStatic( $method, $args )
	{
		try
		{
			if ( $method === 'prepare' )
			{
				if ( empty ( $args[1] ) )
				{
					throw new Error( 'Данные пусты. Используйте функцию query' );
				}
				
				[ $sql, $execute ] = $args;
				
				static::instance() -> dead() -> replaceHolders( $sql );
				
				$statement = static::prepare( $sql );
				
				if ( static::instance() -> isMulti( $execute ) )
				{
					static::instance() -> driver -> beginTransaction();
					
					$e = $statement -> multiExecute( $execute );
					
					static::instance() -> driver -> commit();
				}
				else
				{
					$e = $statement -> execute( $execute );
				}
				
				return $e;
			}
			elseif ( $method === 'query' )
			{
				return static::query( ...$args );
			}
			
			return static::instance() -> driver -> $method( ...$args );
		}
		catch ( Throwable $t )
		{
			static::instance() -> driver -> rollBack();
			
			$this -> exceptionIDriver( $t );
		}
	}
	
	public function __call( $method, $args )
	{
		if ( in_array ( $method, [ 'fetch', 'fetchAll' ] ) )
		{
			try
			{
				return $this -> $method( ...$args );
			}
			catch ( Throwable $t )
			{
				$this -> exceptionIDriver( $t );
			}
		}
		
		return null;
	}
	
	protected function exceptionIDriver( Throwable $t )
	{
		exit ( sprintf ( '<pre>IDriver: %s' . PHP_EOL . '%s</pre>', $t -> getMessage(), $t -> getTraceAsString() ) );
	}
}