<?php
namespace WScore\tests\DiContainer;

use \WScore\DiContainer\Forge\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Forge\Parser */
    var $parser;

    public static function setUpBeforeClass() {
        require_once( __DIR__ . '/../../../scripts/require.php' );
    }
    public function setUp()
    {
        $this->parser = new Parser();
    }
    
    function test_singleton_parsing() {
        $comment = "
        /**
         * This comment should return singleton scope.
         * @Singleton
         * @var    variableVar
         * @param  variableParam
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertNotEmpty( $return );
        $this->assertTrue( $return[ 'scope' ] === 'singleton' );
    }
    function test_scope_parsing() {
        $comment = "
        /**
         * This comment should return only singleton.
         * @scope testScope
         * @var    variableVar
         * @param  variableParam
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertNotEmpty( $return );
        $this->assertTrue( $return[ 'scope' ] === 'testScope' );
        unset( $return['scope' ] );
        $this->assertEmpty( $return );
    }

    /**
     *
     */
    function test_parsing_var_returns_only_one_result() {
        $comment = "
        /**
         * This comment should return parameter list.
         * @Inject
         * @var  variableType
         * @var  variableMore
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertNotEmpty( $return );
        $param = $return[0];
        $this->assertEquals( 'variableMore', $param[ 'id' ] );
        $this->assertArrayNotHasKey( 1, $return );
    }

    /**
     *
     */
    function test_parsing_param() {
        $comment = "
        /**
         * This comment should return parameter list.
         * @Inject
         * @param  parameterType  \$var
         * @param  parameterMore  \$more
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertNotEmpty( $return );
        $this->assertEquals( 'parameterType', $return[ 'var' ] );
        $this->assertEquals( 'parameterMore', $return[ 'more' ] );
    }

    /**
     * 
     */
    function test_parser_returns_empty_if_no_injection() {
        $comment = "
        /**
         * @noInjection
         * @param parameter
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertEmpty( $return );
    }

    /**
     * 
     */
    function test_parser_returns_empty_if_only_injection() {
        $comment = "
        /**
         * @Inject
         * @no parameter
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertEmpty( $return );
    }

    /**
     *
     */
    function test_wrong_param() {
        $comment = "
        /**
         * @Inject
         * @param this_id_is 
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertEmpty( $return );
    }

    /**
     *
     */
    function test_wrong_var() {
        $comment = "
        /**
         * @Inject
         * @var  
         */
        ";
        $return = $this->parser->parse( $comment );
        $this->assertEmpty( $return );
    }
}
