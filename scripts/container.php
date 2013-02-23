<?php
namespace WScore\DiContainer;

return new Container( new Forger( new Analyzer( new Parser() ) ) );