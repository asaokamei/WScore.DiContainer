<?php
namespace WScore\DiContainer;

class Container implements ContainerInterface
{
    /** 
     * @var \WScore\DiContainer\Forger 
     */
    private $forger;

    /**
     * definition of an id. 
     * @var array
     */
    private $value = array();

    /**
     * option for forging an object. 
     * @var array
     */
    private $option = array();

    /**
     * @param \WScore\DiContainer\Forger $forger
     */
    public function __construct( $forger )
    {
        $this->forger = $forger;
    }

    /**
     * Sets a service value for the $id.
     *
     * @param string     $id
     * @param mixed      $value
     * @param array|null $option
     * @return void
     */
    public function set( $id, $value, $option=array() ) 
    {
        $id = Utils::normalizeClassName( $id );
        $this->value[ $id ] = $value;
        if( isset( $option ) ) $this->setOption( $id, $option );
    }

    /**
     * sets a service value as singleton for $id. 
     * 
     * @param       $id
     * @param       $value
     * @param array $option
     */
    public function singleton( $id, $value, $option=array() ) 
    {
        $option[ 'singleton' ] = true;
        $this->set( $id, $value, $option );
    }
    
    /**
     * Sets an option for forging an object for the $id service.
     *
     * @param string $id
     * @param array  $option
     * @param bool   $reset
     * @return void
     */
    public function setOption( $id, $option, $reset=true ) 
    {
        $id = Utils::normalizeClassName( $id );
        $option = Utils::normalizeInjection( $option );
        if( !$reset && isset( $this->option[ $id ] ) ) {
            $option = array_merge( $this->option[ $id ], $option );
        }
        $this->option[ $id ] = $option;
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
        return array_key_exists( $id, $this->value );
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
        $found  = null;
        $check  = $id;
        $option = $this->prepareOption( $id, $option );
        if( array_key_exists( $id, $this->value ) ) {
            $found = $check = $this->value[ $id ];  // set found value.
        }
        // check if $found is a closure, or a className to construct.
        if( $found && is_callable( $found ) ) {
            $found = $found( $this );
        }
        elseif( $found && is_object( $found ) ) {
            // return the found object. 
        }
        elseif( Utils::isClassName( $check ) ) {

            // it's a class. prepare options to construct an object.
            $check  = Utils::normalizeClassName( $check );
            $found  = $this->forger->forge( $this, $check, $option );
            // singleton: set singleton option to true.
            if( $this->forger->singleton ) {
                $this->value[ $id ] = $found;
            }
        }
        return $found;
    }

    /**
     * @param string $id
     * @param array  $option
     * @return array
     */
    private function prepareOption( $id, $option )
    {
        $option = Utils::normalizeOption( $option ); // normalize input option
        if( isset( $this->option[$id] ) ) {
            // get pre-set option from $option, and merge it with the given option.
            $option = Utils::mergeOption( $this->option[$id], $option );
        }
        return $option;
    }
}