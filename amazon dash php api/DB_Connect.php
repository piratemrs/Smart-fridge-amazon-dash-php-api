<?php


class DB_Connect {


    // constructor
    function __construct() {

    }

    // destructor
    function __destruct() {
        // $this->close();
    }

    // Connecting to database
    public function connect() {
        // connecting to mysql

        $con = mysqli_connect("localhost", "root", "","data") or die(mysql_error());
        // selecting database
       // mysql_select_db("data") or die(mysql_error());

        // return database handler
        return $con;
    }

    // Closing database connection
    public function close() {
        mysql_close();
    }

}

?>
