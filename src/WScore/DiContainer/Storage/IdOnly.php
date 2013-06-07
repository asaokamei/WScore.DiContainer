<?php
namespace WScore\DiContainer\Storage;

class IdOnly extends StorageAbstract
{
    /**
     * check if the $name key exists.
     *
     * @param string       $name
     * @param null|string  $namespace
     * @return bool
     */
    public function exists( $name, $namespace=null )
    {
        return array_key_exists( $name, $this->cache );
    }

    /**
     * @param string      $name
     * @param mixed       $value
     * @param null|string $namespace
     * @return void
     */
    public function store( $name, $value, $namespace = null )
    {
        $this->cache[ $name ] = $value;
    }

    /**
     * @param             $name
     * @param null|string $namespace
     * @return mixed
     */
    public function fetch( $name, $namespace = null )
    {
        return array_key_exists( $name, $this->cache ) ? $this->cache[ $name ] : null;
    }
}