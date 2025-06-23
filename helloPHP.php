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

  <button id="startButton">Start Timer</button>
  <button id="stopButton">Stop Timer</button>
  <script>
    const sound = new Audio('http://localhost:8080/audio/loner.mp3');
    let timer = null;

    document.getElementById('startButton').addEventListener('click', () => {
      if (!timer) {
        timer = setInterval(() => {
          sound.play().catch(error => console.log('Error:', error));
          }, 1000);
      }
    });

    document.getElementById('stopButton').addEventListener('click', () => {
      clearInterval(timer);
      timer = null;
    });
  </script>
  
</body>

</html>