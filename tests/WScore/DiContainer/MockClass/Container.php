<?php
namespace WScore\tests\DiContainer\MockClass;

use \WScore\DiContainer\ContainerInterface;

class Container implements ContainerInterface
{

    public function set( $id, $value, $option = null )
    {
    }

    public function setOption( $id, $option )
    {
    }

    public function exists( $id )
    {
        return true;
    }

    public function get( $id, $option = array() )
    {
        return $id;
    }
}