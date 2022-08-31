<?php

    // Enable us to use Headers
    ob_start();

    // Set sessions
    if(!isset($_SESSION)) {
        session_start();
    }

    global $hostname,$username,$password,$dbname
    $hostname = "localhost";
    $username = "root";
    $password = "3pTRrTjgLd6F1hnr";
    $dbname = "Euchre";
    
    $connection = mysqli_connect($hostname, $username, $password, $dbname) or die("Database connection not established.")

?>