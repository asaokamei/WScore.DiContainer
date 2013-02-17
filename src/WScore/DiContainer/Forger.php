<?php
namespace WScore\DiContainer;

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
    public function __construct( $analyzer )
    {
        $this->analyzer = $analyzer;
    }

    /**
     * @param \WScore\DiContainer\ContainerInterface $container
     * @param       $className
     * @param array $option
     * @return mixed|void
     */
    public function forge( $container, $className, $option=array() )
    {
        $injectList = $this->getAnalysis( $className );
        $injectList = Utils::mergeOption( $injectList, $option );
        $object = Utils::newInstanceWithoutConstructor( $injectList[ 'reflections' ][ 'class' ] );
        
        // property injection
        if( !empty( $injectList[ 'property' ] ) ) {
            foreach( $injectList[ 'property' ] as $propName => $id ) {
                /** @var $refProp \ReflectionProperty */
                $refProp = $injectList[ 'reflections' ][ 'property' ][ $propName ];
                $refProp->setAccessible( true );
                $value = $container->get( $id );
                $refProp->setValue( $object, $value );
            }
        }
        
        // setter injection
        if( !empty( $injectList[ 'setter' ] ) ) {
            foreach( $injectList[ 'setter' ] as $name => $list ) {
                $refMethod = $injectList['reflections']['setter'][$name];
                $this->injectMethod( $container, $object, $refMethod, $list );
            }
        }
        
        // constructor injection
        if( !empty( $injectList[ 'construct' ] ) ) {
            /** @var $refMethod \ReflectionMethod */
            $refMethod = $injectList[ 'reflections' ][ 'construct' ];
            $this->injectMethod( $container, $object, $refMethod, $injectList[ 'construct' ] );
        }
        return $object;
    }

    /**
     * @param \WScore\DiContainer\ContainerInterface $container
     * @param object $object
     * @param \ReflectionMethod $refMethod
     * @param array $list
     */
    private function injectMethod( $container, $object, $refMethod, $list )
    {
        $refArgs  = $refMethod->getParameters();
        $args     = array();
        if( !empty( $refArgs ) ) {
            foreach( $refArgs as $refArg ) {
                $name  = $refArg->getName();
                $id    = $list[ $name ];
                $value = $container->get( $id );
                $args[] = $value;
            }
            $refMethod->invokeArgs( $object, $args );
        }
    }
    
    private function getAnalysis( $className ) 
    {
        return $this->analyzer->analyze( $className );
    }
}
