<?php 

namespace Md\Db;

use function basename;

class Repository implements RepositoryInterface
{
    /** @var string $table the table name */
    public string $table;

    /** @var string $pk the table primary key name */
    public string $pk;

    /** @var DbContextInterface $db the dbcontext object */
    protected DbContextInterface $db;

    /**
     * Initialize a new Repository
     * @param string $_table the table name
     * @param string $pk the table primary key name
     * @param string $_dbContext the db context name
     */
    public function __construct(string $_table, string $_pk, string $_dbContext = 'default')
    {
        $this->table = $_table;
        $this->pk = $_pk;
        $this->db = Db::getContext($_dbContext);

        if(empty($this->db)) {
            exit('Repository Error 1 ('.$_dbContext.')');
        }
    }

    /**
     * Get total rows number
     * @return int total rows number
     */
    public function count(): int
    {
        return $this->db->query(("SELECT COUNT(*) as nb FROM " . $this->table . ";"), false)['nb'];
    }

    /**
     * Check if identifier exists in table
     * @param string $_id identifier to search
     * @return bool true if exists, false if not found 
     */
    public function exists($_id): bool
    {
        return $this->db->fetch("SELECT COUNT(*) as nb FROM " . $this->table . " WHERE " . $this->pk . "=:cond;", [':cond' => $_id], false)['nb'] > 0;
    }

    /**
     * Get all table rows
     * @return array all table rows
     */
    public function getAll(): array
    {
        return $this->db->query(("SELECT * FROM " . $this->table . ";"), true);
    }

    /**
     * Get by specific column value
     * @param string $_col the column in table
     * @param string $_value the value to search in column
     * @param bool $_all true to get all result set, false to get the first row found
     */
    public function getBy(string $_col, string $_value, bool $_all = false) : array
    {
        return $this->db->fetch("SELECT * FROM " . $this->table . " WHERE " . basename($_col) . "=:cond;", [':cond' => $_value], $_all);
    }

    /**
     * Get row by identifier
     * @param string $_id identifier to search
     * @return array row found or empty array
     */
    public function getById($_id): array
    {
        return $this->getBy($this->pk, $_id, false);
    }

    /**
     * Get a random row
     */
    public function getRandom(): array
    {
        return $this->db->query("SELECT * FROM " . $this->table . " ORDER BY RAND() ASC LIMIT 1;");
    }

    /**
     * Clean & Validate array structure matching current table. clean data if necessary
     * @param array $_input the array to validate
     * @return bool true if array is valid, false if invalid 
     */
    public function validate(array &$_input): bool
    {
        return true;
        /*$m = $this->getFirst();
        return empty(array_diff_key($m, $_input));*/
    }

    /**
     * Add row to current table
     * @param array data corresponding to current table
     * @return bool true if row added, false otherwise
     */
    public function add(array $_input) : bool
    {
        if($this->validate($_input)) {
            return $this->db->insert($this->table, $_input) > 0;
        }
        return false;
    }

    /**
     * Update a row in current table
     * @param string $_id row identifier
     * @param array $_input data to update
     * @return bool true if row updated, false otherwise
     */
    public function update($_id, array $_input): bool
    {
        if($this->validate($_input)) {
            return $this->db->update($this->table, $this->pk, $_input) > 0;
        }
        return false;
    }

    /**
     * Delete a row in table
     * @param string $_id row identifier
     * @return bool true if deleted, false otherwise
     */
    public function delete($_id): bool
    {   
        if($this->validate($_input)) {
            return $this->db->delete($this->table, $this->pk, $_id) > 0;
        }
        return false;
    }

    
}