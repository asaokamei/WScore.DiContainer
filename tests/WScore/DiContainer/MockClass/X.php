<?php
namespace WScore\tests\DiContainer\MockClass;

class X
{
    /** 
     * @Inject 
     * @var \WScore\tests\DiContainer\MockClass\C 
     */
    public $propC;
    
    public $setC;
    
    /**
     * @Inject
     * @param \WScore\tests\DiContainer\MockClass\A $a
     * @param \WScore\tests\DiContainer\MockClass\B $b
     */
    public function __construct( $a, $b ) {        
    }

    /**
     * @Inject
     * @param \WScore\tests\DiContainer\MockClass\C $c
     */
    public function setC( $c ) {
        $this->setC = $c;
    }
}