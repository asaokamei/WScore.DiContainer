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
        require_once( __DIR__ . '/../../../scripts/require.php' );
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
    
    function test_singleton_annotation()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        // get non-singleton objects
        $class = $names . 'A';
        $object1 = $this->container->get( $class );
        $object2 = $this->container->get( $class );
        $this->assertEquals( $class, '\\' . get_class( $object1 ) );
        $this->assertNotSame( $object1, $object2 );
        // get singleton
        $class = $names . 'S';
        $object1 = $this->container->get( $class );
        $object2 = $this->container->get( $class );
        $this->assertEquals( $class, '\\' . get_class( $object1 ) );
        $this->assertSame( $object1, $object2 );
    }
    
    function test_singleton_option()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        // get non-singleton objects
        $class = $names . 'A';
        $object1 = $this->container->get( $class, array( 'singleton' ) );
        $object2 = $this->container->get( $class );
        $this->assertEquals( $class, '\\' . get_class( $object1 ) );
        $this->assertSame( $object1, $object2 );
        
    }

    function test_singleton_option2()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        // get non-singleton objects
        $class = $names . 'A';
        $object1 = $this->container->get( $class, array( 'singleton' => true ) );
        $object2 = $this->container->get( $class );
        $this->assertEquals( $class, '\\' . get_class( $object1 ) );
        $this->assertSame( $object1, $object2 );

    }
    
    function test_nonexistence_id_returns_null()
    {
        $name  = 'not exist';
        $found = $this->container->get( $name );
        $this->assertEquals( null, $found );
    }
    
    function test_simple_test()
    {
        $name  = 'simple text';
        $value = 'set value';
        $this->container->set( $name, $value );
        $found = $this->container->get( $name );
        $this->assertEquals( $value, $found );
    }
    
    function test_set_closure()
    {
        $value = 'set value';
        $closure = function($c) use( $value ) {
            return $value;
        };
        $this->container->set( 'closure', $closure );
        $found = $this->container->get( 'closure' );
        $this->assertEquals( $value, $found );
    }
    
    function test_class_with_or_wo_slashes()
    {
        $names = 'WScore\tests\DiContainer\MockClass\\';
        // get non-singleton objects
        $class = $names . 'A';
        $object1 = $this->container->get( $class, array( 'singleton' => true ) );
        $object2 = $this->container->get( '\\' . $class );
        $this->assertEquals( $class, get_class( $object1 ) );
        $this->assertSame( $object1, $object2 );
    }

    function test_class_with_or_wo_slashes2()
    {
        $names = 'WScore\tests\DiContainer\MockClass\\';
        // get non-singleton objects
        $class = $names . 'A';
        $object1 = $this->container->get( '\\' . $class, array( 'singleton' => true ) );
        $object2 = $this->container->get( $class );
        $this->assertEquals( $class, get_class( $object1 ) );
        $this->assertSame( $object1, $object2 );
    }
}