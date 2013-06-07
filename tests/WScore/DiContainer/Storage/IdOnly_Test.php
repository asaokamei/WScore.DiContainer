<?php
namespace WScore\tests\DiContainer\Storage;

use \WScore\DiContainer\Storage\IdOnly as Storage;

class IdOnly_Test extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals( 'WScore\DiContainer\Storage\IdOnly', get_class( $this->values ) );
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

    public function test_namespace_is_not_effective()
    {
        $this->values->store( 'only', 'normal' );
        $this->values->store( 'test', 'normal' );
        $this->values->store( 'test', 'named', 'named' );

        $this->assertEquals( 'named',  $this->values->fetch( 'test' ) );
        $this->assertEquals( 'named',  $this->values->fetch( 'test', 'named' ) );
        $this->assertEquals( 'normal', $this->values->fetch( 'only', 'named' ) );
    }

    public function test_namespace_only_in_named()
    {
        $this->values->store( 'name', 'only', 'named' );

        $this->assertEquals( 'only',   $this->values->fetch( 'name' ) );
        $this->assertEquals( 'only',   $this->values->fetch( 'name', 'named' ) );
    }
}