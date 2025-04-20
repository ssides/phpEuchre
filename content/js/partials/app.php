<script type="text/javascript">

  app = {};
  
  app.times = {
    firstJackTime: 600,
    gameTime: 1000
  };
  
  app.positions = 'OPLR';
  app.appURL = '<?php echo $appUrl; ?>';

  app.apiPostData = { <?php echo 'r:'."'{$$a['r']}'"
                   .",gameID:'{$_SESSION['gameID']}'"   ?>  };

  app.getCardURL = function(cardID){
      return (cardID ? app.appURL + 'content/images/cards<?php echo $cardFaces; ?>/' + cardID + '.png' : '');
    };
  
  app.gameControllerLog = <?php echo $gameControllerLog ? 'true' : 'false'; ?>;
  
  app.clearTableDelay = <?php echo $clearTableDelay; ?>;
  
  app.gameEnded = function () {
      window.location.href = 'dashboard.php';
    }
    
  app.replayReportGameEnd = function () {
    window.location.href = 'reports/replay-game-end.php';
  }

  app.deadSoundStartTime = Date.now();
  app.soundQueue = [];
  app.soundPlaying = false;
  
  app.sounds = {
    "C": '<?php echo $appUrl.$audioDir."c.mp3"; ?>',
    "D": '<?php echo $appUrl.$audioDir."d.mp3"; ?>',
    "H": '<?php echo $appUrl.$audioDir."h.mp3"; ?>',
    "S": '<?php echo $appUrl.$audioDir."s.mp3"; ?>',
    "D1C": '<?php echo $appUrl.$audioDir."D1C.mp3"; ?>',
    "D1D": '<?php echo $appUrl.$audioDir."D1D.mp3"; ?>',
    "D1H": '<?php echo $appUrl.$audioDir."D1H.mp3"; ?>',
    "D1S": '<?php echo $appUrl.$audioDir."D1S.mp3"; ?>',
    "D9C": '<?php echo $appUrl.$audioDir."D9C.mp3"; ?>',
    "D9D": '<?php echo $appUrl.$audioDir."D9D.mp3"; ?>',
    "D9H": '<?php echo $appUrl.$audioDir."D9H.mp3"; ?>',
    "D9S": '<?php echo $appUrl.$audioDir."D9S.mp3"; ?>',
    "DAC": '<?php echo $appUrl.$audioDir."DAC.mp3"; ?>',
    "DAD": '<?php echo $appUrl.$audioDir."DAD.mp3"; ?>',
    "DAH": '<?php echo $appUrl.$audioDir."DAH.mp3"; ?>',
    "DAS": '<?php echo $appUrl.$audioDir."DAS.mp3"; ?>',
    "DJC": '<?php echo $appUrl.$audioDir."DJC.mp3"; ?>',
    "DJD": '<?php echo $appUrl.$audioDir."DJD.mp3"; ?>',
    "DJH": '<?php echo $appUrl.$audioDir."DJH.mp3"; ?>',
    "DJS": '<?php echo $appUrl.$audioDir."DJS.mp3"; ?>',
    "DKC": '<?php echo $appUrl.$audioDir."DKC.mp3"; ?>',
    "DKD": '<?php echo $appUrl.$audioDir."DKD.mp3"; ?>',
    "DKH": '<?php echo $appUrl.$audioDir."DKH.mp3"; ?>',
    "DKS": '<?php echo $appUrl.$audioDir."DKS.mp3"; ?>',
    "DQC": '<?php echo $appUrl.$audioDir."DQC.mp3"; ?>',
    "DQD": '<?php echo $appUrl.$audioDir."DQD.mp3"; ?>',
    "DQH": '<?php echo $appUrl.$audioDir."DQH.mp3"; ?>',
    "DQS": '<?php echo $appUrl.$audioDir."DQS.mp3"; ?>',
    "K1C": '<?php echo $appUrl.$audioDir."K1C.mp3"; ?>',
    "K1D": '<?php echo $appUrl.$audioDir."K1D.mp3"; ?>',
    "K1H": '<?php echo $appUrl.$audioDir."K1H.mp3"; ?>',
    "K1S": '<?php echo $appUrl.$audioDir."K1S.mp3"; ?>',
    "K9C": '<?php echo $appUrl.$audioDir."K9C.mp3"; ?>',
    "K9D": '<?php echo $appUrl.$audioDir."K9D.mp3"; ?>',
    "K9H": '<?php echo $appUrl.$audioDir."K9H.mp3"; ?>',
    "K9S": '<?php echo $appUrl.$audioDir."K9S.mp3"; ?>',
    "KAC": '<?php echo $appUrl.$audioDir."KAC.mp3"; ?>',
    "KAD": '<?php echo $appUrl.$audioDir."KAD.mp3"; ?>',
    "KAH": '<?php echo $appUrl.$audioDir."KAH.mp3"; ?>',
    "KAS": '<?php echo $appUrl.$audioDir."KAS.mp3"; ?>',
    "KJC": '<?php echo $appUrl.$audioDir."KJC.mp3"; ?>',
    "KJD": '<?php echo $appUrl.$audioDir."KJD.mp3"; ?>',
    "KJH": '<?php echo $appUrl.$audioDir."KJH.mp3"; ?>',
    "KJS": '<?php echo $appUrl.$audioDir."KJS.mp3"; ?>',
    "KKC": '<?php echo $appUrl.$audioDir."KKC.mp3"; ?>',
    "KKD": '<?php echo $appUrl.$audioDir."KKD.mp3"; ?>',
    "KKH": '<?php echo $appUrl.$audioDir."KKH.mp3"; ?>',
    "KKS": '<?php echo $appUrl.$audioDir."KKS.mp3"; ?>',
    "KQC": '<?php echo $appUrl.$audioDir."KQC.mp3"; ?>',
    "KQD": '<?php echo $appUrl.$audioDir."KQD.mp3"; ?>',
    "KQH": '<?php echo $appUrl.$audioDir."KQH.mp3"; ?>',
    "KQS": '<?php echo $appUrl.$audioDir."KQS.mp3"; ?>',
    "loner": '<?php echo $appUrl.$audioDir."loner.mp3"; ?>',
    "shuffleQuiet": '<?php echo $appUrl.$audioDir."shuffleQuiet.mp3"; ?>',
    "cardplayed": '<?php echo $appUrl.$audioDir."cardplayed.mp3"; ?>',
    "U1C": '<?php echo $appUrl.$audioDir."U1C.mp3"; ?>',
    "U1D": '<?php echo $appUrl.$audioDir."U1D.mp3"; ?>',
    "U1H": '<?php echo $appUrl.$audioDir."U1H.mp3"; ?>',
    "U1S": '<?php echo $appUrl.$audioDir."U1S.mp3"; ?>',
    "U9C": '<?php echo $appUrl.$audioDir."U9C.mp3"; ?>',
    "U9D": '<?php echo $appUrl.$audioDir."U9D.mp3"; ?>',
    "U9H": '<?php echo $appUrl.$audioDir."U9H.mp3"; ?>',
    "U9S": '<?php echo $appUrl.$audioDir."U9S.mp3"; ?>',
    "UAC": '<?php echo $appUrl.$audioDir."UAC.mp3"; ?>',
    "UAD": '<?php echo $appUrl.$audioDir."UAD.mp3"; ?>',
    "UAH": '<?php echo $appUrl.$audioDir."UAH.mp3"; ?>',
    "UAS": '<?php echo $appUrl.$audioDir."UAS.mp3"; ?>',
    "UJC": '<?php echo $appUrl.$audioDir."UJC.mp3"; ?>',
    "UJD": '<?php echo $appUrl.$audioDir."UJD.mp3"; ?>',
    "UJH": '<?php echo $appUrl.$audioDir."UJH.mp3"; ?>',
    "UJS": '<?php echo $appUrl.$audioDir."UJS.mp3"; ?>',
    "UKC": '<?php echo $appUrl.$audioDir."UKC.mp3"; ?>',
    "UKD": '<?php echo $appUrl.$audioDir."UKD.mp3"; ?>',
    "UKH": '<?php echo $appUrl.$audioDir."UKH.mp3"; ?>',
    "UKS": '<?php echo $appUrl.$audioDir."UKS.mp3"; ?>',
    "UQC": '<?php echo $appUrl.$audioDir."UQC.mp3"; ?>',
    "UQD": '<?php echo $appUrl.$audioDir."UQD.mp3"; ?>',
    "UQH": '<?php echo $appUrl.$audioDir."UQH.mp3"; ?>',
    "UQS": '<?php echo $appUrl.$audioDir."UQS.mp3"; ?>',
    "yourturn": '<?php echo $appUrl.$audioDir."yourturn.mp3";        ?>',
    "silence":  '<?php echo $appUrl.$audioDir."momentofsilence.mp3"; ?>'
  };

  app.soundPop = function() {
    if (!app.soundPlaying) {
      if (app.soundQueue.length > 0) {
        var audio = new Audio(app.soundQueue.pop());
        app.soundPlaying = true;
        // Handle the play() Promise
        audio.play()
          .then(() => {
            // Playback started successfully
            audio.addEventListener('ended', function() {
            app.soundPlaying = false;
            app.deadSoundStartTime = Date.now();
            });
          })
          .catch((error) => {
          console.error("Audio playback failed:", error);
          app.soundPlaying = false; // Reset flag on failure
          });
      }
      if (!app.soundPlaying) {
        var timeDiff = Date.now() - app.deadSoundStartTime;
        if (timeDiff >= 5000) {
          app.soundQueue.push(app.sounds["silence"]); // warm up the play sound apparatus.
        }
      }
    }
  };

  app.soundMute = function(){
    app.soundQueue = [];
  };
  
  // this is used by viewmodels that need to make sure a sound
  // is played only once, but code is reentered multiple times
  // in the same state.  todo: make sure there aren't multiple
  // copies of this.
  app.playOnceRemove = function(a, i){
    const ix = a.indexOf(i);
    if (ix != -1) {
      a.splice(ix, 1);
    }
  };

  
</script>