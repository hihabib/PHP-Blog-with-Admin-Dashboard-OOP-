<?php
include_once('../config/config.php');
/**
 * Class Database
 *
 * Represents a database connection and provides methods for interacting with the database.
 */
class Database {
    /**
     * @var string The database host.
     */
    public $host = DB_HOST;

    /**
     * @var string The database name.
     */
    public $dbName = DB_NAME;

    /**
     * @var string The database user.
     */
    public $dbUser = DB_USER;

    /**
     * @var string The database password.
     */
    public $dbPass = DB_PASS;

    /**
     * @var mysqli|null The database connection.
     */
    public $connection;

    /**
     * @var string|null The last database error message.
     */
    public $error;

    /**
     * @var int|null The last database error number.
     */
    public $error_no;

    /**
     * Database constructor.
     * Initializes a database connection.
     */
    public function __construct(){
        $this->connectDB();
    }

    /**
     * Establishes a database connection.
     *
     * @return void
     */
    private function connectDB(){
        if (!isset($this->connection)) {
            $this->connection = new mysqli($this->host, $this->dbUser, $this->dbPass, $this->dbName);
        }

        if ($this->connection->connect_errno) {
            $this->error = $this->connection->connect_error;
            $this->error_no = $this->connection->connect_errno;
            die("Error: Database Connection Failed");
        }
    }

    /**
     * Insert or update data into the database.
     *
     * @param string $query The SQL query to execute.
     * @param string $markerDataTypes The data types for prepared statement markers.
     * @param array $values The values to bind to the prepared statement markers.
     *
     * @return bool True if the query execution was successful, false otherwise.
     */
    public function insert($query, $markerDataTypes = "", $values = []){
        $preparedStatement = $this->connection->prepare($query);
        //skip parameter binding if the $values has no element
        if(count($values) > 0){
            $preparedStatement->bind_param($markerDataTypes, ...$values);
        }
        return $preparedStatement->execute();
    }

    /**
     * Selects data from the database.
     *
     * @param string $query The SQL query to execute.
     * @param string $markerDataTypes The data types for prepared statement markers.
     * @param array $values The values to bind to the prepared statement markers.
     *
     * @return mysqli_result|false A mysqli_result object if the query is successful and results are found, or false otherwise.
     */
    public function select($query, $markerDataTypes = "", $values = []){
        $preparedStatement = $this->connection->prepare($query);
        //skip parameter binding if the $values has no element
        if(count($values) > 0){
            $preparedStatement->bind_param($markerDataTypes, ...$values);
        }
        $execution = $preparedStatement->execute();
        if ($execution) {
            $result = $preparedStatement->get_result();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Deletes data from the database by ID.
     *
     * @param string $table The name of the table.
     * @param int|string $id The ID of the record to delete.
     *
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteWithId($table, $id ){
        $query = sprintf(
            "DELETE FROM %s WHERE id = %s", 
            $this->connection->real_escape_string($table),
            $this->connection->real_escape_string((string)$id)
        );
        $result = $this->connection->query($query);
        if (!$result) {
            echo "Error: Unable to delete record with ID '" . $id . "' from table '" . $table . "'.";
            return false;
        } else {
            return true;
        }
    }
}

