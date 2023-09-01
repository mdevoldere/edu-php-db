<?php

use Md\Db\Db;
use Md\Db\DbTools;
use Md\Db\Repository;

require dirname(__DIR__) . '/vendor/autoload.php';

Db::setConfigDirectory(__DIR__ . '/cfg/');

$db = Db::getContext('example-mysql');

DbTools::use($db);
DbTools::resetDatabase('db_test');
DbTools::execSingleSqlFile(__DIR__. '/data/example-mysql.sql');
echo DbTools::showTables();

$repo = new Repository('people', 'id', 'example-mysql');

var_export($repo);

echo PHP_EOL;

$result = $repo->getAll();

var_export($result);
