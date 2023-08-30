<?php

use Md\Db\Db;
use Md\Db\Repository;

require dirname(__DIR__) . '/vendor/autoload.php';

Db::setConfigDirectory(__DIR__ . '/cfg/');

$db = Db::getContext('example-sqlite');

var_export($db);

$repo = new Repository('people', 'id', 'example-sqlite');

var_export($repo);

$result = $repo->getAll();

var_export($result);
