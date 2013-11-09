<?php
include( dirname( __DIR__ ) . '/autoload.php' );
use WScore\DiContainer\Container;

/** @var Container $di */
$di = include( dirname( dirname( __DIR__ ) ) . '/scripts/container.php' );

// set up left leg. 
$di->set( 'LeftLeg',  '\Robots\Leg' )->inNamespace( 'leftLeg' );
$di->set( 'partName', 'Left Leg'    )->inNamespace( 'leftLeg' );

// set up right leg. 
$di->set( 'RightLeg', '\Robots\Leg' )->inNamespace( 'rightLeg' );
$di->set( 'partName', 'Right Leg'   )->inNamespace( 'rightLeg' );

/** @var \Robots\Body $robot */
$robot = $di->get( '\Robots\Body' );
$robot->walk(5);

var_dump( $robot );