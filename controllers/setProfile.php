<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('controllers/GUID.php');

    if(isset($_POST['upload'])) {
      
      if (empty($_COOKIE[$cookieName])) {
        header('Location: index.php');
      }
      
      if ($_FILES['profileImage']['error'] == 0) {
        list($width, $height, $_gettype, $_getattr) = getimagesize($_FILES['profileImage']['tmp_name']);
        
        if ($width == 0 || $height == 0) {
          $errorMsg = 'Could not get the dimensions of the image.';
        } else {
          $image = file_get_contents($_FILES['profileImage']['tmp_name']);
          $img = imagecreatefromstring($image);
          if ($img === false) {
            $errorMsg = 'Could not create the thumbnail.';
          } else {
            $scale = getScale($width, $height);
            $th = getThumbnailAsString($img, $scale, $width, $height);
            
            $contentType = $_FILES['profileImage']['type'];
            $size = $_FILES['profileImage']['size'];
            
            $_fileBytes = mysqli_real_escape_string($connection, $image);
            $_fileName = mysqli_real_escape_string($connection, $_FILES['profileImage']['name']);
            
            $smt = mysqli_prepare($connection, 'insert into `UserProfile` (`ID` , `PlayerID` ,`FileName` , `OriginalImage`,`ContentType` ,`FileSize` ,`Thumbnail`,`HOffset`,`VOffset`,`OriginalScale`,`InsertDate` ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, now())');
            mysqli_stmt_bind_param($smt, 'sssbsibiid', GUID(), $_COOKIE[$cookieName], $_fileName, $_fileBytes, $contentType, $size, $th, 0, 0, $scale);
            if (!mysqli_stmt_execute($smt)){
              $sqlErr = mysqli_error($connection);
            }
            mysqli_stmt_close($smt);
          }
        }
      } else {
        $errorMsg = 'File could not be uploaded.';
      }
    }
    
    function getThumbnailAsString($img, $scale, $width, $height) {
      global $connection;
      $simg = imagescale($img, $scale * $width);
      ob_start();
      imagepng($simg);
      $imgAsString = ob_get_contents();
      ob_end_clean();
      return mysqli_real_escape_string($connection, $imgAsString);
    }
    
    function getScale($width, $height) {
      global $thumbnailDim;
      if ($width / $height < 1) {
        return $thumbnailDim / $width;
      } else {
        return $thumbnailDim / $height;
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