<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Values;

class ValuesTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Values */
    var $values;

    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../scripts/require.php' );
    }
    
    public function setUp()
    {
        $this->values = new Values();
    }
    
    public function test1()
    {
        $this->assertEquals( 'WScore\DiContainer\Values', get_class( $this->values ) );
    }
    
    public function test_set_get()
    {
        $this->values->set( 'test', 'value' );
        list( $value, $option ) = $this->values->get( 'test' );
        $this->assertEquals( 'value', $value );
        $this->assertTrue( is_array( $option ) );
    }

    public function test_set_get_option()
    {
        $this->values->set( 'test', 'value', 'option' );
        list( $value, $option ) = $this->values->get( 'test' );
        $this->assertEquals( 'value', $value );
        $this->assertTrue( is_array( $option ) );
        $this->assertEquals( 'option', $option[ 'construct' ][0] );
    }
}