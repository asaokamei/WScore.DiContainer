<?php
namespace WScore\tests\DiContainer\MockClass;

/**
 * Class Shared
 *
 * @package WScore\tests\DiContainer\MockClass
 * 
 * @scope shared
 */
class Shared
{
    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockClass\A
     */
    public $a;
    
    public function __construct() {}
}