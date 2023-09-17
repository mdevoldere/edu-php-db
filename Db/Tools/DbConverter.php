<?php 

namespace Md\Db\Tools;


class DbConverter 
{
    public static function ArrayToSqlInsert(string $tablename, array $data) 
    {
        if(count($data) < 1) {
            return '';
        }

        $cols = [];
        $rows = [];
        
        foreach($data as $item) {
            if (empty($cols)) {
                $cols = array_keys($item);
            }
            $item = array_map(function($n) { return ($n !== null) ? addslashes($n) : null; }, $item);
            $rows[] = "('" . implode("', '", $item) . "')"; 
        }

        return 
            ('INSERT INTO ' . $tablename . PHP_EOL .
            '(' . implode(',', $cols) . ')' . PHP_EOL . 
            'VALUES ' . PHP_EOL .
            implode(',' . PHP_EOL, $rows) . ';') . PHP_EOL . PHP_EOL;
    }

}
