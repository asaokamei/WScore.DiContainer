<?php
namespace WScore\DiContainer\Forge;

class Option
{
    /**
     * @var string
     */
    protected $className;
    
    /**
     * @var null|string
     */
    protected $namespace = null;
    
    /**
     * @var bool
     */
    protected $cacheAble = null;
    
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
     * @param null|string $className
     */
    public function __construct( $className=null ) {
        $this->setClass( $className );
    }

    /**
     * @param $className
     * @return static
     */
    public static function forgeOption( $className ) {
        return new static( $className );
    }

    /**
     * @param string $className
     */
    public function setClass( $className ) {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClass() {
        return $this->className;
    }
    // +----------------------------------------------------------------------+
    //  scopes and caches
    // +----------------------------------------------------------------------+
    /**
     * @param string $scope
     */
    public function setScope( $scope )
    {
        $scope = strtolower( $scope );
        if(     $scope === 'singleton' ) $this->setSingleton();
        elseif( $scope === 'cacheable' ) $this->setCacheAble();
        else $this->scope = $scope; 
    }

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

    public function setNameSpace( $namespace ) {
        $this->namespace = $namespace;
    }

    /**
     * @return null|string
     */
    public function getNameSpace() {
        return $this->namespace;
    }
    // +----------------------------------------------------------------------+
    //  constructor injection
    // +----------------------------------------------------------------------+
    /**
     * @param string       $name
     * @param string       $id
     * @param null|string  $default
     * @return $this
     */
    public function setConstructor( $name, $id, $default=null ) 
    {
        $this->construct[ $name ] = $this->packMethodInfo( $name, $id, $default );
        return $this;
    }

    /**
     * @return array
     */
    public function getConstructor() {
        return $this->construct;
    }
    
    // +----------------------------------------------------------------------+
    //  setter injection
    // +----------------------------------------------------------------------+
    /**
     * @param string       $methodName
     * @param string       $name
     * @param string       $id
     * @param null|string  $default
     * @return $this
     */
    public function setSetter( $methodName, $name, $id, $default=null ) 
    {
        $info = $this->packMethodInfo( $name, $id, $default );
        if( !isset( $this->setter[ $methodName ] ) ) {
            $this->setter[ $methodName ] = array();
        }
        $this->setter[ $methodName ][ $name ] = $info;
        return $this;
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
     * @return $this
     */
    public function setProperty( $propertyName, $id ) {
        $this->property[ $propertyName ] = $id;
        return $this;
    }

    /**
     * @param $name
     * @return array|null
     */
    public function getProperty( $name=null ) {
        if( isset( $name ) ) {
            return array_key_exists( $name, $this->property ) ? $this->property[ $name ] : null;
        }
        return $this->property;
    }

    // +----------------------------------------------------------------------+
    //  utilities
    // +----------------------------------------------------------------------+
    /**
     * @param Option $option
     * @throws \RuntimeException
     */
    public function merge( $option )
    {
        if( !$option ) return;
        // check class name are the same. 
        if( $this->className !== $option->getClass() ) {
            $message = sprintf( 'class not match: %s and %s', $this->className, $option->getClass() );
            throw new \RuntimeException( $message );
        }
        // overwrite namespace
        if( $option->getNameSpace() ) {
            $this->setNameSpace( $option->getNameSpace() );
        }
        // overwrite scope
        if( $option->getScope() ) {
            $this->setScope(     $option->getScope() );
        }
        // overwrite cache-able 
        if( $option->getCacheAble() !== null ) {
            $this->setCacheAble( $option->getCacheAble() );
        }
        // constructor
        if( $construct = $option->getConstructor() ) {
            foreach( $construct as $arg ) {
                $this->setConstructor( $arg['name'], $arg['id'], $arg['default'] );
            }
        }
        if( $properties = $option->getProperty() ) {
            foreach( $properties as $name => $id ) {
                $this->setProperty( $name, $id );
            }
        }
        if( $setters = $option->getSetter() ) {
            foreach( $setters as $name => $info ) {
                foreach( $info as $arg ) {
                    $this->setSetter( $name, $arg['name'], $arg['id'], $arg['default'] );
                }
            }
        }
    }
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
