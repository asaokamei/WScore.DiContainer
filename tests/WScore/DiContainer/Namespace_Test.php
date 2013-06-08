<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Container;

class Namespace_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Container */
    var $container;
    
    var $mocks = '\WScore\tests\DiContainer\MockShared\\';

    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../scripts/require.php' );
        require_once( __DIR__ . '/MockShared/require.php' );
    }
    public function setUp()
    {
        $this->container = include( __DIR__ . '/../../../scripts/instance.php' );
    }

    function test_exists()
    {
        $class = $this->mocks . 'ModelA';
        $this->container->set( 'Connector', 'connect to db' );
        /** @var MockShared\ModelA $object */
        $object = $this->container->get( $class );
        $this->assertEquals( $class, '\\' . get_class( $object ) );
        $this->assertEquals( 'connect to db', $object->query->dba->pdo->conn );
    }
    
    function test_shared_namespace()
    {
        $classA = $this->mocks . 'ModelA';
        $classB = $this->mocks . 'ModelB';
        $classC = $this->mocks . 'ModelC';
        $query  = $this->mocks . 'Query';
        $name1  = 'myTest';
        $name2  = 'moreTest';
        $this->container->set( 'Connector', 'connect to myTest'   )->resetNamespace( $name1 );
        $this->container->set( 'Connector', 'connect to moreTest' )->resetNamespace( $name2 );
        $this->container->set( $query  )->scope( 'shared' );

        /** @var \WScore\tests\DiContainer\MockShared\ModelA $objectA */
        /** @var \WScore\tests\DiContainer\MockShared\ModelB $objectC */
        /** @var \WScore\tests\DiContainer\MockShared\ModelC $objectB */
        $objectA = $this->container->get( $classA );
        $objectB = $this->container->get( $classB );
        $objectC = $this->container->get( $classC );
        
        // check connectors
        $this->assertEquals( 'connect to myTest',   $objectA->query->dba->pdo->conn );
        $this->assertEquals( 'connect to myTest',   $objectB->query->dba->pdo->conn );
        $this->assertEquals( 'connect to moreTest', $objectC->query->dba->pdo->conn );
        
        // check query are identical
        $this->assertSame( $objectA->query, $objectB->query );
        $this->assertSame( $objectA->query->dba, $objectB->query->dba );
        $this->assertNotSame( $objectA->query, $objectC->query );
    }
}