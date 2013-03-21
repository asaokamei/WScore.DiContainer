<?php
namespace WScore\DiContainer;

class Cache_Array implements Cache_Interface
{
    // +----------------------------------------------------------------------+
    //  Caching using simple array. Mostly for testing purpose.
    // +----------------------------------------------------------------------+
    private $cache = array();

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function store( $name, $value ) {
        $this->cache[ $name ] = serialize( $value );
    }

    /**
     * @param $name
     * @return mixed
     */
    public function fetch( $name ) {
        return array_key_exists( $name, $this->cache ) ? unserialize( $this->cache[ $name ] ) : false;
    }
    // +----------------------------------------------------------------------+
    /**
     */
    public function clear() {
        $this->cache = array();
    }
}