<?php
namespace WScore\tests\DiContainer\MockShared;

/**
 * Class ModelC
 *
 * @package WScore\tests\DiContainer\MockShared
 * 
 * @namespace moreTest
 */
class ModelC
{
    public $name = 'ModelC class';

    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockShared\Query
     */
    public $query;
}
