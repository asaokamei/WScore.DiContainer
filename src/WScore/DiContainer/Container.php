<?php
namespace WScore\DiContainer;

class Container implements ContainerInterface
{
    /** @var \WScore\DiContainer\Forger */
    private $forger;
    
    private $value = array();
    
    private $option = array();

    /**
     * @param \WScore\DiContainer\Forger $forger
     */
    public function __construct( $forger )
    {
        $this->forger = $forger;
    }

    /**
     * @param string $id
     * @param mixed  $value
     * @param null $option
     */
    public function set( $id, $value, $option = null ) {
        $this->value[ $id ] = $value;
        if( isset( $option ) ) $this->setOption( $id, $option );
    }

    /**
     * @param string $id
     * @param array  $option
     */
    public function setOption( $id, $option ) {
        $this->option[ $id ] = Utils::normalizeInjection( $option );
    }

    /**
     * @param string $id
     * @return bool
     */
    public function exists( $id ) {
        return array_key_exists( $id, $this->value );
    }

    /**
     * @param string $id
     * @param array  $option
     * @return mixed|void
     */
    public function get( $id, $option = array() )
    {
        $found = $id;
        if( array_key_exists( $id, $this->value ) ) {
            $found = $this->value[ $id ];  // set found value.
        }
        // check if $found is a closure, or a className to construct.
        if( $found instanceof \Closure ) {
            $found = $found( $this );
        }
        elseif( Utils::isClassName( $found ) ) {
            // it's a class. prepare options to construct an object.
            $option = $this->prepareOption( $id, $found, $option );
            $found  = $this->forger->forge( $this, $found, $option );
            // singleton: store found object into cached.
            if( array_key_exists( 'singleton', $option ) && $option[ 'singleton' ] ) {
                $this->value[ $id ] = $found;
            }
        }
        return $found;
    }

    /**
     * @param string $id
     * @param string $className
     * @param array  $option
     * @return array
     */
    private function prepareOption( $id, $className, $option )
    {
        $option = Utils::normalizeOption( $option ); // normalize input option
        if( isset( $this->option[$id] ) ) {
            // get pre-set option from $option, and merge it with the given option.
            $option = Utils::mergeOption( $this->option[$id], $option );
        }
        $inject = $this->forger->analyze( $className );
        $option = Utils::mergeOption( $inject, $option );
        return $option;
    }
}