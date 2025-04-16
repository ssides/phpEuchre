<script type="text/javascript">

  function trickModel(data) {
    this.lead = data.lead || '';
    this.cardO = data.cardO || '';
    this.cardL = data.cardL || '';
    this.cardP = data.cardP || '';
    this.cardR = data.cardR || '';
    this.organizerTricks = data.organizerTricks || '';
    this.opponentTricks = data.opponentTricks || '';
    this.playCardIndex = 0;
    this.playOrder = '';
  }
  
  function handModel(data) {
    this.organizerTrump = data.organizerTrump || '';
    this.opponentTrump = data.opponentTrump || '';
    this.organizerScore = data.organizerScore || '';
    this.opponentScore = data.opponentScore || '';
    this.cardFaceUp = data.cardFaceUp || '';
    this.dealer = data.dealer || '';
    this.dealID = data.dealID || '';
    this.trickIndex = 0;
    this.tricks = [];
  }

  function replayController() {
    var self = this;

    self.whatsTrumpVM = new whatsTrumpViewModel();
    self.organizerInfoVM = new playerViewModel();
    self.partnerInfoVM = new playerViewModel();
    self.leftInfoVM = new playerViewModel();
    self.rightInfoVM = new playerViewModel();
    self.organizerScoreVM = new scoreViewModel(); // for displaying the score during replay
    self.opponentScoreVM = new scoreViewModel();  // same as above
    self.replayVM = new replayViewModel();
    self.playVM = new playViewModel(); // to play the cards on the PlayTable.
    self.getScoresTimeout = 10; // five seconds
    self.scoresTimer = null;
    self.gameID = '<?php echo $_SESSION['replayGameID']; ?>';
    self.fromActiveGame = <?php $f = (isset($_SESSION['replayFromActiveGame']) && $_SESSION['replayFromActiveGame'] == 'true' ? 'true' : 'false'); echo $f; ?>;
    self.hands = [];
    self.currentHandIndex = 0;
    self.currentTrick = 0;
    
    self.getPlayOrder = function(lead, cardfaceup){
      var o = '';
      switch (lead) {
        case 'O': o = 'OLPR'; break;
        case 'P': o = 'PROL'; break;
        case 'L': o = 'LPRO'; break;
        case 'R': o = 'ROLP'; break;
      }
      if (cardfaceup.length > 4) {
        o = o.replace(cardfaceup[4], '');
      }
      return o;
    };

    self.pushHand = function(h) {
      if (h.tricks.length == 5) {
        self.hands.push(h);
      }    
    };
    
    self.processScores = function(scores) {
      var dealid = '';
      var hand = null;

      scores.forEach(function(s){
        if (s.dealID != dealid) {
          if (hand === null) {
            hand = new handModel(s);
          } else {
            self.pushHand(hand);
            hand = new handModel(s);
          }
          dealid = s.dealID;
        }
        var t = new trickModel(s);
        t.playOrder = self.getPlayOrder(s.lead, hand.cardFaceUp);
        hand.tricks.push(t);
        if (hand.tricks.length == 5) {
          hand.organizerScore = s.organizerScore;
          hand.opponentScore = s.opponentScore;
        }
      });
      if (hand !== null) {
        self.pushHand(hand);
      }
      self.initializeHand();
    };
    
    self.initializeHand = function(){
      self.currentHandIndex = self.hands.length;
      self.currentHand = self.hands[self.currentHandIndex - 1];
      self.populateCurrentHand(self.currentHand);
    };
    
    self.populateCurrentHand = function(){
      if (self.hands.length == 0) {
        self.replayVM.setMessage('There are no fully played hands in this game.');
      } else {
        var hand = self.currentHand;
        self.currentTrick = 0;
        self.organizerInfoVM.updateHand('O', hand);
        self.partnerInfoVM.updateHand('P', hand);
        self.leftInfoVM.updateHand('L', hand);
        self.rightInfoVM.updateHand('R', hand);
        self.whatsTrumpVM.updateHand(hand);
        self.replayVM.updateHand(hand, self.fromActiveGame);
        self.organizerScoreVM.zeroScore();
        self.opponentScoreVM.zeroScore();
        if (self.fromActiveGame) {
          self.replayVM.setMessage('During game play, only one hand is visible.');
        } else {
          self.replayVM.setHandMessage('Hand ' + self.currentHandIndex + ' of ' + self.hands.length);
        }
        if (self.currentHandIndex > 1) {
          var h = self.hands[self.currentHandIndex - 2];
          self.organizerScoreVM.updateScore(h);
          self.opponentScoreVM.updateScore(h);
        }
        hand.trickIndex = 0;
        hand.tricks.forEach(function(t){ t.playCardIndex = 0; });
      }
    };
    
    self.getScoresFinished = function() {
      self.getScoresTimeout--;
      if (!scores.success && self.getScoresTimeout === 0) {
        self.replayVM.setMessage('Could not get scores.');
      }
      if (scores.success || self.getScoresTimeout === 0) {
        clearInterval(self.scoresTimer);
        if (scores.success) {
          self.processScores(scores.scores);
        }
      }
    }

    self.getGame = function(){
      var postData = { 
        <?php echo 'r:'."'{$$a['r']}'" ?>,
        gameID: self.gameID
      };

      $.ajax({
        method: 'POST',
        url: '../api/getGame.php',
        data: postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              self.organizerInfoVM.updateName(data.Game.OThumbnailURL, data.Game.OName, true);
              self.partnerInfoVM.updateName(data.Game.PThumbnailURL, data.Game.PName);
              self.leftInfoVM.updateName(data.Game.LThumbnailURL, data.Game.LName);
              self.rightInfoVM.updateName(data.Game.RThumbnailURL, data.Game.RName);
            }
          } catch (error) {
            console.log('Error in getGame(): ' + error.message || error + ': Game stopped.');
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.getScores = function() {
      self.getScoresTimeout = 10; // five seconds
      self.scoresTimer = setInterval(self.getScoresFinished, 500);
      scores.getScores(self.gameID);
    };
    
    self.nextHandCallback = function(){
      self.replayVM.setMessage('');
      self.playVM.clearTable();
      self.organizerInfoVM.updateLead(false);
      self.partnerInfoVM.updateLead(false);
      self.leftInfoVM.updateLead(false);
      self.rightInfoVM.updateLead(false);

      if (self.currentHandIndex == self.hands.length) {
        self.replayVM.setMessage('No more hands to see in this game.');
      } else {
        self.currentHandIndex++;
        self.currentHand = self.hands[self.currentHandIndex - 1];
        self.populateCurrentHand();
      }
    };
    
    self.previousHandCallback = function(){
      self.replayVM.setMessage('');
      self.playVM.clearTable();
      self.organizerInfoVM.updateLead(false);
      self.partnerInfoVM.updateLead(false);
      self.leftInfoVM.updateLead(false);
      self.rightInfoVM.updateLead(false);

      if (self.currentHandIndex == 1) {
        self.replayVM.setMessage('This is the first hand of the game.');
      } else {
        self.currentHandIndex--;
        self.currentHand = self.hands[self.currentHandIndex - 1];
        self.populateCurrentHand();
      }
    };
    
    self.playCardCallback = function(){
      self.replayVM.setMessage('');
      var h = self.currentHand;
      var t = h.tricks[h.trickIndex];
      var playCardLimit = (h.cardFaceUp.length > 4 ? 3 : 4);
      
      if (h.trickIndex == 5) {
        self.organizerScoreVM.updateScore(h);
        self.opponentScoreVM.updateScore(h);
        self.nextHandCallback();
      } else if (t.playCardIndex == playCardLimit) {
        self.playVM.clearTable();
        self.organizerScoreVM.updateTricks(t);
        self.opponentScoreVM.updateTricks(t);
        h.trickIndex++;
      } else {
        var player = t.playOrder[t.playCardIndex];
        switch (player) {
          case 'O': 
            self.organizerInfoVM.playCard(t.cardO); 
            self.playVM.playCard('O', t.cardO);
            break;
          case 'P': 
            self.partnerInfoVM.playCard(t.cardP); 
            self.playVM.playCard('P', t.cardP);
            break;
          case 'L': 
            self.leftInfoVM.playCard(t.cardL); 
            self.playVM.playCard('L', t.cardL);
            break;
          case 'R': 
            self.rightInfoVM.playCard(t.cardR); 
            self.playVM.playCard('R', t.cardR);
            break;
        }
        t.playCardIndex++;
      }
    };
    
    self.playTrickCallback = function() {
      self.replayVM.setMessage('');
      var h = self.currentHand;
      var t = h.tricks[h.trickIndex];
      
      if (h.trickIndex == 5) {
        self.playVM.clearTable();
        self.organizerScoreVM.updateScore(h);
        self.opponentScoreVM.updateScore(h);
        self.nextHandCallback();
      } else {
        if (!self.organizerInfoVM.isSkipped()) {
          self.organizerInfoVM.playCard(t.cardO);
          self.organizerInfoVM.updateLead(t.lead == 'O');
          self.playVM.playCard('O', t.cardO);
        }

        if (!self.partnerInfoVM.isSkipped()) {
          self.partnerInfoVM.playCard(t.cardP);
          self.partnerInfoVM.updateLead(t.lead == 'P');
          self.playVM.playCard('P', t.cardP);
        }
        
        if (!self.leftInfoVM.isSkipped()) {
          self.leftInfoVM.playCard(t.cardL);
          self.leftInfoVM.updateLead(t.lead == 'L');
          self.playVM.playCard('L', t.cardL);
        }
        
        if (!self.rightInfoVM.isSkipped()) {
          self.rightInfoVM.playCard(t.cardR);
          self.rightInfoVM.updateLead(t.lead == 'R');
          self.playVM.playCard('R', t.cardR);
        }
        
        self.organizerScoreVM.updateTricks(t);
        self.opponentScoreVM.updateTricks(t);
        if (h.trickIndex == 4) {
          self.organizerScoreVM.updateScore(h);
          self.opponentScoreVM.updateScore(h);
        }
        h.trickIndex++;
      }

    };
    
    self.initialize = function(){
      self.getScores();
      self.getGame();
      self.organizerScoreVM.updateName("Organizer");
      self.opponentScoreVM.updateName("Opponent");
      self.replayVM.setCallbacks(self.nextHandCallback, self.previousHandCallback, self.playCardCallback, self.playTrickCallback);
    }
    
    self.initialize();
  }
  
  $(function (){
    var rc = new replayController();
    ko.applyBindings(rc.whatsTrumpVM, $('#WhatsTrump')[0]);
    ko.applyBindings(rc.organizerInfoVM, $('#OrganizerInfo')[0]);
    ko.applyBindings(rc.partnerInfoVM, $('#PartnerInfo')[0]);
    ko.applyBindings(rc.leftInfoVM, $('#LeftInfo')[0]);
    ko.applyBindings(rc.rightInfoVM, $('#RightInfo')[0]);
    ko.applyBindings(rc.organizerScoreVM, $('#OrganizerScore')[0]);
    ko.applyBindings(rc.opponentScoreVM, $('#OpponentScore')[0]);
    ko.applyBindings(rc.playVM, $('#PlayTable')[0]);
    ko.applyBindings(rc.replayVM, $('#Controls')[0])
  });
  
</script>
