<?php
namespace Robots;

use Robots\Leg as LeftLeg;
use Robots\Leg as RightLeg;

class Body
{
    /**
     * @Inject
     * @var LeftLeg
     */
    public $leftLeg;

    /**
     * @Inject
     * @var RightLeg
     */
    public $rightLeg;

    /**
     * @param int $steps
     */
    public function walk( $steps )
    {
        for( $i = 0; $i < $steps; $i++ ) {
            if( $this->leftLeg ) $this->leftLeg->step();
            if( $this->rightLeg ) $this->rightLeg->step();
        }
    }
}