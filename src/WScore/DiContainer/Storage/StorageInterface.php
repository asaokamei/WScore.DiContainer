<?php
namespace WScore\DiContainer\Storage;

interface StorageInterface
{
    /**
     * @param string       $name
     * @param null|string  $namespace
     * @return bool
     */
    public function exists( $name, $namespace=null );
    
    /**
     * @param string      $name
     * @param mixed       $value
     * @param null|string $namespace
     * @return void
     */
    public function store( $name, $value, $namespace = null );


    /**
     * @param             $name
     * @param null|string $namespace
     * @return mixed
     */
    public function fetch( $name, $namespace = null );

    /**
     * clears the contents.
     */
    public function clear();


    /**
     * @param string $id
     * @param string $name1
     * @param string $name2
     */
    public function resetNamespace( $id, $name1, $name2 );
}