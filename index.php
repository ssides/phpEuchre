<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>PHP Hello World</title>
    <!-- jQuery + Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>

<?php 

    $hostname = "35.229.118.28";
    $username = "dbUser";
    $password = "$Password1";
    $dbname = "Euchre";
    
    $connection = mysqli_connect($hostname, $username, $password, $dbname) or die("Database connection not established.")

?>
    <div class="App">
      <h3>Hello World!</h3>
    </div>

</body>

</html>