<?php
namespace WScore\DiContainer\Storage;

class IdWithNamespace extends StorageAbstract
{
    /**
     * @param string      $name
     * @param mixed       $value
     * @param null|string $namespace
     * @return void
     */
    public function store( $name, $value, $namespace = null )
    {
        $id = $this->named( $name, $namespace );
        $this->cache[ $id ] = $value;
    }

    /**
     * @param             $name
     * @param null|string $namespace
     * @return mixed
     */
    public function fetch( $name, $namespace = null )
    {
        $id = $this->named( $name, $namespace );
        return array_key_exists( $id, $this->cache ) ? $this->cache[ $id ] : null;
    }
}