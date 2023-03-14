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
  <link rel="stylesheet" href="<?php echo './content/css/testStyles.css?v='.$version  ?>">
  <title>Hello PHP</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  
</head>

<body>

  <div class="helloPadding">
    <?php 
      
      $test = array();
      $test['Title'] = "It tolerates prev deals empty - expecting 'is set'";
      // C# cuts off the trailing space after the last card.
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "1H AH JC JH KH 1C 9H JD KC QD 1D 9C 9D JS KD 9S AS QC QH QS 1S KS AC AD");
      $test['PrevArray'] = array();
      runTest($test);
      
      $test = array();
      $test['Title'] = "It recognizes one previous deal no match - expecting 'is set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "1H AH JC JH KH 1C 9H JD KC QD 1D 9C 9D JS KD 9S AS QC QH QS 1S KS AC AD");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "1C AD JD KC KS 9H 9S JH QD QH 1S 9C AS JC QC 1D 1H 9D AC KD JS QS KH AH");
      runTest($test);
      
      $test = array();
      $test['Title'] = "Organizer matches last deal - expecting 'is not set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "1C 1D 1H 1S JC 9D AC KC KD QD 9C 9H AS JH KS 9S AH JS QC QS QH KH JD AD");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "1C 1D 1H 1S JC 9D AC JD JH QD 9S AH AS QC QS AD KC KD KH QH JS KS 9C 9H");
      runTest($test);
      
      $test = array();
      $test['Title'] = "Organizer matches second to last deal - expecting 'is not set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "1C 1D 1H 1S JC 9D AC KC KD QD 9C 9H AS JH KS 9S AH JS QC QS QH KH JD AD");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "1H AH JC JH KH 1C 9H JD KC QD 1D 9C 9D JS KD 9S AS QC QH QS 1S KS AC AD");
      array_push($test['PrevArray'],                                                       "1C 1D 1H 1S JC 9D AC JD JH QD 9S AH AS QC QS AD KC KD KH QH JS KS 9C 9H");
      runTest($test);
      
      $test = array();
      $test['Title'] = "Organizer matches third to last deal if we want that - expecting 'is not set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "1C 1D 1H 1S JC 9D AC KC KD QD 9C 9H AS JH KS 9S AH JS QC QS QH KH JD AD");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "1S 9S AC AS QS 1C 9C KD KS QC 1H AH JH KC QH 9H AD JS KH QD JC 9D 1D JD");
      array_push($test['PrevArray'],                                                       "1H AH JC JH KH 1C 9H JD KC QD 1D 9C 9D JS KD 9S AS QC QH QS 1S KS AC AD");
      array_push($test['PrevArray'],                                                       "1C 1D 1H 1S JC 9D AC JD JH QD 9S AH AS QC QS AD KC KD KH QH JS KS 9C 9H");
      runTest($test);
      
      $test = array();
      $test['Title'] = "Three deals no match - expecting 'is set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "1H AH JC JH KH 1C 9H JD KC QD 1D 9C 9D JS KD 9S AS QC QH QS 1S KS AC AD");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "9C 9H KC KS QH 9S AD JC KH QS 1D 1H 9D JH KD AH AS JS QC QD 1S 1C AC JD");
      array_push($test['PrevArray'],                                                       "1S 9D AC JH KS 1H 9C 9H 9S QH JD KC KH QC QS 1C AD AS JC JS AH 1D QD KD");
      array_push($test['PrevArray'],                                                       "9H AH KH KS QS 9C 9D AS JH QH 1H 9S AC JC JS 1S AD KC KD QD 1C QC 1D JD");
      runTest($test);

      $test = array();
      $test['Title'] = "Left match second to last deal - expecting 'is not set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "AH JC KC KD QD 9C JD JH JS QH 1C 1D 1H 1S AD 9D 9H 9S AC KS QS AS QC KH");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "1H AH JC JH KH 1C 9H JD KC QD 1D 9C 9D JS KD 9S AS QC QH QS 1S KS AC AD");
      array_push($test['PrevArray'],                                                       "9D 9S JC KS QD 9C AS KC KD QS 1C 1D 1H 1S AD 9H AH JD JS QC AC JH QH KH");
      runTest($test);

      $test = array();
      $test['Title'] = "Organizer matches last deal, Left matches second to last deal - expecting 'is not set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "AH JC KC KD QD 9C JD JH JS QH 1C 1D 1H 1S AD 9D 9H 9S AC KS QS AS QC KH");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "AH JC KC KD QD 1C 1D 1H AS QC 9H 9S KS QH QS 9C 9D AC JH KH 1S JS JD AD");
      array_push($test['PrevArray'],                                                       "9D 9S JC KS QD 9C AS KC KD QS 1C 1D 1H 1S AD 9H AH JD JS QC AC JH QH KH");
      runTest($test);

      $test = array();
      $test['Title'] = "Partner matches last deal - expecting 'is not set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "JS KC KH KS QD 1C 1D 1H 1S AD 9D 9H AC JC QS 9C 9S AS KD QH QC JH AH JD");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "9D JD JH KC QS 1C 1D 1H 1S AD 9C 9H AH KH QD 9S AC AS JC QC JS KS QH KD");
      array_push($test['PrevArray'],                                                       "9D 9S JC KS QD 9C AS KC KD QS 1C 1D 1H 1S AD 9H AH JD JS QC AC JH QH KH");
      runTest($test);

      $test = array();
      $test['Title'] = "Right matches last deal - expecting 'is not set'";
      $test['Deal'] = array("DealID" => "00128594-37C5-4629-91C1-B0BB8668E28E", "Cards" => "1S 9C AH JD JS 1H 9S AC AD JC 1D 9H AS JH KS KH QC QD QH QS 1C KC 9D KD");
      $test['PrevArray'] = array();
      array_push($test['PrevArray'],                                                       "1H 9H AC JD JH 1C 9C AD JS KS 1S 9S AS KC KD KH QC QD QH QS JC AH 9D 1D");
      array_push($test['PrevArray'],                                                       "9D 9S JC KS QD 9C AS KC KD QS 1C 1D 1H 1S AD 9H AH JD JS QC AC JH QH KH");
      runTest($test);

      // --- test functions --- 
      function runTest($test) {
        checkTest($test);
        
        $deal = checkUnique($test['Deal'], $test['PrevArray']);
        $set = isset($deal['DealID']) ? 'is set' : ' is not set';
        echo '<p><span class="testTitle">'.$test['Title']."</span><br>";
        echo "'DealID' isset after calling  checkUnique(): ".$set."<br></p>";
      }
      
      function checkTest($test) {
        echo '<p class="checkTest">Checking test "'.$test['Title'].'"<br>';
        if (count($test['PrevArray']) == 0) {
          echo "&nbsp;&nbsp;PrevArray is empty.<br>";
        } else {
          $dealt = $test['Deal']['Cards'];
          foreach ($test['PrevArray'] as $c) {
            $o = getOCards($dealt) == getOCards($c) ? 'organizer  ' : '';
            $p = getPCards($dealt) == getPCards($c) ? 'partner  ' : '';
            $l = getLCards($dealt) == getLCards($c) ? 'left  ' : '';
            $r = getRCards($dealt) == getRCards($c) ? 'right ' : '';
            echo "matches: $o $p $l $r <br>";
          }
        }
        echo '</p>';
      }
      
      // --- copy of production code functions  --- 

      function checkUnique($deal, $prevCards) {
        $result = $deal;
        $dealt = $deal['Cards'];
        
        foreach ($prevCards as $c) {
          if (getOCards($dealt) == getOCards($c)
            || getPCards($dealt) == getPCards($c)
            || getLCards($dealt) == getLCards($c)
            || getRCards($dealt) == getRCards($c)
          ) {
            unset($result['DealID']);
            unset($result['Cards']);
          }
        }

        return $result;
      }
      
      function getOCards($cards) {
        return substr($cards,0,15);
      }
      
      function getPCards($cards) {
        return substr($cards,15,15);
      }
      
      function getLCards($cards) {
        return substr($cards,30,15);
      }
      
      function getRCards($cards) {
        return substr($cards,45,15);
      }
      
    ?>
  
  
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