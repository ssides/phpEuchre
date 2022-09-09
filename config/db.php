<?php 

    if(!isset($_SESSION)) {
        session_start();
    }

    $hostname = 'localhost';
    $username = 'dbUser';
    $password = 'JerryMaker34#$';
    $dbname = 'Euchre';
    
    $connection = mysqli_connect($hostname, $username, $password, $dbname) or die("Database connection not established.")

?>