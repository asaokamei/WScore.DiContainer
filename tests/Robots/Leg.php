<?php
namespace Robots;

class Leg
{
    /**
     * @Inject
     * @var \Robots\Controller
     */
    public $controller;
    
    public function step()
    {
        $this->controller->work( 'step' );
    }
}