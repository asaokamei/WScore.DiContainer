<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

class AllDicTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s DiContainer' );
        $folder = __DIR__ . '/';
        $suite->addTestFile( $folder . 'Storage/IdWithNamespace_Test.php' );
        $suite->addTestFile( $folder . 'Storage/IdOrNamespace_Test.php' );
        $suite->addTestFile( $folder . 'Storage/IdOnly_Test.php' );
        $suite->addTestFile( $folder . 'UtilsTest.php' );
        $suite->addTestFile( $folder . 'ParserTest.php' );
        $suite->addTestFile( $folder . 'AnalyzerTest.php' );
        $suite->addTestFile( $folder . 'ForgerTest.php' );
        $suite->addTestFile( $folder . 'Forger_CachedTest.php' );
        $suite->addTestFile( $folder . 'ContainerTest.php' );
        $suite->addTestFile( $folder . 'Namespace_Test.php' );

        return $suite;
    }
}
