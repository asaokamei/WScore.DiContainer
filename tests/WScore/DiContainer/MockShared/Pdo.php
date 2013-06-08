<?php
namespace WScore\tests\DiContainer\MockShared;

use WScore\DiContainer\String as Connector;

class Pdo
{
    public $name = 'Pdo class';

    /**
     * @var string
     */
    public $conn;

    /**
     * @Inject
     * @param Connector $conn
     */
    public function __construct( $conn )
    {
        $this->conn = $conn;
    }
}
