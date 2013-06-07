<?php
namespace WScore\DiContainer\Forge;

use \WScore\DiContainer\Cache_Interface;

class Analyzer
{
    /** @var \WScore\DiContainer\Forge\Parser */
    protected $parser;
    
    /** @var array|Cache_Interface  */
    protected $cache = array();

    /**
     * @param \WScore\DiContainer\Forge\Parser           $parser
     * @param Cache_Interface                      $cache
     */
    public function __construct( $parser, $cache=null )
    {
        $this->parser = $parser;
        if( $cache ) $this->cache = $cache;
    }

    /**
     * @param string $className
     * @return string
     */
    private function normalize( $className ) {
        return 'DimAnalyzer-' . str_replace( '\\', '-', $className );
    }

    /**
     * @param string $className
     * @return bool|mixed
     */
    private function fetch( $className ) {
        $name = $this->normalize( $className );
        if( $this->cache instanceof Cache_Interface ) {
            return $this->cache->fetch( $name );
        }
        if( array_key_exists( $name, $this->cache ) ) {
            return $this->cache[ $name ];
        }
        return false;
    }

    /**
     * @param string $className
     * @param mixed $diList
     */
    private function store( $className, $diList ) {
        $name = $this->normalize( $className );
        if( $this->cache instanceof Cache_Interface ) {
            $this->cache->store( $name, $diList );
            return;
        }
        $this->cache[ $name ] = $diList;
    }

    /**
     * list dependencies of a className.
     *
     * @param string $className
     * @return Option
     */
    public function analyze( $className )
    {
        if( false !== $option = $this->fetch( $className ) ) return $option;
        $option = new Option( $className );

        $refClass = new \ReflectionClass( $className );
        $this->constructor( $refClass, $option );
        $this->property(    $refClass, $option );
        $this->setter( $refClass, $option );
        $this->getClassAnnotation( $refClass, $option );
        $this->store( $className, $option );
        return $option;
    }

    /**
     * @param \ReflectionClass $refClass
     * @param Option           $option
     * @return array
     */
    private function getClassAnnotation( $refClass, $option )
    {
        $comment = $refClass->getDocComment();
        $dimClass = $this->parser->parse( $comment );
        $diList = array();
        if( isset( $dimClass[ 'namespace' ] ) ) {
            $option->setNameSpace( $dimClass[ 'namespace' ] );
        }
        if( isset( $dimClass[ 'singleton' ] ) && $dimClass[ 'singleton' ] ) {
            $option->setSingleton();
        }
        if( isset( $dimClass[ 'scope' ] ) && $dimClass[ 'scope' ] ) {
            $option->setScope( $dimClass[ 'scope' ] );
        }
        if( isset( $dimClass[ 'cacheable' ] ) && $dimClass[ 'cacheable' ] ) {
            $option->setCacheAble();
        }
    }

    /**
     * @param \ReflectionClass $refClass
     * @param Option           $option
     */
    private function constructor( $refClass, $option )
    {
        $refConst   = $refClass->getConstructor();
        $arguments  = $this->analyzeMethod( $refConst );
        if( !empty( $arguments ) ) {
            foreach( $arguments as $arg ) {
                extract( $arg );
                $option->setConstructor( $arg[ 'name' ], $arg[ 'id' ], $arg[ 'default' ] );
            }
        }
    }

    /**
     * get dependency information of properties for a class.
     * searches all properties in parent classes as well.
     *
     * @param \ReflectionClass $refClass
     * @param Option           $option
     */
    private function property( $refClass, $option )
    {
        // loop for all parent classes. 
        do {
            // get all properties. ignore if no properties found. 
            if( !$properties = $refClass->getProperties() ) continue;
            // loop for all properties. 
            foreach( $properties as $refProp )
            {
                // ignore property if already found as @Inject
                if( $option->getProperty( $refProp->name ) ) continue;
                if( !$comments = $refProp->getDocComment() ) continue;
                if( !$info = $this->parser->parse( $comments ) ) continue;
                
                $option->setProperty( $refProp->name, $info[0][ 'id' ] );
            }
        } while( false !== ( $refClass = $refClass->getParentClass() ) );
    }

    /**
     * get dependency information of properties for a class.
     * searches all properties in parent classes as well.
     *
     * @param \ReflectionClass $refClass
     * @param Option           $option
     */
    private function setter( $refClass, $option )
    {
        do {
            
            if( !$methods = $refClass->getMethods() ) continue;
            foreach( $methods as $refMethod )
            {
                if( $refMethod->isConstructor() ) continue;
                if( $option->getSetter( $refMethod->name ) ) continue;
                if( $info = $this->analyzeMethod( $refMethod ) ) {
                    foreach( $info as $arg ) {
                        $option->setSetter( $refMethod->name, $arg[ 'name' ], $arg[ 'id' ], $arg[ 'default' ] );
                    }
                }
            }
        } while( false !== ( $refClass = $refClass->getParentClass() ) );
    }

    /**
     * analyze method for injection.
     * returns array as
     * array(
     *    [ 'name'    => name of argument variable,
     *      'id'      => id to inject, 
     *      'default' => default value,
     *    ],
     * )
     * 
     * @param \ReflectionMethod $refMethod
     * @return bool|array
     */
    private function analyzeMethod( $refMethod )
    {
        // no phpDocs comments. 
        if( !$refMethod ) return array();
        if( !$comments = $refMethod->getDocComment() ) return array();
        // no injection info. 
        if( !$info = $this->parser->parse( $comments ) ) return array();
        // get argument list. 
        $refArgs  = $refMethod->getParameters();
        if( empty( $refArgs ) ) return array();
        
        //get inject list. 
        $injectList = array();
        foreach( $refArgs as $refArg ) 
        {
            $name  = $refArg->getName();
            if( $refArg->isDefaultValueAvailable() ) {
                $default = $refArg->getDefaultValue();
            } else {
                $default = null;
            }
            $id    = isset( $info[ $name ] ) ? $info[ $name ] : null;
            $injectList[] = array(
                'name'    => $name,
                'id'      => $id,
                'default' => $default,
            );
        }
        return $injectList;
    }
    // +----------------------------------------------------------------------+

}
