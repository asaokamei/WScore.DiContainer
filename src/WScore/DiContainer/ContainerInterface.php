<?php
namespace WScore\DiContainer;

interface ContainerInterface
{
    public function set( $id, $value=null );
    public function setOption( $id, $option );
    public function has( $id );
    public function get( $id );
    public function getNamespace();
    public function setNamespace( $namespace=null );
}
