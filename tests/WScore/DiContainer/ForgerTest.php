<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Forge\Parser;
use \WScore\DiContainer\Forge\Analyzer;
use \WScore\DiContainer\Forge\Forger;
use \WScore\tests\DiContainer\MockClass\Container;

class ForgerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Forge\Parser */
    var $parser;

    /** @var \WScore\DiContainer\Forge\Analyzer */
    var $analyzer;

    /** @var \WScore\DiContainer\Forge\Forger */
    var $forger;
    
    var $container;
    
    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../scripts/require.php' );
        require_once( __DIR__ . '/MockClass/require.php' );
    }
    public function setUp()
    {
        $this->parser = new Parser();
        $this->analyzer = new Analyzer( $this->parser );
        $this->container = new Container();
        $this->forger = new Forger( $this->analyzer );
    }

    function test_injection_inherit_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'Y';
        $object = $this->forger->forge( $this->container, $class );
        $this->assertEquals( $class, '\\' . get_class( $object ) );
        $this->assertEquals( $names.'A', $object->a );
        $this->assertEquals( $names.'B', $object->b );
        $this->assertEquals( $names.'C', $object->getPropC() );
        $this->assertEquals( $names.'CC', $object->setC );
    }

    function test_injection_basic_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $object = $this->forger->forge( $this->container, $class );
        $this->assertEquals( $class, '\\' . get_class( $object ) );
        $this->assertEquals( $names.'A', $object->a );
        $this->assertEquals( $names.'B', $object->b );
        $this->assertEquals( $names.'C', $object->getPropC() );
        $this->assertEquals( $names.'C', $object->setC );
    }
}

    