<?php
namespace WScore\DiContainer\Storage;

class Option
{
    /**
     * @var null|string
     */
    protected $namespace = null;
    
    /**
     * @var bool
     */
    protected $cacheAble = false;
    
    /**
     * @var null|string
     */
    protected $scope = null;
    
    /**
     * @var array
     */
    protected $construct = array();
    
    /**
     * @var array
     */
    protected $property = array();
    
    /**
     * @var array
     */
    protected $setter = array();
    
    // +----------------------------------------------------------------------+
    //  scopes and caches
    // +----------------------------------------------------------------------+
    /**
     * @param bool $isCacheAble
     */
    public function setCacheAble( $isCacheAble=true ) {
        $this->cacheAble = $isCacheAble;
    }

    /**
     * @return bool
     */
    public function getCacheAble() {
        return $this->cacheAble;
    }

    /**
     */
    public function setSingleton() {
        $this->scope = 'singleton';
    }

    /**
     */
    public function setPrototype() {
        $this->scope = 'prototype';
    }

    /**
     */
    public function setShared() {
        $this->scope = 'shared';
    }

    /**
     * @return string
     */
    public function getScope() {
        return $this->scope;
    }

    // +----------------------------------------------------------------------+
    //  constructor injection
    // +----------------------------------------------------------------------+
    /**
     * @param string       $name
     * @param string       $id
     * @param null|string  $default
     */
    public function setConstructor( $name, $id, $default=null ) 
    {
        $this->construct = $this->packMethodInfo( $name, $id, $default );
    }
    
    // +----------------------------------------------------------------------+
    //  setter injection
    // +----------------------------------------------------------------------+
    /**
     * @param string       $methodName
     * @param string       $name
     * @param string       $id
     * @param null|string  $default
     */
    public function setSetter( $methodName, $name, $id, $default=null ) 
    {
        $info = $this->packMethodInfo( $name, $id, $default );
        if( !isset( $this->setter[ $methodName ] ) ) {
            $this->setter[ $methodName ] = array();
        }
        $this->setter[ $methodName ][] = $info;
    }

    /**
     * @return array
     */
    public function getMethods() {
        return array_keys( $this->setter );
    }
    
    /**
     * @param null|string $name
     * @return array
     */
    public function getSetter( $name=null ) 
    {
        if( isset( $name ) ) {
            return array_key_exists( $name, $this->setter ) ? $this->setter[ $name ] : null;
        } 
        return $this->setter;
    }

    // +----------------------------------------------------------------------+
    //  property injection
    // +----------------------------------------------------------------------+
    /**
     * @param string $propertyName
     * @param string $id
     */
    public function setProperty( $propertyName, $id ) {
        $this->setter[ $propertyName ] = $id;
    }

    // +----------------------------------------------------------------------+
    //  utilities
    // +----------------------------------------------------------------------+
    /**
     * normalize dependency option.
     * option can be set for construct, property, or setter.
     *
     * @param $option
     * @return array
     */
    public function normalizeOption( $option )
    {
        $normalized = array();
        if( empty( $option ) ) return $normalized;
        if( !is_array( $option ) ) $option = array( $option );
        if( in_array( 'singleton', $option ) ) {
            $normalized[ 'singleton' ] = true;
        }
        if( !isset( $option[ 'construct' ] ) && !isset( $option[ 'property' ] ) && !isset( $option[ 'setter' ] ) ) {
            $normalized[ 'construct' ] = $this->normalizeInjection( $option );
        }
        if( isset( $option[ 'construct' ] ) ) {
            $normalized[ 'construct' ] = $this->normalizeInjection( $option[ 'construct' ] );
        }
        if( isset( $option[ 'property' ] ) ) {
            $normalized[ 'property' ] = $this->normalizeInjection( $option[ 'property' ] );
        }
        if( isset( $option[ 'setter' ] ) ) {
            $normalized[ 'setter' ] = $this->normalizeInjection( $option[ 'setter' ] );
        }
        return $normalized;
    }

    /**
     * normalize dependency information.
     *
     * @param $option
     * @return array
     */
    private function normalizeInjection( $option )
    {
        if( empty( $option ) ) return $option;
        if( !is_array( $option ) ) $option = array( $option );
        // check injection info for each key... 
        return $option;
    }
    
    /**
     * @param string       $name
     * @param string       $id
     * @param null|string  $default
     * @return array
     */
    private function packMethodInfo( $name, $id, $default=null )
    {
        return array(
            'name'    => $name,
            'id'      => $id,
            'default' => $default,
        );
    }

}
