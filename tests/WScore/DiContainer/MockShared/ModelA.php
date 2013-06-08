<?php
namespace WScore\tests\DiContainer\MockShared;

/**
 * Class ModelA
 *
 * @package WScore\tests\DiContainer\MockShared
 * 
 * @namespace myTest
 */
class ModelA
{
    public $name = 'ModelA class';

    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockShared\Query
     */
    public $query;
}
