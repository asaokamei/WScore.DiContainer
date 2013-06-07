<?php
namespace WScore\DiContainer;

use WScore\DiContainer\Forge\Option;
use WScore\DiContainer\Utils;

class Container implements ContainerInterface
{
    /** 
     * @var \WScore\DiContainer\Forge\Forger 
     */
    private $forger;

    /**
     * @var \WScore\DiContainer\Storage\IdOrNamespace
     */
    private $values = null;

    /**
     * @var \WScore\DiContainer\Storage\IdOnly
     */
    private $singletons;
    
    /** 
     * namespace for object construction. 
     * 
     * @var null|string 
     */
    public $namespace = null;
    
    public $lastId = null;

    /**
     * @param \WScore\DiContainer\Storage\IdOrNamespace $values
     * @param \WScore\DiContainer\Forge\Forger $forger
     * @param \WScore\DiContainer\Storage\IdOnly  $singles
     */
    public function __construct( $values, $forger, $singles=null )
    {
        $this->values = $values;
        $this->forger = $forger;
        if( $singles ) {
            $this->singletons = $singles;
        } else {
            $this->singletons = new Storage\IdOnly();
        }
    }

    /**
     * Sets a service value for the $id.
     *
     * @param string     $id
     * @param mixed      $value
     * @return $this
     */
    public function set( $id, $value=null ) 
    {
        $id = Utils::normalizeClassName( $id );
        if( is_null( $value ) ) {
            if( Utils::isClassName( $id ) ) {
                $value = new Option( $id );
            }
        } else {
            if( Utils::isClassName( $value ) ) {
                $value = new Option( $value );
            }
        }
        $this->values->store( $id, $value, $this->namespace );
        $this->lastId = $id;
        return $this;
    }

    /**
     * sets a service value as singleton for $id. 
     * 
     * @return $this
     */
    public function singleton() 
    {
        if( $this->lastId ) {
            $option = $this->getValue( $this->lastId );
            if( is_object( $option ) && $option instanceof Option ) {
                $option->setSingleton();
            }
        }
        return $this;
    }

    /**
     * @param $id
     * @return mixed|null|Option
     */
    public function getValue( $id )
    {
        $id = Utils::normalizeClassName( $id );
        if( $option = $this->values->fetch( $id, $this->namespace ) ) {
            return $option;
        }
        if( Utils::isClassName( $id ) ) {
            return new Option( $id );
        }
        return null;
    }
    
    /**
     * Checks if a value is set for the $id.
     *
     * @param string $id
     * @return bool
     */
    public function has( $id ) 
    {
        $id = Utils::normalizeClassName( $id );
        return $this->values->fetch( $id, $this->namespace ) ? true: false;
    }

    /**
     * Gets a service for a given $id.
     * Forges an object if the set value or the $id is a class name.
     *
     * @param string $id
     * @return mixed|void
     */
    public function get( $id )
    {
        $id = Utils::normalizeClassName( $id );
        if( $found = $this->singletons->fetch( $id ) ) {
            return $found;
        }
        $found  = null;
        $found  = $this->getValue( $id );
        // check if $found is a closure, or a className to construct.
        if( is_callable( $found ) ) {
            $found = $found( $this );
        }
        elseif( is_object( $found ) && $found instanceof Option ) {
            $found = $this->forge( $id, $found );
        }
        return $found;
    }

    /**
     * @param string $id
     * @param Option $option
     * @return mixed|void
     */
    private function forge( $id, $option )
    {
        // it's a class. prepare options to construct an object.
        $found  = $this->forger->forge( $this, $option );
        // singleton: set singleton option to true.
        if( $option->getScope() === 'singleton' ) {
            $this->singletons->store( $id, $found );
        }
        return $found;
    }

    /**
     * @return null|string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @param null|string $namespace
     */
    public function setNamespace( $namespace=null ) {
        $this->namespace = $namespace;
    }
}