<?php
namespace WScore\DiContainer;

return new Container(
    new Storage\IdOrNamespace(),
    new Forge\Forger(
        new Forge\Analyzer(
            new Forge\Parser(),
            Cache::getCache()
        ),
        Cache::getCache()
    )
);