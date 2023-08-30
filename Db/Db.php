<?php

namespace Md\Db;

use PDO;
use Exception;
use Md\Db\Exceptions\BadContextException;
use Md\Db\Exceptions\BadDbConfigException;

/** Class Db
 * DbContext Factory (Multiton)
 *
 * @author   MDevoldere 
 * @version  1.1.0
 * @access   public
 */
class Db 
{
    private static string $cfgDir = '';

    /** @var DbContextInterface[] $ctx DbContext storage */
    private static array $ctx = [];

    public static function setConfigDirectory(string $_dir): void
    {
        if(!is_dir($_dir)) {
            throw new BadDbConfigException('Invalid Directory');
        }

        self::$cfgDir = (rtrim($_dir, '/') . '/');
    }

    /**
     * Get DbContext by name
     * @param string $_context the DbContext Name
     * @return DbContextInterface the DbContext object
     */
    public static function getContext(string $_context): DbContextInterface
    {
        if(!isset(self::$ctx[$_context])) {
            $p = (self::$cfgDir . $_context . '.php');
            if(!is_file($p)) {
                throw new BadDbConfigException('NotFound');
            }
            self::$ctx[$_context] = self::createContext((require $p));  
        }
        return self::$ctx[$_context];
    }

    /**
     * Create a new DbContext using config array
     * @param $c db connection information
     * Example for SQLITE: 
     * [0 => 'sqlite:'/path/to/mydb.sqlite']
     * Example for MySQL: 
     * [
     *  0 => 'mysql:host=localhost;port=3306;dbname=mydb;charset=utf8mb4',
     *  1 => 'root', // default: root
     *  2 => '', // default: empty password
     *  3 => [] // optional array of PDO options
     * ]
     * @return DbContextInterface a new DbContextInterface object
     */
    private static function createContext(array $c): ?DbContextInterface
    {
        try 
        {
            $dbt = explode(':', $c[0] ?? '')[0] ?? null;

            switch($dbt)
            {
                case 'mysql':
                    return new DbContext(self::getPdoMysql($c[0], $c[1] ?? 'root', $c[2] ?? '', $c[3] ?? []));
                break;
                case 'sqlite':
                    return new DbContext(self::getPdoSqlite($c[0]));
                break;
                default:
                    throw new BadContextException("DbType must be 'mysql' or 'sqlite'");
                break;
            }
        } catch (Exception $ex) {
            throw new BadContextException('', $ex);
        }
    }

    /**
     * 
     */
    public static function getPdoMysql(string $dsn, string $username = 'root', string $password = '', array $options = []): PDO
    {
        if(empty($options)) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
        }
        return new PDO($dsn, $username, $password, $options);
    }

    /**
     * 
     */
    public static function getPdoSqlite(string $file): PDO
    {
        $pdo = new PDO($file, 'charset=utf8');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('pragma synchronous = off;');
        return $pdo;
    }
}
