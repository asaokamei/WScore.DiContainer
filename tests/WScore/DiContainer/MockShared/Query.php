<?php
namespace WScore\tests\DiContainer\MockShared;

class Query
{
    public $name = 'Query class';

    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockShared\Dba
     */
    public $dba;
}
