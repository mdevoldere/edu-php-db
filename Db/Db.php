<?php

namespace Md\Db;

use PDO;
use Exception;

/** Class Db
 * DbContext Factory
 *
 * @author   MDevoldere 
 * @version  1.0.1
 * @access   public
 */
class Db 
{

    /** @var array[] $config Temp DbConfig Storage (deleted when connection is established) */
    static private array $config = [];

    /** @var IDbContext[] $context DbContext storage */
    static private array $context = [];

    /**
     * Register a new Db Context 
     * @param string $_context Context name
     * @param array $c an array contains db connection information
     * Example for SQLITE: 
     * [
     *  'db_type' => 'sqlite',
     *  'db_dsn' => 'sqlite:'/path/to/mydb.sqlite'
     * ]
     * * Example for MySQL: 
     * [
     *  'db_type' => 'mysql', 
     *  'db_dsn' => 'mysql:host=localhost;port=3306;dbname=mydb;charset=utf8mb4',
     *  'db_user' => 'root', // default: root
     *  'db_pass' => '' // default: empty password
     * ]
     */
    static public function register(string $_context, array $_c): void
    {
        if (!empty(self::$context[$_context])) {
            return;
        }

        if (empty($_c['db_type']) || empty($_c['db_dsn'])) {
            exit('Incomplete DbContext');
        }

        self::$config[$_context] = $_c;
    }

    /**
     * Get a named DbContext (must have been added via setDbContext)
     * @param string $_context Context name
     * @return DbContextInterface Context associated with Context name
     */
    static public function getContext(string $_context = 'default') : ?DbContextInterface
    {
        if(empty(self::$context[$_context])) {
            if(empty(self::$config[$_context])) {
                exit('Unknown DbContext');
            }
            self::$context[$_context] = self::createContext(self::$config[$_context]);
            self::$config[$_context] = null;
        }

        if(empty(self::$context[$_context])) {
            exit('DbContext Fatal Error');
        }

        return self::$context[$_context];
    }

    /**
     * Create a new DbContext using config array
     * @param $c db connection information
     * * Example for SQLITE: 
     * [
     *  'db_type' => 'sqlite',
     *  'db_dsn' => 'sqlite:'/path/to/mydb.sqlite'
     * ]
     * * Example for MySQL: 
     * [
     *  'db_type' => 'mysql', 
     *  'db_dsn' => 'mysql:host=localhost;port=3306;dbname=mydb;charset=utf8mb4',
     *  'db_user' => 'root', // default: root
     *  'db_pass' => '' // default: empty password
     * ]
     * @return DbContextInterface a new DbContextInterface object
     */
    static private function createContext(array $c): ?DbContextInterface
    {
        try {
            switch ($c['db_type']) {
                case 'mysql':
                    return new DbContext(new PDO(
                        $c['db_dsn'],
                        ($c['db_user'] ?? 'root'),
                        ($c['db_pass'] ?? ''),
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false
                        ]
                    ));
                    break;
                case 'sqlite':
                    $pdo = new PDO($c['db_dsn'], 'charset=utf8');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    $pdo->exec('pragma synchronous = off;');
                    return new DbContext($pdo);
                    break;
                default:
                    exit('Invalid DbContext Type');
                    break;
            }

        } catch (Exception $e) {
            exit('DbContext Connection Error');
        }
    }
}
