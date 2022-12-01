<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $dealID = $_POST['dealID'];
      $lead = $_POST['lead'];
      $cardO = $_POST['cardO'];
      $cardP = $_POST['cardP'];
      $cardL = $_POST['cardL'];
      $cardR = $_POST['cardR'];
      $organizerTrump = $_POST['organizerTrump'];
      $opponentTrump = $_POST['opponentTrump'];
      $organizerScore = $_POST['organizerScore'];
      $organizerTricks = $_POST['organizerTricks'];
      $opponentScore = $_POST['opponentScore'];
      $opponentTricks = $_POST['opponentTricks'];
      
      $sql = "insert into `GamePlay` () values ()";
      
      $ID = GUID();

      $sql = "
        insert into `GamePlay` (`ID`,`GameID`,`DealID`,`Lead`,`CardO`,`CardP`,`CardL`,`CardR`,`OrganizerTrump`,`OpponentTrump`,`OrganizerScore`,`OrganizerTricks`,`OpponentScore`,`OpponentTricks`,`InsertDate`) 
        values ('{$ID}','{$gameID}','{$dealID}','{$lead}','{$cardO}','{$cardP}','{$cardL}','{$cardR}','{$organizerTrump}','{$opponentTrump}',{$organizerScore},{$organizerTricks},{$opponentScore},{$opponentTricks},now())
      ";

      if (mysqli_query($connection, $sql) === false) {
        $response['ErrorMsg'] = mysqli_error($connection);
      }

      http_response_code(200);
      
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }


?>