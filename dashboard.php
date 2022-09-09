<?php include('authorize.php'); ?>
<?php include('controllers/dashboard.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="./content/css/site.css">
  <title>PHP User Registration System Example</title>
  <!-- jQuery + Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>
  <!-- Header -->
  <?php include('header.php'); ?>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block">
        <?php echo $sqlErr; ?>
        <form action="" method="post">
          <table>
            <tr>
              <td>
                <button type="submit" name="organize" id="organize" class="btn btn-outline-primary btn-md btn-block">Start a Game</button>
              </td>
              <td>&nbsp;&nbsp;You will be the organizer of the game.</td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
<?php include('content/js/dashboard.php') ?>
</body>

</html>