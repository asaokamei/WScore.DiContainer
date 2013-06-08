<?php
namespace WScore\DiContainer\Storage;

abstract class StorageAbstract implements StorageInterface
{
    // +----------------------------------------------------------------------+
    //  Caching using simple array. Mostly for testing purpose.
    // +----------------------------------------------------------------------+
    protected $cache = array();

    /** @var string  */
    protected $sep = '-|-';

    /**
     * clears the contents.
     */
    public function clear()
    {
        $this->cache = array();
    }

    /**
     * @param string $id
     * @param string $name1
     * @param string $name2
     */
    public function resetNamespace( $id, $name1, $name2 )
    {
        $id1 = $this->named( $id, $name1 );
        if( isset( $this->cache[ $id1 ] ) ) {
            $id2 = $this->named( $id, $name2 );
            $this->cache[ $id2 ] = $this->cache[ $id1 ];
            unset( $this->cache[ $id1 ] );
        }
    }
    
    /**
     * @param string $id
     * @param string $namespace
     * @return string
     */
    protected function named( $id, $namespace ) {
        if( $namespace ) {
            $id = $namespace . $this->sep . $id;
        }
        return $id;
    }
}