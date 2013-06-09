<?php
namespace WScore\tests\DiContainer\MockShared;

/**
 * Class ModelB
 *
 * @package WScore\tests\DiContainer\MockShared
 */
class ModelB
{
    public $name = 'ModelB class';

    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockShared\Query
     */
    public $query;
}
