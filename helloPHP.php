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
  <style>
    .flip-container-p {
      perspective: 1000px;
    }
    .flip-container-p.hover .flipper-p {
      transform: rotateY(180deg);
    }
    .flip-container-p,
    .back-p,
    .front-p {
      width: 50px;
      height: 70px;
    }
    .flipper-p {
      transition: 0.5s;
      transform-style: preserve-3d;
      position: relative;
    }
    .back-p,
    .front-p {
      backface-visibility: hidden;
      position: absolute;
      top: 0;
      left: 0;
    }
    .back-p {
      z-index: 2;
      transform: rotateY(0deg);
    }
    .front-p {
      transform: rotateY(180deg);
    }
    
    .flip-container-l {
      perspective: 1000px;
    }
    .flip-container-l.hover .flipper-l {
      transform: rotateX(180deg);
    }
    .flip-container-l,
    .back-l,
    .front-l {
      width: 70px;
      height: 50px;
    }
    .flipper-l {
      transition: 0.5s;
      transform-style: preserve-3d;
      position: relative;
    }
    .back-l,
    .front-l {
      backface-visibility: hidden;
      position: absolute;
      top: 0;
      left: 0;
    }
    .back-l {
      z-index: 2;
      transform:  rotateX(0deg) ;
    }
    .front-l {
      transform: rotateX(180deg) ;
    }

  </style>
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
      echo 'turn up:'.substr($cards,60,3).'<br>';
      
      function auto_version($file)
      {
        if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
          return $file;

        $mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
        return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
      }

      echo auto_version('/content/css/site.css');
      
      ?>
    <?php
    ?>
    <div class="flip-container-p">
      <div class="flipper-p">
        <div class="back-p">
          <img src="http://localhost:8080/content/images/cards/cardback.png"  style="width:50px;height:70px;">
        </div>
        <div class="front-p">
          <img src="http://localhost:8080/content/images/cards/AS.png"  style="width:50px;height:70px;">
        </div>
      </div>
    </div>
    
    <div  id="as" class="flip-container-l"">
      <div class="flipper-l">
        <div class="back-l">
          <img src="http://localhost:8080/content/images/cards/cardback.png"  style="width:50px;height:70px; rotate: 90deg;">
        </div>
        <div class="front-l">
          <img src="http://localhost:8080/content/images/cards/AC.png"  style="width:50px;height:70px; rotate: 90deg; transform: translate(50px,0);">
        </div>
      </div>
    </div>
  
    <div id="ah" class="flip-container-l"">
      <div class="flipper-l">
        <div class="back-l">
          <img src="http://localhost:8080/content/images/cards/cardback.png"  style="width:50px;height:70px; rotate: -90deg;">
        </div>
        <div class="front-l">
          <img src="http://localhost:8080/content/images/cards/AH.png"  style="width:50px;height:70px; rotate: -90deg; transform: translate(-50px,0); ">
        </div>
      </div>
    </div>
  </div>
  

  
<br><br><br>
  <button id="play">Play a card</button>
  <button id="play-l">Play other card</button>
  <button id="play-k">Play the card</button>
<script type="text/javascript">

  $(function(){
    // $(document).on("click", ".flip-container-p", function () {
        // $(this).toggleClass('hover');
    // });
    $('#play').click(function(){
      console.log('button click');
      $('.flip-container-p').toggleClass('hover');
    });
    $('#play-l').click(function(){
      console.log('button click');
      $('#as').toggleClass('hover');
    });
    $('#play-k').click(function(){
      console.log('button click');
      $('#ah').toggleClass('hover');
    });
  });
  
</script>
</body>

</html>