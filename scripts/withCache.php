<?php
namespace WScore\DiContainer;

require_once __DIR__ . '/require.php';
return new Container(
    new Values(),
    new Forge\Forger(
        new Forge\Analyzer( new Forge\Parser(), Cache::getCache() ),
        Cache::getCache()
    )
);