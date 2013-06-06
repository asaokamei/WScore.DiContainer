<?php
namespace WScore\tests\DiContainer;

use WScore\DiContainer\Cache;
use \WScore\DiContainer\Forge\Parser;
use \WScore\DiContainer\Forge\Analyzer;

class AnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Forge\Parser */
    var $parser;
    
    /** @var \WScore\DiContainer\Forge\Analyzer */
    var $analyzer;

    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../scripts/require.php' );
        require_once( __DIR__ . '/MockClass/require.php' );
    }
    public function setUp()
    {
        $this->parser = new Parser();
        $cache = Cache::getCache();
        $this->analyzer = new Analyzer( $this->parser, $cache );
    }

    function test_analyze_class_wo_phpDocs()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'C';
        $return = $this->analyzer->analyze( $class );
        $this->assertNotEmpty( $return );
    }
    function test_analyze_returns_reflection_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $return = $this->analyzer->analyze( $class );
        $this->assertNotEmpty( $return );
        
        $this->assertEquals( $names.'A', $return['construct'][0]['id'] );
        $this->assertEquals( $names.'B', $return['construct'][1]['id'] );
        $this->assertEquals( $names.'C', $return['property']['propC'] );
        $this->assertEquals( $names.'C', $return['setter']['setC'][0]['id'] );
        $this->assertTrue( $return[ 'singleton' ] );
        $this->assertEquals( 'a', $return['construct'][0]['name'] );
        $this->assertEquals( 'b', $return['construct'][1]['name'] );
        $this->assertEquals( 'c', $return['setter']['setC'][0]['name'] );
    }
    function test_ignore_methods_without_inject()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $return = $this->analyzer->analyze( $class );

        $this->assertArrayHasKey( 'setter', $return );
        $this->assertArrayNotHasKey( 'noSetter', $return['setter'] );
    }

    function test_analyze_inherited_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'Y';
        $return = $this->analyzer->analyze( $class );
        $this->assertNotEmpty( $return );

        $this->assertEquals( $names.'A', $return['construct'][0]['id'] );
        $this->assertEquals( $names.'B', $return['construct'][1]['id'] );
        $this->assertEquals( $names.'C', $return['property']['propC'] );
        $this->assertEquals( $names.'CC', $return['setter']['setC'][0]['id'] );
        $this->assertFalse( $return[ 'singleton' ] );
        $this->assertEquals( 'a', $return['construct'][0]['name'] );
        $this->assertEquals( 'b', $return['construct'][1]['name'] );
        $this->assertEquals( 'c', $return['setter']['setC'][0]['name'] );
    }
}
