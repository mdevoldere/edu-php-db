<?php 

namespace Md\Db;

/** Interface DbContextInterface
 *
 * @author   MDevoldere 
 * @version  1.0.0
 * @access   public
 */
interface DbContextInterface
{
    /** Performs a simple read request 
     * @param string $_query SQL query to execute 
     * @param bool $_all true = return all rows. false = return first row
     * @return array result set or empty array 
     */
    public function query(string $_query, bool $_all = false): array;

    /** Executes a parameterized read request
     * @param string $_query SQL query to execute
     * @param array $_values the values associated with the query parameters
     * @param bool $_all true = return all rows. false = return first row
     * @return array result set or empty array 
     */
    public function fetch(string $_query, array $_values = [], bool $_all = false): array;

    /** Execute a parameterized read request and return all rows  
     * @param string $_query SQL query to execute
     * @param array $_values the values associated with the query parameters
     * @return array result set or empty array 
     */
    public function fetchAll(string $_query, array $_values = []): array;

    /** Executes a parameterized write request and returns the number of rows affected
     * @param string $_query SQL query to prepare and execute
     * @param array $_values the values associated with the query parameters
     * @return int number of rows affected by the query
     */
    public function exec(string $_query, array $_values = []): int;

    /** Add data to specific table
     * @param string $_table the table
     * @param array $_values data to insert (must match to table structure)
     * @return int number of rows affected
     */
    public function insert(string $_table, array $_values): int;

    /** Update a row in specific table
     * @param string $_table the table
     * @param string $_pk the primary key name
     * @param array $_values The array of values corresponding to the current table. Must contain the identifier of the row to update.
     * @return int number of rows affected
     */
    public function update(string $_table, string $_pk, array $_values): int;

    /** Delete a row in specific table
     * @param string $_table the table
     * @param string $_pk the primary key name
     * @param string $_id row identifier
     * @return int number of rows affected
     */
    public function delete(string $_table, string $_pk, string $_id): int;
}
