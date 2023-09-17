<?php 

namespace Md\Db\Tools;

use Md\Db\Db;
use PDO;

class InformationSchema
{
    public static function getFullTables(): array
    {
        return Db::getContext()->pdo->query('SHOW FULL TABLES;')->fetchAll(PDO::FETCH_NUM);
    }

    public static function getColumns(string $table): array
    {
        $r = [];
        $cols = Db::getContext()->pdo->query('SHOW COLUMNS FROM ' . $table . ';')->fetchAll(PDO::FETCH_ASSOC);

        foreach($cols as $col) {
            $r[$col['Field']] = $col;
        }

        return $r;
    }
    
    public static function getPrimaryKey(string $table): string
    {
        return (Db::getContext()->pdo->query("SHOW INDEX FROM " . $table . " WHERE Key_name = 'PRIMARY';")->fetch(PDO::FETCH_ASSOC)['Column_name'] ?? 'id');
    }

    public static function getNumRows(string $table): int
    {
        return (Db::getContext()->pdo->query('SELECT COUNT(*) as c FROM ' .$table. ';')->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
    }

    public static function getSchema(): array 
    {
        $tables = [];
        $stmt = self::getFullTables();
        
        foreach($stmt as $t) {
            $tname = $t[0];
            $tables[$tname] = [
                'name' => $tname,
                'pk' => self::getPrimaryKey($tname),
                'rows' => self::getNumRows($tname),
                'cols' => self::getColumns($tname),
                // 'create' => ($pdo->query('SHOW CREATE TABLE '.$tname.';')->fetch(PDO::FETCH_NUM)[1] ?? '')
            ];
        } 

        return $tables;
    }

    public static function showTables(): string
    {
        $tables = self::getSchema();

        $r = PHP_EOL . '';
        foreach($tables as $t) {
            $r .= ($t['name'] . ' (');
            $r .= implode(',', array_keys($t['cols']));
            $r .= (') { rows: ' . $t['rows'] . ' }' . PHP_EOL);
        }

        return (count($tables) . ' TABLES/VIEWS ' . PHP_EOL . $r . PHP_EOL);
    }
}
