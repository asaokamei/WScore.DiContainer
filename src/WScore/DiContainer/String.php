<?php
namespace WScore\DiContainer;

class String
{
    private $string;

    /**
     * @param $string
     */
    public function __construct( $string ) {
        $this->string = $string;
    }

    /**
     * @param string $name
     * @param array $args
     * @return string
     */
    public function __call( $name, $args ) {
        $this->string = $name( $this->string );
        return $this->string;
    }
    
    /**
     * @return mixed
     */
    public function __toString() {
        return $this->string;
    }
}