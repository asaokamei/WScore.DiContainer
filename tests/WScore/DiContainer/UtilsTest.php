<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Utils as Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../scripts/require.php' );
        require_once( __DIR__ . '/MockClass/require.php' );
    }
    
    function test_simple_array_considered_as_constructor_option()
    {
        $option = array( 'var' => 'classA' );
        $normal = Utils::normalizeOption( $option );
        $this->assertEquals( $option, $normal['construct'] );

        $option = array( 'var' => 'classA', 'var2' => 'classB' );
        $normal = Utils::normalizeOption( $option );
        $this->assertEquals( $option, $normal['construct'] );
    }
    function test_singleton()
    {
        $option = array( 'var' => 'classA', 'var2' => 'classB', 'singleton' );
        $normal = Utils::normalizeOption( $option );
        $this->assertTrue( isset( $normal['singleton'] ) );
        $option[ 'singleton' ] = true;
        $this->assertNotEquals( $option, $normal['construct'] );
    }
    function test_normalizeOption_sample4()
    {
        $option = array( 'setter' => array( 'var' => 'classA' ) );
        $normal = Utils::normalizeOption( $option );
        $this->assertEquals( 'classA', $normal['setter']['var'] );
    }
    function test_normalizeInfo_no_effect()
    {
        $text = 'normalize test';
        $key  = 'myKey';
        $info = array( $key => array( 'id' => $text, 'key2' => 'value2' ) );
        $norm = Utils::normalizeInjection( $info );
        $this->assertTrue( is_array( $norm ) );
        $this->assertArrayHasKey( 'id', $norm[$key] );
        $this->assertEquals( $text, $norm[$key]['id'] );
        $this->assertEquals( 'value2', $norm[$key]['key2'] );
    }
}
