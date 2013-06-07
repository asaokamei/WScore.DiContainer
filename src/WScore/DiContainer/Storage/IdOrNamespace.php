<?php
namespace WScore\DiContainer\Storage;

class IdOrNamespace extends StorageAbstract
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
        if( $namespace ) {
            $id = $this->named( $name, $namespace );
            if( array_key_exists( $id, $this->cache ) ) {
                return $this->cache[ $id ];
            }
        }
        $id = $name;
        return array_key_exists( $id, $this->cache ) ? $this->cache[ $id ] : null;
    }
}