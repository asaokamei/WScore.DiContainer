<?php
namespace WScore\DiContainer\Forge;

class Parser
{
    // +----------------------------------------------------------------------+
    //  parsing @DimInjection in PHPDoc.
    // +----------------------------------------------------------------------+

    /**
     * parse phpDoc comments for Dependency Injection.
     *
     * @param string $comment
     * @return array
     */
    public function parse( $comment )
    {
        $injectList = array();
        if( preg_match( '/@namespace[\s]+([-_\w]+)/mi', $comment, $matches ) ) {
            $injectList[ 'namespace' ] = $matches[1];
        }
        if( preg_match( '/@scope[\s]+([-_\w]+)/mi', $comment, $matches ) ) {
            $injectList[ 'scope' ] = $matches[1];
        }
        if( preg_match( '/@singleton/mi', $comment ) ) {
            $injectList[ 'scope' ] = 'singleton';
        }
        if( preg_match( '/@cacheable/mi', $comment ) ) {
            $injectList[ 'cacheable' ] = true;
            return $injectList;
        }
        if( !preg_match( '/@inject/mi', $comment ) ) return $injectList;
        if( !preg_match_all( "/(@.*)$/mU", $comment, $comments ) ) return $injectList;
        foreach( $comments[1] as $parameter ) 
        {
            if( preg_match( '/@param/i', $parameter, $matches ) ) {
                if( $list = $this->parseParam( $parameter ) ) {
                    $injectList[ $list['var'] ] = $list[ 'id' ];
                }
            }
            elseif( preg_match( '/@var/i', $parameter, $matches ) ) {
                if( $list = $this->parseVar( $parameter ) ) {
                    $injectList[0] = $list;
                }
            }
        }
        return $injectList;
    }

    /**
     * a dumb parser for @param.
     *
     * @param  string  $parameter
     * @return array
     */
    protected function parseVar( $parameter )
    {
        $parameter = trim( $parameter );
        $list = preg_split( '/[\s]+/', $parameter );
        if( count( $list ) < 2 ) return array();
        return array(
            'id'  => $list[1],
        );
    }
    
    /**
     * a dumb parser for @param. 
     * 
     * @param  string  $parameter
     * @return array
     */
    protected function parseParam( $parameter )
    {
        $parameter = trim( $parameter );
        $list = preg_split( '/[\s]+/', $parameter );
        if( count( $list ) < 3 ) return array();
        if( substr( $list[2], 0, 1 ) === '$' ) $list[2] = substr( $list[2], 1 );
        return array(
            'id'  => $list[1],
            'var' => $list[2],
        );
    }
}