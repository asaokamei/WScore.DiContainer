<?php
namespace WScore\DiContainer;

/*
order of injection
------------------
1. property,
2. setter (method),
3. constructor

public methods and properties
-----------------------------

The Forger can inject private or protected methods and properties
with @Inject annotation.

For methods and properties without @Inject annotation,
they have to be public to inject value.

structure of $injectList
------------------------

      $injectList = array(
        'construct' => [ 'name' => method name, 'id' => id to inject, 'default' => default value, ],
        'setter' => [
          'methodName' => [ 'name' => method name, 'id' => id to inject, 'default' => default value, ], [], ...,
          'methodName2'=> [ ... ],
        ],
        'property' => [
          '$propertyName' => '$id', ...
        ],
      )


 */
class Forger
{
    /** 
     * @Inject
     * @var \WScore\DiContainer\Analyzer
     */
    private $analyzer;

    /** @var null|\WScore\DiContainer\Cache_Interface */
    private $cache = null;
    
    /** @var bool */
    public $singleton = false;
    
    /**
     * @param \WScore\DiContainer\Analyzer $analyzer
     * @param \WScore\DiContainer\Cache_Interface   $cache
     */
    public function __construct( $analyzer=null, $cache=null )
    {
        if( isset( $analyzer ) ) $this->analyzer = $analyzer;
        if( isset( $cache    ) ) $this->cache = $cache;
    }

    /**
     * @param string $className
     * @return string
     */
    private function normalize( $className ) {
        return 'DimForger-' . str_replace( '\\', '-', $className );
    }
    
    /**
     * @param string $className
     * @return bool|mixed
     */
    private function fetch( $className ) {
        if( isset( $this->cache ) ) {
            return $this->cache->fetch( $this->normalize( $className ) );
        }
        return false;
    }

    /**
     * @param string $className
     * @param mixed  $object
     */
    private function store( $className, $object ) {
        if( isset( $this->cache ) ) $this->cache->store( $this->normalize( $className ), $object );
    }
    
    /**
     * constructs an object of $className.
     *
     * @param \WScore\DiContainer\ContainerInterface $container
     * @param string $className
     * @param array  $option
     * @return mixed|void
     */
    public function forge( $container, $className, $option=array() )
    {
        if( ( $object = $this->fetch( $className ) ) !== false ) return $object;

        $injectList = $this->analyze( $className );
        if( $option ) $injectList = Utils::mergeOption( $injectList, $option );
        
        // set namespace if set. 
        $namespaceOriginal = $container->getNamespace();
        $namespace         = null;
        if( isset( $injectList[ 'namespace' ] ) ) {
            $container->setNamespace( $injectList[ 'namespace' ] );
        }
        
        // get reflection of class, and a new instance. 
        $refClass = new \ReflectionClass( $className );
        $object = Utils::newInstanceWithoutConstructor( $refClass );
        
        // property injection
        if( !empty( $injectList[ 'property' ] ) ) {
            foreach( $injectList[ 'property' ] as $propName => $id ) {
                $this->injectProperty( $container, $object, $propName, $id );
            }
        }
        
        // setter injection
        if( !empty( $injectList[ 'setter' ] ) ) {
            foreach( $injectList[ 'setter' ] as $name => $list ) {
                $this->injectMethod( $container, $object, $name, $list );
            }
        }
        
        // construct object
        $this->injectConstruct( $container, $object, $refClass, $injectList[ 'construct' ] );
        
        // cache an object, if cacheable annotation is set. 
        if( isset( $injectList[ 'cacheable'] ) && $injectList[ 'cacheable' ] ) {
            $this->store( $className, $object );
        }
        if( isset( $injectList[ 'singleton'] ) && $injectList[ 'singleton' ] ) {
            $this->singleton = true;
        } else {
            $this->singleton = false;
        }
        // set namespace to original value. 
        $container->setNamespace( $namespaceOriginal );
        return $object;
    }

    /**
     * injects a $id object from $container into $object's property ($refProp).
     * if $refProp is a property name, it can only inject to public property.
     *
     * @param \WScore\DiContainer\ContainerInterface $container
     * @param object $object
     * @param string $propName
     * @param string $id
     */
    private function injectProperty( $container, $object, $propName, $id )
    {
        $object->$propName = $container->get( $id );
        return;
    }

    /**
     * injects a list of $id from $container into $object's method ($refMethod).
     * if $refMethod is a method name, it can only inject to public method.
     *
     * @param \WScore\DiContainer\ContainerInterface $container
     * @param object $object
     * @param string $methodName
     * @param array $list
     */
    private function injectMethod( $container, $object, $methodName, $list )
    {
        $args = array();
        if( !empty( $list ) )
        foreach( $list as $idx => $info ) {
            
            // $idx as number is for injection. 
            if( !is_numeric( $idx ) ) continue;
            $name = $info[ 'name' ];
            // overwrite $id if set in $list. 
            $id = isset( $list[ $name ] ) ? $list[ $name ] : $info[ 'id' ];
            $val = $container->get( $id );
            $args[] = is_null( $val ) ? $info[ 'default' ] : $val ;
        }
        call_user_func_array( array( $object, $methodName ), $args );
        return;
    }

    /**
     * @param ContainerInterface $container
     * @param mixed $object
     * @param \ReflectionClass $refClass
     * @param array $list
     */
    private function injectConstruct( $container, $object, $refClass, $list )
    {
        $refConstruct = $refClass->getConstructor();
        if( !$refConstruct ) return; // no constructor. 
        $methodName = $refConstruct->getName();
        $this->injectMethod( $container, $object, $methodName, $list );
    }
    /**
     * @param string $className
     * @return mixed
     */
    public function analyze( $className ) 
    {
        return $this->analyzer->analyze( $className );
    }
}
