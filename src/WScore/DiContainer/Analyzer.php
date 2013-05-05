<?php
namespace WScore\DiContainer;

use \WScore\DiContainer\Cache_Interface;

class Analyzer implements \Serializable
{
    /** @var \WScore\DiContainer\Parser */
    protected $parser;
    
    /** @var array|Cache_Interface  */
    protected $cache = array();

    /**
     * @param \WScore\DiContainer\Parser           $parser
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
        if( $this->cache instanceof Cache_Interface ) {
            $name = $this->normalize( $className );
            $this->cache->store( $name, $diList );
            return;
        }
        $this->cache[ $className ] = $diList;
    }
    
    /**
     * list dependencies of a className.
     *
     * @param string $className
     * @return array
     */
    public function analyze( $className )
    {
        if( false !== $diList = $this->fetch( $className ) ) return $diList;

        $refClass = new \ReflectionClass( $className );
        $dimConst = $this->constructor( $refClass );
        $dimProp  = $this->property( $refClass );
        $dimSet   = $this->setter( $refClass );
        $diList   = $this->getClassAnnotation( $refClass );
        $diList   = array_merge( $diList, array(
            'construct' => $dimConst,
            'setter'    => $dimSet,
            'property'  => $dimProp,
        ) );
        $this->store( $className, $diList );
        return $diList;
    }

    /**
     * @param \ReflectionClass $refClass
     * @return array
     */
    private function getClassAnnotation( $refClass )
    {
        $comment = $refClass->getDocComment();
        $dimClass = $this->parser->parse( $comment );
        $diList = array();
        if( isset( $dimClass[ 'singleton' ] ) && $dimClass[ 'singleton' ] ) {
            $diList[ 'singleton' ] = true;
        } else {
            $diList[ 'singleton' ] = false;
        }
        if( isset( $dimClass[ 'cacheable' ] ) && $dimClass[ 'cacheable' ] ) {
            $diList[ 'cacheable' ] = true;
        } else {
            $diList[ 'cacheable' ] = false;
        }
        return $diList;
    }

    /**
     * @param \ReflectionClass $refClass
     * @return array
     */
    private function constructor( $refClass )
    {
        $refConst   = $refClass->getConstructor();
        return $this->analyzeMethod( $refConst );
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
        // loop for all parent classes. 
        do {
            // get all properties. ignore if no properties found. 
            if( !$properties = $refClass->getProperties() ) continue;
            // loop for all properties. 
            foreach( $properties as $refProp )
            {
                // ignore property if already found as @Inject
                if( isset( $injectList[ $refProp->name ] ) ) continue;
                if( !$comments = $refProp->getDocComment() ) continue;
                if( !$info = $this->parser->parse( $comments ) ) continue;
                
                $injectList[ $refProp->name ] = $info[0]['id'];
            }
        } while( false !== ( $refClass = $refClass->getParentClass() ) );
        return $injectList;
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
        do {
            
            if( !$methods = $refClass->getMethods() ) continue;
            foreach( $methods as $refMethod )
            {
                if( $refMethod->isConstructor() ) continue;
                if( isset( $injectList[ $refMethod->name ] ) ) continue;
                if( $info = $this->analyzeMethod( $refMethod ) ) {
                    $injectList[$refMethod->name] = $info;
                }
            }
        } while( false !== ( $refClass = $refClass->getParentClass() ) );
        return $injectList;
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
    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        $data = array();
        $list = get_object_vars( $this );
        foreach( $list as $var => $val ) {
            if( $var !== 'cache' ) $data[ $var ] = $val;
        }
        return serialize( $data );
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized   The string representation of the object.
     * @return mixed the original value unserialized.
     */
    public function unserialize( $serialized )
    {
        $info = unserialize( $serialized );
        foreach( $info as $var => $val ) {
            $this->$var = $val;
        }
    }
    // +----------------------------------------------------------------------+

}
