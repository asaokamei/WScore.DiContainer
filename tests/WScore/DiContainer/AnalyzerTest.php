<?php
namespace WScore\tests\DiContainer;

use WScore\DiContainer\Cache;
use WScore\DiContainer\Forge\Option;
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
        $this->assertTrue( $return instanceof Option );
        
        $construct = $return->getConstructor();
        $properties= $return->getProperty();
        $setter    = $return->getSetter();
        
        $this->assertEquals( $names.'A', $construct['a']['id'] );
        $this->assertEquals( $names.'B', $construct['b']['id'] );
        $this->assertEquals( $names.'C', $properties['propC'] );
        $this->assertEquals( $names.'C', $setter['setC']['c']['id'] );
        $this->assertEquals( 'singleton', $return->getScope() );
        $this->assertEquals( 'a', $construct['a']['name'] );
        $this->assertEquals( 'b', $construct['b']['name'] );
        $this->assertEquals( 'c', $setter['setC']['c']['name'] );
    }
    function test_ignore_methods_without_inject()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'X';
        $return = $this->analyzer->analyze( $class );
        $setter = $return->getSetter();

        $this->assertArrayNotHasKey( 'noSetter', $setter );
    }

    function test_analyze_inherited_class()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'Y';
        $return = $this->analyzer->analyze( $class );
        $this->assertNotEmpty( $return );

        $construct = $return->getConstructor();
        $properties= $return->getProperty();
        $setter    = $return->getSetter();

        $this->assertEquals( $names.'A', $construct['a']['id'] );
        $this->assertEquals( $names.'B', $construct['b']['id'] );
        $this->assertEquals( $names.'C', $properties['propC'] );
        $this->assertEquals( $names.'CC', $setter['setC']['c']['id'] );
        $this->assertEquals( null, $return->getScope() );
        $this->assertEquals( 'a', $construct['a']['name'] );
        $this->assertEquals( 'b', $construct['b']['name'] );
        $this->assertEquals( 'c', $setter['setC']['c']['name'] );
    }
    
    function test_shared()
    {
        $names = '\WScore\tests\DiContainer\MockClass\\';
        $class = $names . 'Shared';
        $return = $this->analyzer->analyze( $class );

        $this->assertEquals( 'shared', $return->getScope() );
    }
}
