<?php 

namespace Md\Db;

use Md\Db\Exceptions\BadContextException;

use function basename;

/** Class RepositoryFile
 *
 * @author   MDevoldere 
 * @version  1.1.0
 * @access   public
 */
class RepositoryFile implements RepositoryInterface
{
    /** @var string $table the table name */
    public string $table;

    /** @var string $pk the table primary key name */
    public string $pk;

    /** @var string $file the file containing data */
    public readonly string $file;

    private array $db;

    /**
     * Initialize a new Repository
     * @param string $_table the table name
     * @param string $pk the table primary key name
     * @param string $_dbContext the db context name
     */
    public function __construct(string $_table, string $_pk, string $_dbContext = 'default')
    {
        if(!is_file($_dbContext)) {
            throw new BadContextException('File not found ('.$_dbContext.')');
        }

        $this->table = $_table;
        $this->pk = $_pk;
        $this->file = $_dbContext;
        $ext = pathinfo($this->file, PATHINFO_EXTENSION);
        $this->db = ($ext === 'json' 
                    ? json_decode(file_get_contents($this->file), true) 
                    : (require $this->file));
    }

    /**
     * Get total rows number
     * @return int total rows number
     */
    public function count(): int
    {
        return count($this->db);
    }

    /**
     * Check if identifier exists in table
     * @param string $_id identifier to search
     * @return bool true if exists, false if not found 
     */
    public function exists($_id): bool
    {
        foreach($this->db as $v) {
            if($v[$this->pk] ?? '' === $_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all table rows
     * @return array all table rows
     */
    public function getAll(): array
    {
        return $this->db;
    }

    /**
     * Get by specific column value
     * @param string $_col the column in table
     * @param string $_value the value to search in column
     * @param bool $_all true to get all result set, false to get the first row found
     * @return array found row(s) or empty array
     */
    public function getBy(string $_col, string $_value, bool $_all = false) : array
    {
        $r = [];
        foreach($this->db as $v) {
            if($v[$_col] ?? '' === $_value) {
                $r[] = $v;
            }
        }
        return $r;
    }

    /**
     * Get row by identifier
     * @param string $_id identifier to search
     * @return array found row or empty array
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
        return $this->db[array_rand($this->db)];
    }

    /**
     * Clean & Validate array structure matching current table. clean data if necessary
     * @param array $_input the array to validate
     * @return bool true if array is valid, false otherwise 
     */
    public function validate(array &$_input): bool
    {
        return false;
    }

    /**
     * Add row to current table
     * @param array data corresponding to current table
     * @return bool true if row added, false otherwise
     */
    public function add(array $_input) : bool
    {
        return false;
    }

    /**
     * Update a row in current table
     * @param array $_input data to update
     * @return bool true if row updated, false otherwise
     */
    public function update(array $_input): bool
    {
        return false;
    }

    /**
     * Delete a row in table
     * @param string $_id row identifier
     * @return bool true if deleted, false otherwise
     */
    public function delete($_id): bool
    {   
        return false;
    }
}
