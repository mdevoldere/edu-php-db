<?php 

namespace Md\Db;

use Md\Db\Exceptions\BadQueryException;
use PDO;

class DbTools
{
    private static ?DbContext $ctx = null;

    public static function use(DbContext $ctx) 
    {
        self::$ctx = $ctx;
    }

    public static function execMany(string $queries) 
    {
        $q = explode(';', $queries);
        $q = array_filter($q);

        foreach($q as $query) {
            $query = trim($query);
            if(!empty($query)) {
                self::$ctx->pdo->exec($query);
                //Logger::done($query);
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
        self::$ctx->pdo->exec('DROP DATABASE IF EXISTS ' . $dbname .';');
        self::$ctx->pdo->exec('CREATE DATABASE IF NOT EXISTS ' .  $dbname .' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        self::$ctx->pdo->exec('USE ' . $dbname . ';');
    }

    public static function showTables(): string
    {
        $stmt = self::$ctx->pdo->query('SHOW FULL TABLES;')->fetchAll(PDO::FETCH_NUM);
        $r = '';
        foreach($stmt as $t) {
            $ctmt = self::$ctx->pdo->query('SHOW COLUMNS FROM ' .$t[0]. ';')->fetchAll(PDO::FETCH_ASSOC);
            $ctmc = self::$ctx->pdo->query('SELECT COUNT(*) as c FROM ' .$t[0]. ';')->fetch(PDO::FETCH_ASSOC);
            $r .= ($t[0] . " (");
            $cols = [];
            foreach($ctmt as $col) {
                $cols[] = $col['Field'];
            }
            $r .=  implode(', ', $cols); 
            $r .= ') { rows: ' . $ctmc['c']. ' }' . PHP_EOL;
            // $r .= (self::db()->query('SHOW CREATE TABLE '.$t[0].';')->fetch(PDO::FETCH_NUM)[1] ?? 'No create') . PHP_EOL;
        } 
        
        return (count($stmt) . ' TABLES/VIEWS ' . PHP_EOL . $r . PHP_EOL);
    }
}
