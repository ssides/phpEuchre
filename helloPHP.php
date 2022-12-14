<?php 
  include_once('config/config.php');
  ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version  ?>">
  <title>Hello PHP</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  
</head>

<body>

  <div class="helloPadding">
    <p>Server Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?></p>
    <?php echo $_SESSION['gameID'].'<br>'; ?>
    <?php 
      echo "Today is " . date("Y-m-d").'<br>' ; 
      echo 'The number for today is '.mt_rand(1,500).'<br>';
    
      $now = new DateTime();
      $now->sub(new DateInterval('P6M'));
      $cutoffDate = $now->format('Y-m-d');

      echo 'Cutoff Date: '.$cutoffDate.'<br>';

      
      function auto_version($file)
      {
        if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
          return $file;

        $mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
        return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
      }

      echo auto_version('/content/css/site.css');
      
      ?>
  
<br><br><br>
  
<!-- Modal -->
<div class="modal fade" id="bidModal" tabindex="-1" aria-labelledby="bidModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Choose Trump</h1>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Pass</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

  <button id="play-k">Show the modal</button>
  
  
<script type="text/javascript">

  $(function(){
    
    var bidModal = new bootstrap.Modal($('#bidModal'));
    
    $('#play-k').click(function(){
      console.log('button click');
      bidModal.show();
    });
  });
  
</script>
</body>

</html>