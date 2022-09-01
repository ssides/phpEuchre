<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('controllers/GUID.php');

    if(isset($_POST['upload'])) {
      
      if (empty($_COOKIE[$cookieName])) {
        header('Location: index.php');
      }
      
      $fileName = $_FILES['profileImage']['name'];
      $contentType = $_FILES['profileImage']['type'];
      $size = $_FILES['profileImage']['size'];
      
      if ($_FILES['profileImage']['error'] == 0) {
        list($width, $height, $_gettype, $_getattr) = getimagesize($_FILES['profileImage']['tmp_name']);
        $bytes = mysqli_real_escape_string(file_get_contents($_FILES['profileImage']['tmp_name']));
        $_fileName = mysqli_real_escape_string($fileName);
        $smt = mysqli_prepare($connection, 'insert into `UserProfile` (`ID` , `PlayerID` ,`FileName` , `OriginalImage`,`ContentType` ,`FileSize` ,`InsertDate` ) values (?, ?, ?, ?, ?, ?, now())');
        mysqli_stmt_bind_param($smt, 'sssssi', GUID(), $_COOKIE[$cookieName]), $_fileName, $bytes, $contentType, $size);
        if (!mysqli_stmt_execute($smt)){
          $sqlErr = mysqli_error($connection);
        }
        mysqli_stmt_close($smt);
      } else {
        $errorMsg = 'File could not be uploaded.';
      }
    }
    
    function getContentType(string $filename)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        if (!$info) {
            return "image";
        }

        $mime_type = finfo_file($info, $filename);
        finfo_close($info);

        return $mime_type;
    }
?>