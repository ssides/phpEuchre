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
  <link rel="stylesheet" href="<?php echo './content/css/site.css?r='.mt_rand() ?>">
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
      $c = array();
      $c['Index'] = 0;
      // $c['Index'] += 3;
      echo 'the array value: '.$c['Index'].'<br>';
      $s = 'This is a string';
      echo 'This is the first character of the string: '.$s[0].'<br>';
      $c['Index'] = 3;
      $c['Cards'] = '1H JD JS KC QC 1D 9H AS QD';
      $cards = $c['Cards'];
      if ($c['Cards'][$c['Index']] == 'J') {
        echo "Jack at index {$c['Index']}: {$c['Cards'][0]} {$c['Cards'][$c['Index']]}<br>";
      } else {
        echo "No jack at index {$c['Index']}: {$c['Cards'][0]} {$c['Cards'][$c['Index']]}<br>";
      }
      if ($cards[$c['Index']] == 'J') {
        echo "Jack at index {$c['Index']}: {$cards[0]} {$cards[$c['Index']]}<br>";
      } else {
        echo "No jack at index {$c['Index']}: {$cards[0]} {$cards[$c['Index']]}<br>";
      }
      
      $deal = array();
      if (isset($deal['DealID']))
        echo 'dealid is set: <br>';
      else
        echo 'dealid is not set: <br>';
      
      $cards = '1C KH KS QC QH 1D 1S 9C 9S KD 1H 9D AS JC QD 9H AC AD KC QS JS JH AH JD ';
      //  POS   0123456789012345678901234567890123456789012345678901234567890
      //        0         1         2         3         4         5         6
      //  LEN   123456789012345
      echo 'O: '.substr($cards,0,15) .'<br>';
      echo 'P: '.substr($cards,15,15).'<br>';
      echo 'L: '.substr($cards,30,15).'<br>';
      echo 'R: '.substr($cards,45,15).'<br>';
      echo 'turn up: '.substr($cards,60,3).'<br>';
      
      ?> <div style="font-family: Courier, monospace; font-weight: bold">  <?php
      $c = substr($cards,0,15);
      echo '"'.substr($c,0,3).'"'
      ?> </div>  <?php
      
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