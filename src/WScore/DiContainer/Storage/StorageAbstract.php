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