<?php
namespace WScore\tests\DiContainer\MockClass;

/**
 * Class Named
 *
 * @package WScore\tests\DiContainer\MockClass
 * 
 * @namespace   test
 */
class N
{
    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockClass\A
     */
    public $a;
    
    public function __construct() {}
}