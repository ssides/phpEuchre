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
        if($result->num_rows>0){
            //iterating only if the table is not empty
            while ($row = $result->fetch_object()) {
                //Here you are iterating each row of the database
                foreach ($row as $r){
                    //Here you are iterating each column as $r
                    //and (trying) adding it again to the $row array
                    echo $row['ID'] . " " . $row['Value'] . '</br>';
                }
            }
        }
 ?>
    </div>

</body>

</html>