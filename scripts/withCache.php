<?php
namespace WScore\DiContainer;

require_once __DIR__ . '/require.php';
return new Container(
    new Forger(
        new Analyzer( new Parser(), Cache::getCache() ),
        Cache::getCache()
    )
);