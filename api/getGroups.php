<?php 
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    
    $response = array();
    $response['ErrorMsg'] = is_null($connection) ? "No connection. " : "";
    $groups = array();

    $sql = "select `ID`,`Description` from `Group`";
    
    $results = mysqli_query($connection, $sql);
    if ($results === false) {
      $response['ErrorMsg'] .= mysqli_error($connection);;
    } else {
      $rowCount = mysqli_num_rows($results);
      if ($rowCount <= 0) {
        $response['ErrorMsg'] .= "No groups are defined. ";
      } else {
        while ($row = mysqli_fetch_array($results)) {
          array_push($groups, array($row['ID'],$row['Description']));
        }
      }
    }
  
    $response['Groups'] = $groups;

    http_response_code(200);
    echo json_encode($response);

  } else {
    echo "Expecting request method: POST";
  }

?>