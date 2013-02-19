<?php
namespace WScore\DiContainer;

class Cache_None implements Cache_Interface
{
    // +----------------------------------------------------------------------+
    //  No Caching.
    // +----------------------------------------------------------------------+
    /**
     *
     */
    public function __construct() {}

    /**
     * @param $name
     * @param $value
     */
    public function store( $name, $value ) {}

    /**
     * @param $name
     * @return bool
     */
    public function fetch( $name ) {
        return false;
    }
    // +----------------------------------------------------------------------+
    /**
     */
    public function clear() {}
}