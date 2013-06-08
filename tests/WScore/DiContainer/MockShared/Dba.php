<?php
namespace WScore\tests\DiContainer\MockShared;

use WScore\DiContainer\String as Connector;

class Dba
{
    public $name = 'Query class';

    /**
     * @Inject
     * @var \WScore\tests\DiContainer\MockShared\Pdo
     */
    public $pdo;

}
