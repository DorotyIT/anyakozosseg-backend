<?php
    require("cors.php");
    header("Content-Type: application/json; charset=utf-8");
    define("DBHOST", "localhost");
    define("DBUSER", "root");
    define("DBPASS", "");
    define("DBNAME", "mother_community");
    $connection = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
    $connection->set_charset("utf8mb4");
    if ($connection -> connect_errno) {
        printf("Connection failed!");
        exit();
    }
?>