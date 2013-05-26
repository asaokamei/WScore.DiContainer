<?php
namespace WScore\tests\DiContainer\MockClass;

class Named
{
    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockClass\A
     */
    public $a;
    
    public function __construct() {}
}