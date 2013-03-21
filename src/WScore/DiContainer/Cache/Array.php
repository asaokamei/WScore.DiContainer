<?php
namespace WScore\DiContainer;

class Cache_Apc implements Cache_Interface
{
    // +----------------------------------------------------------------------+
    //  Caching using APC.
    // +----------------------------------------------------------------------+
    /**
     *
     */
    public function __construct()
    {
        if( !function_exists( 'apc_store' ) ) {
            throw new \RuntimeException( 'apc function not available.' );
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function store( $name, $value ) {
        apc_store( $name, $value );
    }

    /**
     * @param $name
     * @return mixed
     */
    public function fetch( $name ) {
        return apc_fetch( $name );
    }
    // +----------------------------------------------------------------------+
    /**
     */
    public function clear() {
        apc_clear_cache( 'opcode' );
        apc_clear_cache( 'user' );
    }
}