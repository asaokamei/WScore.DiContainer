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
        // constructor injection
        if( !empty( $injectList[ 'construct' ] ) ) {
            /** @var $refConst \ReflectionMethod */
            $refConst = $injectList[ 'reflections' ][ 'construct' ];
            $refArgs  = $refConst->getParameters();
            $args     = array();
            if( !empty( $refArgs ) ) {
                foreach( $refArgs as $refArg ) {
                    $name  = $refArg->getName();
                    $id    = $injectList[ 'construct' ][ $name ];
                    $value = $container->get( $id );
                    $args[] = $value;
                }
                $refConst->invokeArgs( $object, $args );
            }
        }
        return $object;
    }
    
    private function getAnalysis( $className ) 
    {
        return $this->analyzer->analyze( $className );
    }
}
