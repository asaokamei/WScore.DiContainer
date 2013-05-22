<?php
namespace WScore\DiContainer;

return new Container( new Values(), new Forger( new Analyzer( new Parser() ) ) );