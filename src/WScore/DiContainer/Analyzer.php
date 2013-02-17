<?php
namespace WScore\DiContainer;

class Analyze
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
    public function lists( $className )
    {
        $refClass   = new \ReflectionClass( $className );
        $dimConst   = $this->dimConstructor( $refClass );
        $dimProp    = $this->dimProperty( $refClass );
        $diList     = array(
            'construct' => $dimConst,
            'setter'    => array(),
            'property'  => $dimProp,
        );
        return $diList;
    }

    /**
     * @param \ReflectionClass $refClass
     * @return array
     */
    private function dimConstructor( $refClass )
    {
        if( !$refConst   = $refClass->getConstructor() ) return array();
        if( !$comments   = $refConst->getDocComment()  ) return array();
        $injectList = $this->parser->parseDimDoc( $comments );
        return $injectList;
    }

    /**
     * get dependency information of properties for a class.
     * searches all properties in parent classes as well.
     *
     * @param \ReflectionClass $refClass
     * @return array
     */
    private function dimProperty( $refClass )
    {
        $injectList = array();
        if( !self::$PROPERTY_INJECTION ) return $injectList;
        do {
            if( $properties = $refClass->getProperties() ) {
                foreach( $properties as $refProp ) {
                    if( isset( $injectList[ $refProp->name ] ) ) continue;
                    if( $comments = $refProp->getDocComment() ) {
                        if( $info = $this->parser->parseDimDoc( $comments ) ) {
                            $injectList[ $refProp->name ] = array( end( $info ), $refProp );
                        }
                    }
                }
            }
            $refClass = $refClass->getParentClass();
        } while( false !== $refClass );
        return $injectList;
    }

}
