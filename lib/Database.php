<?php

class Database {
    public $host = DB_HOST;
    public $dbName = DB_NAME;
    public $dbUser = DB_USER;
    public $dbPass = DB_PASS;

    public $connection;
    public $error;
    public $error_no;

    public function __construct(){
        $this -> connectDB();
    }

    private function connectDB(){
        if(!isset($connection)){
            $this -> connection = new mysqli($this -> host, $this -> dbUser, $this -> dbPass, $this -> dbName);
        }

        if($this -> connection -> connect_errno){
            $this -> error = $this -> connection -> connect_error;
            $this -> error_no = $this -> connection -> connect_errno;
            die("Error Database Connection");
        }
    }

    //insert or update data
    public function insert($query, $markerDataTypes = "", $values = []){
        $preparedStatement = $this -> connection -> prepare($query);
        $preparedStatement -> bind_param($markerDataTypes, ...$values);
        return $preparedStatement->execute();
    }

    //select data
    public function select($query, $markerDataTypes = "", $values = []){
        $preparedStatement = $this -> connection -> prepare($query);
        $preparedStatement -> bind_param($markerDataTypes, ...$values);
        $result = $preparedStatement -> execute();
        if($result -> num_rows > 0){
            return $result;
        } else {
            return false;
        }
    }

    //delete data
    public function deleteWithId($table, $id ){
        $query = sprintf(
            "DELETE FROM %s WHERE id = %s", 
            $this -> connection -> real_escape_string($table),
            $this -> connection -> real_escape_string((string)$id)
        );
        $result = $this -> connection -> query($query);
        if($result == false){
            echo "Error cannot delete id '" . $id . "' from table '" . $table . "'.";
            return false;
        } else {
            return true;
        }
    }
}