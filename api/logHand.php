<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) 
    || !isAuthenticated($_POST['r']) 
    || !isset($_POST['gameID']) 
    || !isset($_POST['dealID']) 
    || !isset($_POST['lead']) 
    || !isset($_POST['cardO']) 
    || !isset($_POST['cardP']) 
    || !isset($_POST['cardL']) 
    || !isset($_POST['cardR']) 
    || !isset($_POST['organizerTrump']) 
    || !isset($_POST['opponentTrump']) 
    || !isset($_POST['organizerScore']) 
    || !isset($_POST['organizerTricks']) 
    || !isset($_POST['opponentScore']) 
    || !isset($_POST['opponentTricks']) 
    || !isset($_POST['cardFaceUp']) 
    || !isset($_POST['dealer']) 
    ) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'One or more request parameters are missing or invalid.']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $id = GUID();
  $game_id = $_POST['gameID'];
  $deal_id = $_POST['dealID'];
  $lead = $_POST['lead'];
  $card_o = $_POST['cardO'];
  $card_p = $_POST['cardP'];
  $card_l = $_POST['cardL'];
  $card_r = $_POST['cardR'];
  $organizer_trump = $_POST['organizerTrump'];
  $opponent_trump = $_POST['opponentTrump'];
  $organizer_score = $_POST['organizerScore'];
  $organizer_tricks = $_POST['organizerTricks'];
  $opponent_score = $_POST['opponentScore'];
  $opponent_tricks = $_POST['opponentTricks'];
  $card_face_up = $_POST['cardFaceUp'];
  $dealer = $_POST['dealer'];

  try {
    $sql = "insert into `GamePlay` (id, gameid, dealid, lead, cardo, cardp, cardl, cardr, organizertrump, opponenttrump, organizerscore, organizertricks, opponentscore, opponenttricks, cardfaceup, dealer, insertdate) 
            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())";

    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }

    $bind = mysqli_stmt_bind_param(
          $stmt,
          'ssssssssssiiisss',
          $id,
          $game_id,
          $deal_id,
          $lead,
          $card_o,
          $card_p,
          $card_l,
          $card_r,
          $organizer_trump,
          $opponent_trump,
          $organizer_score,
          $organizer_tricks,
          $opponent_score,
          $opponent_tricks,
          $card_face_up,
          $dealer
      );

      if ($bind === false) { throw new Exception(mysqli_error($connection)); }

      if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

      mysqli_stmt_close($stmt);

      http_response_code(200);
      echo json_encode($response);
      
  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while logging the hand.']);
  }

?>