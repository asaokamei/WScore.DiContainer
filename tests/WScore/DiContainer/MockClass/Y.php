<?php
namespace WScore\tests\DiContainer\MockClass;

class Y extends X
{
    /** @var \WScore\tests\DiContainer\MockClass\C  */
    public $setC;

    /**
     * @Inject
     * @param \WScore\tests\DiContainer\MockClass\CC $c
     */
    public function setC( $c ) {
        $this->setC = $c;
    }
}