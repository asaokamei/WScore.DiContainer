<?php
namespace WScore\DiContainer;

interface Cache_Interface
{
    /**
     * @param string $name
     * @param mixed $value
     */
    public function store( $name, $value );

    /**
     * @param string $name
     * @return mixed
     */
    public function fetch( $name );

    /**
     */
    public function clear();
}
