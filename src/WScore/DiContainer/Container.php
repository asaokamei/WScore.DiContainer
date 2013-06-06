<?php
namespace WScore\DiContainer;

class Container implements ContainerInterface
{
    /** 
     * @var \WScore\DiContainer\Forge\Forger 
     */
    private $forger;

    /**
     * @var \WScore\DiContainer\Values
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

    /**
     * @param \WScore\DiContainer\Values $values
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
     * @param array|null $option
     * @return $this
     */
    public function set( $id, $value, $option=array() ) 
    {
        $id = Utils::normalizeClassName( $id );
        $this->values->set( $id, $value, $option, $this->namespace );
        return $this;
    }

    /**
     * sets a service value as singleton for $id. 
     * 
     * @param string $id
     * @param mixed  $value
     * @param array  $option
     * @return $this
     */
    public function singleton( $id, $value, $option=array() ) 
    {
        $option[ 'singleton' ] = true;
        $this->set( $id, $value, $option );
        return $this;
    }
    
    /**
     * Sets an option for forging an object for the $id service.
     *
     * @param string $id
     * @param array  $option
     * @param bool   $reset
     * @return $this
     */
    public function setOption( $id, $option, $reset=false )
    {
        $id = Utils::normalizeClassName( $id );
        $this->values->setOption( $id, $option, $reset, $this->namespace );
        return $this;
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
        return $this->values->get( $id, $this->namespace ) ? true: false;
    }

    /**
     * Gets a service for a given $id.
     * Forges an object if the set value or the $id is a class name.
     *
     * @param string $id
     * @param array  $option
     * @return mixed|void
     */
    public function get( $id, $option = array() )
    {
        $id = Utils::normalizeClassName( $id );
        if( $found = $this->singletons->fetch( $id ) ) {
            return $found;
        }
        $found  = null;
        $found  = $this->values->get( $id, $this->namespace );
        $option = Utils::normalizeOption( $option );
        if( $found ) {
            list( $found, $config ) = $found;
            $option = Utils::mergeOption( $config, $option );
        }
        // check if $found is a closure, or a className to construct.
        if( !$found ) {
            if( Utils::isClassName( $id ) ) {
                $found = $this->forge( $id, $id, $option );
            }
        }
        elseif( is_callable( $found ) ) {
            $found = $found( $this );
        }
        elseif( Utils::isClassName( $found ) ) {
            $found = $this->forge( $id, $found, $option );
        }
        return $found;
    }

    /**
     * @param string $id
     * @param string $className
     * @param array  $option
     * @return mixed|void
     */
    private function forge( $id, $className, $option )
    {
        // it's a class. prepare options to construct an object.
        $className  = Utils::normalizeClassName( $className );
        $found  = $this->forger->forge( $this, $className, $option );
        // singleton: set singleton option to true.
        if( $this->forger->singleton ) {
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