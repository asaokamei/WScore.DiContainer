<?php
namespace Robots;

use WScore\DiContainer\Types\String as partName;

class Controller
{
    /**
     * @Inject
     * @var partName
     */
    public $partName = 'unknown parts';
    
    public function work( $move )
    {
        echo "{$move} {$this->partName} \n";
    }
}