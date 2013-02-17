<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Parser;
use \WScore\DiContainer\Analyzer;
use \WScore\DiContainer\Forger;
use \WScore\DiContainer\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Parser */
    var $parser;

    /** @var \WScore\DiContainer\Analyzer */
    var $analyzer;

    /** @var \WScore\DiContainer\Forger */
    var $forger;

    /** @var \WScore\DiContainer\Container */
    var $container;

    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../scripts/require.php' );
        require_once( __DIR__ . '/MockClass/require.php' );
    }
    public function setUp()
    {
        $this->parser    = new Parser();
        $this->analyzer  = new Analyzer( $this->parser );
        $this->forger    = new Forger( $this->analyzer );
        $this->container = new Container( $this->forger );
    }

    function test_injection_basic_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $object = $this->container->get( $class );
        $this->assertEquals( $class, '\\' . get_class( $object ) );
        $this->assertEquals( $names.'A', $object->a );
        $this->assertEquals( $names.'B', $object->b );
        $this->assertEquals( $names.'C', $object->getPropC() );
        $this->assertEquals( $names.'C', $object->setC );
    }
}