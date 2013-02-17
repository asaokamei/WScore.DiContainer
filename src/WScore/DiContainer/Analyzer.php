<?php
namespace WScore\DiContainer;

class Analyzer
{
    /** @var \WScore\DiContainer\Parser */
    protected $parser;

    /**
     * @param \WScore\DiContainer\Parser $parser
     */
    public function __construct( $parser )
    {
        $this->parser = $parser;
    }
    
    /**
     * list dependencies of a className.
     *
     * @param string $className
     * @return array
     */
    public function analyze( $className )
    {
        $refClass   = new \ReflectionClass( $className );
        list( $dimConst, $refConst ) = $this->constructor( $refClass );
        list( $dimProp,  $refProp  ) = $this->property( $refClass );
        list( $dimSet,   $refSet   ) = $this->setter( $refClass );
        $diList     = array(
            'singleton' => $this->singleton( $refClass ),
            'construct' => $dimConst,
            'setter'    => $dimSet,
            'property'  => $dimProp,
            'reflections' => array(
                'class'     => $refClass,
                'construct' => $refConst,
                'setter'    => $refSet,
                'property'  => $refProp,
            ),
        );
        return $diList;
    }

    /**
     * @param \ReflectionClass $refClass
     * @return bool
     */
    private function singleton( $refClass )
    {
        $comment = $refClass->getDocComment();
        $dimClass = $this->parser->parse( $comment );
        if( isset( $dimClass[ 'singleton' ] ) && $dimClass[ 'singleton' ] ) return true;
        return false;
    }
    /**
     * @param \ReflectionClass $refClass
     * @return array
     */
    private function constructor( $refClass )
    {
        $injectList = array();
        $refConst   = $refClass->getConstructor();
        if( $refConst ) {
            $comments   = $refConst->getDocComment();
            $injectList = $this->parser->parse( $comments );
        }
        return array( $injectList, $refConst );
    }

    /**
     * get dependency information of properties for a class.
     * searches all properties in parent classes as well.
     *
     * @param \ReflectionClass $refClass
     * @return array
     */
    private function property( $refClass )
    {
        $injectList = array();
        $refObjects = array();
        do {
            
            if( !$properties = $refClass->getProperties() ) continue;
            foreach( $properties as $refProp )
            {
                if( isset( $injectList[ $refProp->name ] ) ) continue;
                if( !$comments = $refProp->getDocComment() ) continue;
                if( !$info = $this->parser->parse( $comments ) ) continue;
                
                $injectList[ $refProp->name ] = $info[0]['id'];
                $refObjects[ $refProp->name ] = $refProp;
            }
        } while( false !== ( $refClass = $refClass->getParentClass() ) );
        return array( $injectList, $refObjects );
    }

    /**
     * get dependency information of properties for a class.
     * searches all properties in parent classes as well.
     *
     * @param \ReflectionClass $refClass
     * @return array
     */
    private function setter( $refClass )
    {
        $injectList = array();
        $refObjects = array();
        do {
            
            if( !$methods = $refClass->getMethods() ) continue;
            foreach( $methods as $refMethod )
            {
                if( $refMethod->isConstructor() ) continue;
                if( isset( $injectList[ $refMethod->name ] ) ) continue;
                if( !$comments = $refMethod->getDocComment() ) continue;
                if( !$info = $this->parser->parse( $comments ) ) continue;
                
                foreach( $info as $var => $id ) {
                    $injectList[$refMethod->name][ $var ] = $id;
                    $refObjects[$refMethod->name] = $refMethod;
                }
            }
        } while( false !== ( $refClass = $refClass->getParentClass() ) );
        return array( $injectList, $refObjects );
    }

}
