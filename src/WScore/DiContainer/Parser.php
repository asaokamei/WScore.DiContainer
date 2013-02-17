<?php
namespace WScore\DiContainer;

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
        if( preg_match( '/@singleton/mi', $comment ) ) {
            $injectList[ 'singleton' ] = true;
            return $injectList;
        }
        if( !preg_match( '/@inject/mi', $comment ) ) return $injectList;
        if( !preg_match_all( "/(@.*)$/mU", $comment, $comments ) ) return $injectList;
        foreach( $comments[1] as $parameter ) 
        {
            if( preg_match( '/@param/i', $parameter, $matches ) ) {
                $list = $this->parseParam( $parameter );
                $injectList[ $list['var'] ] = $list[ 'id' ];
            }
            elseif( preg_match( '/@var/i', $parameter, $matches ) ) {
                $injectList[0] = $this->parseVar( $parameter );
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
        $list = preg_split( '/[\s]+/', $parameter );
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
        $list = preg_split( '/[\s]+/', $parameter );
        if( substr( $list[2], 0, 1 ) === '$' ) $list[2] = substr( $list[2], 1 );
        return array(
            'id'  => $list[1],
            'var' => $list[2],
        );
    }
}