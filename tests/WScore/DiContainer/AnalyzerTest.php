<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Parser;
use \WScore\DiContainer\Analyzer;

class AnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Parser */
    var $parser;
    
    /** @var \WScore\DiContainer\Analyzer */
    var $analyzer;

    public static function setUpBeforeClass() {
        require( __DIR__ . '/../../../scripts/require.php' );
        require( __DIR__ . '/MockClass/require.php' );
    }
    public function setUp()
    {
        $this->parser = new Parser();
        $this->analyzer = new Analyzer( $this->parser );
    }

    function test_analyze_returns_reflection_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $return = $this->analyzer->analyze( $class );
        $this->assertNotEmpty( $return );
        $this->assertEquals( new \ReflectionClass( $class ), $return[ 'reflections']['class' ] );
        
        $this->assertEquals( $names.'A', $return['construct']['$a'] );
        $this->assertEquals( $names.'B', $return['construct']['$b'] );
        $this->assertEquals( $names.'C', $return['property']['propC'] );
        $this->assertEquals( $names.'C', $return['setter']['setC']['$c'] );
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
        $this->assertEquals( new \ReflectionClass( $class ), $return[ 'reflections']['class' ] );

        $this->assertEquals( $names.'A', $return['construct']['$a'] );
        $this->assertEquals( $names.'B', $return['construct']['$b'] );
        $this->assertEquals( $names.'C', $return['property']['propC'] );
        $this->assertEquals( $names.'CC', $return['setter']['setC']['$c'] );
    }
}
