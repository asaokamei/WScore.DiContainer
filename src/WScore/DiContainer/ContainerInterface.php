<?php
namespace WScore\DiContainer;

interface ContainerInterface
{
    public function set( $id, $value, $option=null );
    public function setOption( $id, $option );
    public function has( $id );
    public function get( $id, $option=array() );
}
