<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Parser;
use \WScore\DiContainer\Analyzer;
use \WScore\DiContainer\Forger;
use \WScore\DiContainer\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Container */
    var $container;

    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/MockClass/require.php' );
    }
    public function setUp()
    {
        $this->container = include( __DIR__ . '/../../../scripts/instance.php' );
    }
    
    function test_exists()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $this->container->set( 'classX', $class );
        
        $this->assertTrue( $this->container->has( 'classX' ) );
        $this->assertFalse( $this->container->has( 'classZ' ) );
    }

    function test_set_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $this->container->set( 'classX', $class );
        $object = $this->container->get( 'classX' );
        $this->assertEquals( $class, '\\' . get_class( $object ) );
        $this->assertEquals( $names.'A', '\\' . get_class( $object->a ) );
        $this->assertEquals( $names.'B', '\\' . get_class( $object->b ) );
        $this->assertEquals( $names.'C', '\\' . get_class( $object->getPropC() ) );
        $this->assertEquals( $names.'C', '\\' . get_class( $object->setC ) );
    }
    function test_getting_basic_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $object = $this->container->get( $class );
        $this->assertEquals( $class, '\\' . get_class( $object ) );
        $this->assertEquals( $names.'A', '\\' . get_class( $object->a ) );
        $this->assertEquals( $names.'B', '\\' . get_class( $object->b ) );
        $this->assertEquals( $names.'C', '\\' . get_class( $object->getPropC() ) );
        $this->assertEquals( $names.'C', '\\' . get_class( $object->setC ) );
    }
}