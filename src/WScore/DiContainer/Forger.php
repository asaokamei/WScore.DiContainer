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
        'construct' => [
          '$varName' => '$id, ...
        ],
        'setter' => [
          '$methodName' => [ '$varName' => '$id', ... ], [], ...
        ],
        'property' => [
          '$propertyName' => '$id', ...
        ],
        'reflections' => [
          'class' => $ReflectionClassObject,
          'construct' => $ReflectionConstructorObject,
          'setter' => [ '$methodName' => $ReflectionMethodObject, ... ],
        ]
      )


 */
class Forger
{
    /** 
     * @Inject
     * @var \WScore\DiContainer\Analyzer
     */
    private $analyzer;

    /**
     * @param \WScore\DiContainer\Analyzer $analyzer
     */
    public function __construct( $analyzer=null )
    {
        if( isset( $analyzer ) ) $this->analyzer = $analyzer;
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
        $injectList = $this->analyze( $className );
        if( $option ) $injectList = Utils::mergeOption( $injectList, $option );
        $object = Utils::newInstanceWithoutConstructor( $injectList[ 'reflections' ][ 'class' ] );
        
        // property injection
        if( !empty( $injectList[ 'property' ] ) ) {
            foreach( $injectList[ 'property' ] as $propName => $id ) {
                /** @var $refProp \ReflectionProperty */
                $refProp = isset( $injectList[ 'reflections' ][ 'property' ][ $propName ] ) ?
                    $injectList[ 'reflections' ][ 'property' ][ $propName ] : $propName;
                $this->injectProperty( $container, $object, $refProp, $id );
            }
        }
        
        // setter injection
        if( !empty( $injectList[ 'setter' ] ) ) {
            foreach( $injectList[ 'setter' ] as $name => $list ) {
                $refMethod = isset( $injectList['reflections']['setter'][$name] ) ?
                    $injectList['reflections']['setter'][$name]: $name;
                $this->injectMethod( $container, $object, $refMethod, $list );
            }
        }
        
        // constructor injection
        /** @var $refMethod \ReflectionMethod */
        if( $refMethod = $injectList[ 'reflections' ][ 'construct' ] ) {
            $this->injectMethod( $container, $object, $refMethod, $injectList[ 'construct' ] );
        }
        return $object;
    }

    /**
     * injects a $id object from $container into $object's property ($refProp).
     * if $refProp is a property name, it can only inject to public property.
     *
     * @param \WScore\DiContainer\ContainerInterface $container
     * @param object $object
     * @param \ReflectionProperty $refProp
     * @param string $id
     */
    private function injectProperty( $container, $object, $refProp, $id )
    {
        if( is_string( $refProp ) ) {
            $object->$refProp = $container->get( $id );
            return;
        }
        $refProp->setAccessible( true );
        $value = $container->get( $id );
        $refProp->setValue( $object, $value );
    }

    /**
     * injects a list of $id from $container into $object's method ($refMethod).
     * if $refMethod is a method name, it can only inject to public method.
     *
     * @param \WScore\DiContainer\ContainerInterface $container
     * @param object $object
     * @param \ReflectionMethod $refMethod
     * @param array $list
     */
    private function injectMethod( $container, $object, $refMethod, $list )
    {
        if( is_string( $refMethod ) ) {
            $args = array();
            if( !empty( $list ) ) {
                foreach( $list as $id ) {
                    $args[] = $container->get( $id );
                }
            }
            call_user_func_array( array( $object, $refMethod ), $args );
            return;
        }
        $refArgs  = $refMethod->getParameters();
        $args     = array();
        if( !empty( $refArgs ) ) {
            foreach( $refArgs as $refArg ) {
                $name  = $refArg->getName();
                if( isset( $list[ $name ] ) ) {
                    $value = $container->get( $list[ $name ] );
                }
                elseif( $refArg->isDefaultValueAvailable() ) {
                    $value = $refArg->getDefaultValue();
                }
                else {
                    $value = null;
                }
                $args[] = $value;
            }
        }
        $refMethod->invokeArgs( $object, $args );
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
