<?php
namespace WScore\DiContainer;

interface ContainerInterface
{
    public function set( $id, $value=null );
    public function has( $id );
    public function get( $id );
    public function load( $id );
    public function getNamespace();
    public function setNamespace( $namespace=null );
}
