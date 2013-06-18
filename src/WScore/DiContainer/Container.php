<?php
namespace WScore\DiContainer;

use WScore\DiContainer\Forge\Option;
use WScore\DiContainer\Storage\StorageInterface;
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
     * @var StorageInterface[]
     */
    private $scopes = array();
    
    /** 
     * namespace for object construction. 
     * 
     * @var null|string 
     */
    public $namespace = null;

    /**
     * keeps last used id as [ 0 => $id, 1 => $namespace ].
     * 
     * @var array
     */
    public $lastId = array();

    // +----------------------------------------------------------------------+
    //  construction and managing its state (scope and namespace)
    // +----------------------------------------------------------------------+
    /**
     * @param \WScore\DiContainer\Storage\IdOrNamespace $values
     * @param \WScore\DiContainer\Forge\Forger $forger
     * @param \WScore\DiContainer\Storage\IdOnly  $singles
     * @param \WScore\DiContainer\Storage\IdWithNamespace  $shared
     */
    public function __construct( $values, $forger, $singles=null, $shared=null )
    {
        $this->values = $values;
        $this->forger = $forger;
        if( !$singles ) {
            $singles = new Storage\IdOnly();
        }
        if( !$shared ) {
            $shared = new Storage\IdWithNamespace();
        }
        $this->setScope( 'singleton', $singles );
        $this->setScope( 'shared',    $shared );
    }

    /**
     * @param string           $scope
     * @param StorageInterface $storage
     */
    public function setScope( $scope, $storage ) {
        $this->scopes[ $scope ] = $storage;
    }

    /**
     * @param string $scope
     */
    public function clearScope( $scope ) {
        if( isset( $this->scopes[ $scope ] ) ) {
            $this->scopes[ $scope ]->clear();
        }
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

    // +----------------------------------------------------------------------+
    //  managing values
    // +----------------------------------------------------------------------+
    /**
     * Sets a service value for the $id.
     *
     * @param string     $id
     * @param mixed      $value
     * @return $this
     */
    public function set( $id, $value=null ) 
    {
        $numArgs = func_num_args();
        $id = Utils::normalizeClassName( $id );
        if( $numArgs === 1 ) {
            if( Utils::isClassName( $id ) ) {
                $value = new Option( $id );
            }
        } else {
            if( Utils::isClassName( $value ) ) {
                $value = new Option( $value );
            }
        }
        $this->values->store( $id, $value, $this->namespace );
        return $this->id( $id );
    }

    /**
     * @param $id
     * @return $this
     */
    public function id( $id ) {
        $this->lastId = array( $id, $this->namespace );
        return $this;
    }

    /**
     * @return bool|Option
     */
    private function getLastOption() 
    {
        if( $this->lastId ) {
            $option = $this->getValue( $this->lastId[0], $this->lastId[1] );
            if( is_object( $option ) && $option instanceof Option ) {
                return $option;
            }
        }
        return false;
    }
    
    /**
     * sets a service value as singleton for $id. 
     * 
     * @return $this
     */
    public function singleton() {
        return $this->scope( 'singleton' );
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function scope( $scope ) {
        if( $option = $this->getLastOption() ) {
            $option->setScope( $scope );
        }
        return $this;
    }

    /**
     * reset an existing definition of value to another namespace.
     * 
     * @param string $namespace
     * @return $this
     */
    public function resetNamespace( $namespace ) {
        if( $this->values->exists( $this->lastId[0], $this->namespace ) ) {
            $this->values->resetNamespace( $this->lastId[0], $this->lastId[1], $namespace );
            $this->lastId[1] = $namespace;
        }
        return $this;
    }

    /**
     * put object in namespace for class definition, or reset namespace for values. 
     * 
     * @param $namespace
     * @return $this
     */
    public function inNamespace( $namespace ) {
        if( $option = $this->getLastOption() ) {
            $option->setNameSpace( $namespace );
        } else {
            $this->resetNamespace( $namespace );
        }
        return $this;
    }
    
    /**
     * @param string       $id
     * @param null|string  $namespace
     * @return mixed|null|Option
     */
    public function getValue( $id, $namespace=null )
    {
        if( !$namespace ) $namespace = $this->namespace;
        $id = Utils::normalizeClassName( $id );
        if( $this->values->exists( $id, $namespace ) ) {
            return $this->values->fetch( $id, $namespace );
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

    // +----------------------------------------------------------------------+
    //  get/forge values
    // +----------------------------------------------------------------------+
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
        $found = $this->getFromScope( $id );
        if( $found !== null ) {
            return $found;
        }
        $found  = $this->getValue( $id );
        // check if $found is a closure, or a className to construct.
        if( is_callable( $found ) ) {
            $found = $found( $this );
        }
        elseif( is_object( $found ) && $found instanceof Option ) {
            // it's a class. prepare options to construct an object.
            $object  = $this->forger->forge( $this, $found );
            $this->setToScope( $id, $object, $found->getScope(), $found->getNameSpace() );
            return $object;
        }
        return $found;
    }

    /**
     * @param string $id
     * @return mixed|null
     */
    private function getFromScope( $id )
    {
        foreach( $this->scopes as $scope ) {
            /** @var $scope StorageInterface */
            if( $scope->exists( $id, $this->namespace ) ) {
                return $scope->fetch( $id, $this->namespace );
            }
        }
        return null;
    }

    /**
     * @param string $id
     * @param mixed  $value
     * @param string $scope
     * @param string $namespace
     */
    private function setToScope( $id, $value, $scope, $namespace )
    {
        if( !$namespace ) $namespace = $this->namespace;
        if( isset( $this->scopes[ $scope ] ) ) {
            $this->scopes[ $scope ]->store( $id, $value, $namespace );
        }
    }
    // +----------------------------------------------------------------------+
}