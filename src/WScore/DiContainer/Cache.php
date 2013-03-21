<?php
namespace WScore\DiContainer;

class Cache
{
    public static $useMem  = false;
    public static $useApc  = true;
    public static $useFile = false;
    public static $useArray= false;
    // +----------------------------------------------------------------------+
    public static function cacheOn( $on='apc' ) {
        self::$useMem  = false;
        self::$useApc  = false;
        self::$useFile = false;
        self::$useArray= false;
        if( $on === 'apc' ) {
            self::$useApc = true;
        } elseif( $on === 'memcache' ) {
            self::$useMem = true;
        } elseif( $on === 'array' ) {
            self::$useArray = true;
        } else {
            self::$useFile = $on;
        }
    }

    /**
     * @param null $location
     * @return Cache_Interface
     * @throws \RuntimeException
     */
    public static function getCache( $location=null )
    {
        $cache = 'None';
        if( self::$useMem && class_exists( 'Memcached' ) ) {
            $cache = 'Memcache';
        }
        elseif( self::$useApc && function_exists( 'apc_store' ) ) {
            $cache = 'Apc';
        }
        elseif( self::$useArray ) {
            $cache = 'Array';
        }
        elseif( self::$useFile && isset( $location ) ) {
            $cache = 'File';
        }
        $cache = '\WScore\DiContainer\Cache_' . $cache;
        if( !class_exists( $cache ) ) {
            throw new \RuntimeException( "no such cache: $cache" );
        }
        return new $cache();
    }
    // +----------------------------------------------------------------------+
}