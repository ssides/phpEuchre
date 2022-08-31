<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>PHP DB Test</title>
    <!-- jQuery + Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>

<?php 

    $hostname = "localhost";
    $username = "dbUser";
    $password = "PwdPwd123";
    $dbname = "Euchre";
    
    $conn = mysqli_connect($hostname, $username, $password, $dbname) or die("Database connection not established.")

?>
    <div class="App">
      <h3>Hello World!</h3>
      <?php         
        $sql = "select `ID`,`Value` from `HelloWorld` ";
        $result = mysqli_query($conn, $sql);
        echo "Row count " . mysqli_num_rows($result);
        if(mysqli_num_rows($result) > 0){
          //iterating only if the table is not empty
          while($row = mysqli_fetch_array($result)) {
            echo $row['ID'] . " " . $row['Value'] . '</br>';
          }
        }
      ?>
    </div>

</body>

</html>