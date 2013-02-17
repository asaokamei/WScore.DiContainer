<?php
namespace WScore\tests\DiContainer\MockClass;

class Y extends X
{
    /**
     * @Inject
     * @param \WScore\tests\DiContainer\MockClass\CC $c
     */
    public function setC( $c ) {
        $this->setC = $c;
    }
}