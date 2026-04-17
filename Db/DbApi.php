<?php

namespace Md\Db;

use PDO;
use PdoStatement;
use Exception;
use PDOException;

/** Class DbContext
 *
 * @author   MDevoldere 
 * @version  1.1.0
 * @access   public
 */
class DbApi
{
    public readonly PDO $pdo;

    private string $table;

    /**
     * DbApi Constructor
     * @param PDO $_pdo PDO Connection
     * @param string $_table the table name
     */
    public function __construct(PDO $_pdo, string $_table) 
    {
        $this->pdo = $_pdo;
        $this->table = $_table;
    }  

    /** Set the table to use in the next query
     * @param string $_table the table name
     * @return self
     */
    public function setTable(string $_table) : self
    {
        $this->table = $_table;
        return $this;
    }

    /** Executes a read request and returns 1 row identified by its id
     * @param int item identifier
     * @return array  result set or empty array 
     */
    public function fetch(int $_id): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1;");
            return ($stmt->execute([':id' => $_id]) ? $stmt->fetch(PDO::FETCH_ASSOC) : []);
        } catch (Exception $e) {
            exit('DbFetch Error' . $e->getMessage());
        }
    }

    /** Execute read request and return all rows  
     * @return array result set or empty array 
     */
    public function fetchAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM " . $this->table . ";");
            return (($stmt !== false) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []);
        } catch (Exception $e) {
            exit('DbFetch Error' . $e->getMessage());
        }
    }

    /** Executes a parameterized write request and returns the number of rows affected
     * @param string $_query SQL query to execute
     * @param array $_values the values associated with the query parameters
     * @return int number of rows affected by the query
     */
    public function exec(string $_query, array $_values = []): int
    {
        try {
            $stmt = $this->pdo->prepare($_query);

            if ($stmt->execute($_values)) {
                $r = $stmt->rowCount();
                $stmt->closeCursor();
                return $r;
            }
            return 0;
        } catch (Exception $e) {
            exit('DbExec Error : ' . $e->getMessage());
        }
    }

    /** Add data to specific table
     * @param array $_values data to insert (must match to table structure)
     * @return int number of rows affected
     */
    public function insert(array $_values): int
    {
        $cols = \array_keys($_values);
        $vals = (':' . \implode(', :', $cols));
        $cols = \implode(',', $cols);

        return $this->exec("INSERT INTO " . $this->table . " (" . $cols . ") VALUES (" . $vals . ");", $_values);
    }

    /** Update a row in specific table
     * @param string $_pk the primary key name
     * @param array $_values The array of values corresponding to the current table. Must contain the identifier of the row to update.
     * @return int number of rows affected
     */
    public function update(array $_values): int
    {
        $id = null;
        $cols = [];

        foreach ($_values as $k => $v) {
            if($k !== $_pk) {
                $cols[$k] = ($k . '=:' . $k);
            }
            else {
                $id = $v;
            }            
        }
        
        if($id !== null) {
            return $this->exec("UPDATE " . $this->table  . " SET " . \implode(', ', $cols) . " WHERE " . $_pk  . "=:" . $_pk  . " LIMIT 1;", $_values);
        }

        return 0;
    }

    /** Delete a row in specific table
     * @param string $_table the table
     * @param string $_pk the primary key name
     * @param string $_id row identifier
     * @return int number of rows affected
     */
    public function delete(string $_table, string $_pk, string $_id): int
    {
        return $this->exec("DELETE FROM " . $_table  . " WHERE " . $_pk  . "=:id LIMIT 1;", [':id' => $_id]);
    }
}
