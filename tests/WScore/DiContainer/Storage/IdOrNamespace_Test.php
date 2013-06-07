<?php
namespace WScore\tests\DiContainer\Storage;

use \WScore\DiContainer\Storage\IdOrNamespace as Storage;

class IdOrNamespace_Test extends \PHPUnit_Framework_TestCase
{
    /** @var Storage */
    var $values;

    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../../scripts/require.php' );
    }

    public function setUp()
    {
        $this->values = new Storage();
    }

    public function test1()
    {
        $this->assertEquals( 'WScore\DiContainer\Storage\IdOrNamespace', get_class( $this->values ) );
    }

    public function test_set_get()
    {
        $this->values->store( 'test', 'value' );
        $value = $this->values->fetch( 'test' );
        $this->assertEquals( 'value', $value );
    }

    public function test_set_with_option()
    {
        $this->values->store( 'test', 'value' );
        $value = $this->values->fetch( 'test' );
        $this->assertEquals( 'value', $value );
    }

    public function test_namespace()
    {
        $this->values->store( 'only', 'normal' );
        $this->values->store( 'test', 'normal' );
        $this->values->store( 'test', 'named', 'named' );

        $this->assertEquals( 'normal', $this->values->fetch( 'test' ) );
        $this->assertEquals( 'named',  $this->values->fetch( 'test', 'named' ) );
        $this->assertEquals( 'normal', $this->values->fetch( 'only', 'named' ) );
    }

    public function test_namespace_only_in_named()
    {
        $this->values->store( 'name', 'only', 'named' );

        $this->assertEquals( null,     $this->values->fetch( 'name' ) );
        $this->assertEquals( 'only',   $this->values->fetch( 'name', 'named' ) );
    }
    
    function test_exists()
    {
        $this->values->store( 'only', 'normal' );

        $this->assertTrue(  $this->values->exists( 'only' ) );
        $this->assertTrue(  $this->values->exists( 'only', 'named' ) );
        $this->assertFalse( $this->values->exists( 'none' ) );
    }

    function test_exists_with_namespace()
    {
        $this->values->store( 'only', 'normal', 'named' );

        $this->assertFalse( $this->values->exists( 'only' ) );
        $this->assertTrue(  $this->values->exists( 'only', 'named' ) );
        $this->assertFalse( $this->values->exists( 'only', 'nope' ) );
        $this->assertFalse( $this->values->exists( 'none' ) );
    }
}