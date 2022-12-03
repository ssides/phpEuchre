

  app = {};
  
  app.times = {
    firstJackTime: 600,
    gameTime: 1000
  };
  
  app.positions = 'OPLR';
  app.appURL = '<?php echo $appUrl; ?>';

  app.apiPostData = '';

  app.getCardURL = function(cardID){
      return app.appURL + 'content/images/cards/' + cardID + '.png';
    };
  
