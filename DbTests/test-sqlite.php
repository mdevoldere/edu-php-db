<?php

use Md\Db\Db;
use Md\Db\Repository;

require dirname(__DIR__) . '/vendor/autoload.php';

Db::setConfigDirectory(__DIR__ . '/cfg/');

$repo = new Repository('people', 'id', 'example-sqlite');

var_export($repo);

echo PHP_EOL;

$result = $repo->getAll();

var_export($result);
