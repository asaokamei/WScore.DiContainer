<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

class AllDicTests_Suite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s DiContainer' );
        $folder = __DIR__ . '/';
        $suite->addTestFile( $folder . 'ParserTest.php' );
        $suite->addTestFile( $folder . 'AnalyzerTest.php' );
        $suite->addTestFile( $folder . 'ForgerTest.php' );

        return $suite;
    }
}
