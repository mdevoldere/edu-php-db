<?php 

namespace Md\Db\Tools;

use Md\Db\Db;
use Md\Db\Exceptions\BadQueryException;

class DbTools
{
    public static function execMany(string $queries) 
    {
        $q = explode(';', $queries);
        $q = array_filter($q);

        foreach($q as $query) {
            $query = trim($query);
            if(!empty($query)) {
                Db::getContext()->pdo->exec($query);
            }
        }
    }

    public static function execSingleSqlFile(string $sqlfile)
    {
        if(!is_file($sqlfile)) {
            throw new BadQueryException('Unable to execute SQL from ' . $sqlfile);
        }

        self::execMany(file_get_contents($sqlfile));
    }

    public static function execSqlFiles(string $from)
    {
        foreach(glob($from . '/*.{sql}', GLOB_BRACE) as $sqlfile) {
            self::execSingleSqlFile($sqlfile);
        }
    }

    public static function resetDatabase(string $dbname)
    {
        Db::getContext()->pdo->exec('DROP DATABASE IF EXISTS ' . $dbname .';');
        Db::getContext()->pdo->exec('CREATE DATABASE IF NOT EXISTS ' .  $dbname .' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        Db::getContext()->pdo->exec('USE ' . $dbname . ';');
    }
}
