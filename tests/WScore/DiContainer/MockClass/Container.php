<?php
namespace WScore\tests\DiContainer\MockClass;

use \WScore\DiContainer\ContainerInterface;

class Container implements ContainerInterface
{

    public function set( $id, $value=null )
    {
    }

    public function setOption( $id, $option )
    {
    }

    public function has( $id )
    {
        return true;
    }

    public function get( $id )
    {
        return $id;
    }

    public function getNamespace()
    {
    }

    public function setNamespace( $namespace = null )
    {
    }

    public function load( $id )
    {
        return $this->get( $id );
    }
}