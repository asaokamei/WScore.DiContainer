<?php
namespace WScore\DiContainer;

class Cache
{
    public static $useMem  = false;
    public static $useApc  = true;
    public static $useFile = false;
    // +----------------------------------------------------------------------+
    public static function cacheOn( $on=true ) {
        if( $on === false ) {
            self::$useMem  = false;
            self::$useApc  = false;
            self::$useFile = false;
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