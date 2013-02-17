<?php
namespace WScore\DiContainer;

class Container implements ContainerInterface
{
    /** @var \WScore\DiContainer\Forger */
    private $forger;
    
    private $value = array();
    
    private $option = array();

    private $cached = array();

    /**
     * @param \WScore\DiContainer\Forger $forger
     */
    public function __construct( $forger )
    {
        $this->forger = $forger;
    }

    public function set( $id, $value, $option = null ) {
        $this->value[ $id ] = $id;
        if( isset( $option ) ) $this->setOption( $id, $option );
    }

    public function setOption( $id, $option ) {
        $this->option[ $id ] = Utils::normalizeInjection( $option );
    }

    public function exists( $id ) {
        return array_key_exists( $id, $this->value );
    }

    public function get( $id, $option = array() )
    {
        if( array_key_exists( $id, $this->cached ) ) return $this->cached[ $id ];
        $found = $id;
        if( array_key_exists( $id, $this->value ) ) $found = $id;
        // check if $found is a closure, or a className to construct.
        if( $found instanceof \Closure ) {
            $found = $found( $this );
        }
        elseif( Utils::isClassName( $found ) ) {
            // prepare options
            $option = Utils::normalizeOption( $option ); // normalize input option
            if( isset( $this->option[$id] ) ) { 
                $option = Utils::mergeOption( $this->option[$id], $option );
            }
            $inject = $this->forger->analyze( $found );
            $option = Utils::mergeOption( $inject, $option );
            $found  = $this->forger->forge( $this, $found, $option );
        }
        // singleton: store found object into cached. 
        if( array_key_exists( 'singleton', $option ) && $option[ 'singleton' ] ) { 
            $this->cached[ $id ] = $found; 
        }
        return $found;
        
    }
}