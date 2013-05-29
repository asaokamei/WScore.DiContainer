<?php
namespace WScore\DiContainer;

class Singleton
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
        $this->cache[ $name ] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function fetch( $name ) {
        return array_key_exists( $name, $this->cache ) ? $this->cache[ $name ] : false;
    }
    // +----------------------------------------------------------------------+
    /**
     */
    public function clear() {
        $this->cache = array();
    }
}
