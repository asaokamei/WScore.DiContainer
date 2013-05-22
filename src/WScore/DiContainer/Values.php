<?php
namespace WScore\DiContainer;

class Values
{
    const COL_VALUE  = 0;
    const COL_OPTION = 1;

    /** @var array  */
    private $values = array();

    /** @var string  */
    private $sep = '-|-';

    /**
     * @param string $id
     * @param mixed  $value
     * @param array  $option
     * @param null|string $namespace
     */
    public function set( $id, $value, $option=array(), $namespace=null )
    {
        $id = $this->named( $id, $namespace );
        $this->values[ $id ] = array( self::COL_VALUE => $value );
        $this->setOption( $id, $option, true, $namespace );
    }

    /**
     * Sets an option for forging an object for the $id service.
     *
     * @param string $id
     * @param array  $option
     * @param bool   $reset
     * @param null|string $namespace
     * @return void
     */
    public function setOption( $id, $option, $reset=false, $namespace=null )
    {
        $id = $this->named( $id, $namespace );
        $option = Utils::normalizeOption( $option );
        if( !$reset && isset( $this->values[ $id ][ self::COL_OPTION ] ) ) {
            $option = array_merge( $this->values[ $id ][ self::COL_OPTION ], $option );
        }
        $this->values[ $id ][ self::COL_OPTION ] = $option;
    }

    /**
     * returns ( $value, $option ).
     *
     * @param string $id
     * @param null|string $namespace
     * @return null|array
     */
    public function get( $id, $namespace=null )
    {
        if( array_key_exists( $this->named( $id, $namespace ), $this->values ) ) {
            return $this->values[ $this->named( $id, $namespace ) ];
        }
        return array_key_exists( $id, $this->values ) ? $this->values[ $id ] : null;
    }

    /**
     * @param string $id
     * @param string $namespace
     * @return string
     */
    private function named( $id, $namespace ) {
        if( $namespace ) {
            $id = $namespace . $this->sep . $id;
        }
        return $id;
    }
}