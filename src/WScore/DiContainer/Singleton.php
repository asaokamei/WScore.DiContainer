<?php
namespace WScore\DiContainer;

class Singleton
{
    private $value;
    
    public function __construct( $value ) {
        $this->value = $value;
    }
    
    public function __invoke() {
        return $this->value;
    }
}