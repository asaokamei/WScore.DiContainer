<?php
namespace WScore\DiContainer;

return new Container( new Values(), new Forge\Forger( new Forge\Analyzer( new Forge\Parser() ) ) );