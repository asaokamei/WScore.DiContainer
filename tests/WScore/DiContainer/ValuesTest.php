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

    public function test_set_with_option()
    {
        $this->values->set( 'test', 'value', 'option' );
        list( $value, $option ) = $this->values->get( 'test' );
        $this->assertEquals( 'value', $value );
        $this->assertTrue( is_array( $option ) );
        $this->assertEquals( 'option', $option[ 'construct' ][0] );
    }

    public function test_setOption()
    {
        $this->values->set( 'test', 'value', 'option' );
        $this->values->setOption( 'test', array( 'more' => 'test' ) );
        list( $value, $option ) = $this->values->get( 'test' );
        $this->assertEquals( 'value', $value );
        $this->assertTrue( is_array( $option ) );
        $this->assertEquals( 'option', $option[ 'construct' ][0] );
        $this->assertEquals( 'test', $option[ 'construct' ]['more'] );
    }
    
    public function test_namespace()
    {
        $this->values->set( 'only', 'normal', 'option' );
        $this->values->set( 'test', 'normal', 'option' );
        $this->values->set( 'test', 'named',  'option', 'named' );

        $this->assertEquals( 'normal', $this->values->get( 'test' )[0] );
        $this->assertEquals( 'named',  $this->values->get( 'test', 'named' )[0] );
        $this->assertEquals( 'normal', $this->values->get( 'only', 'named' )[0] );
    }

    public function test_namespace_only_in_named()
    {
        $this->values->set( 'name', 'only',   'option', 'named' );

        $this->assertEquals( null,     $this->values->get( 'name' )[0] );
        $this->assertEquals( 'only',   $this->values->get( 'name', 'named' )[0] );
    }
}