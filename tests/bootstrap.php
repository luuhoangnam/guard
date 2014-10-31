<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->addPsr4("Nam\\Guard\\TestCases\\", __DIR__ . '/TestCases/');

return $loader;