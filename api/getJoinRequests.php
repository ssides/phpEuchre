<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID
  
  // Select group join requests for the group I am logged in to.
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication']);
    exit;
  }

  $response = ['ErrorMsg' => ''];
  $response['Requests'] = [];
  $playerID = $_POST['r'];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    echo json_encode(['ErrorMsg' => 'Internal server error']);
    exit;
  }
  
  try {
    $sql = "select g.`ID` GroupID, g.`Description`, p.`ID` PlayerID, p.`Name` 
            from `GroupRequest` gr 
            join `Player` p on gr.`PlayerID` = p.`ID`
            join `Group` g on gr.`GroupID` = g.`ID`
            where gr.`IsActive` = 'R' and g.`ManagerID` = ?
            order by g.`Description`,p.`Name`";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    if (!mysqli_stmt_bind_param($stmt, 's', $playerID)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }
    
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
      $response['Requests'][] = [
        'ID' => GUID(), // so the front end can uniquely identify the buttons
        'GroupID' => $row['GroupID'],
        'Description' => $row['Description'],
        'PlayerID' => $row['PlayerID'],
        'Name' => $row['Name'],
        ];
    }
    
    mysqli_stmt_close($stmt);
    
    http_response_code(200);
    echo json_encode($response);
            
  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'Internal Server Error']);
  }

  mysqli_close($conn);

?>