<?php

use Md\Db\Db;
use Md\Db\Repository;

require dirname(__DIR__) . '/vendor/autoload.php';

Db::setConfigDirectory(__DIR__ . '/cfg/');

$repo = new Repository('people', 'id', 'example-mysql');

var_export($repo);

$result = $repo->getAll();

var_export($result);
