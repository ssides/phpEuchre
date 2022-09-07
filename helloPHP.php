<?php include('controllers/helloPHP.php'); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./content/css/site.css">
    <title>Hello PHP</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="row">
        <div class="col-md-4">
            &nbsp;
        </div>
        <div class="col-md-4">
            <img src="./thumbnail.php?r=<?php echo mt_rand(0, 65535) ?>" alt="Thumbnail">
            <br>
            <br>
        </div>
    </div>

    <div class="helloPadding">
        <?php include('svc/helloPHP.php'); ?>
        <?php include_once('svc/thumbnailServices.php'); ?>

        <table>
            <tr>
                <td></td>
                <td>
                    <form action="" method="post">
                        <button type="submit" name="up" id="up" class="btn btn-outline-primary btn-sm btn-block">
                            <div class="bi bi-arrow-up"></div>
                        </button>
                    </form>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <form action="" method="post">
                        <button type="submit" name="left" id="left" class="btn btn-outline-primary btn-sm btn-block">
                            <div class="bi bi-arrow-left"></div>
                        </button>
                    </form>
                </td>
                <td>
                    <div class="container">
                        <div class="center">
                            <img src="./thumbnail.php?r=<?php echo mt_rand(0, 65535) ?>" alt="Thumbnail">
                        </div>
                    </div>
                </td>
                <td>
                    <form action="" method="post">
                        <button type="submit" name="right" id="right" class="btn btn-outline-primary btn-sm btn-block">
                            <div class="bi bi-arrow-right"></div>
                        </button>
                    </form>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <form action="" method="post">
                        <button type="submit" name="down" id="down" class="btn btn-outline-primary btn-sm btn-block">
                            <div class="bi bi-arrow-down"></div>
                        </button>
                    </form>
                </td>
                <td>
                    <form action="" method="post">
                        <button type="submit" name="zoomin" id="zoomin" class="btn btn-outline-primary btn-sm btn-block">+</button>
                    </form>
                    <form action="" method="post">
                        <button type="submit" name="zoomout" id="zoomout" class="btn btn-outline-primary btn-sm btn-block">-</button>
                    </form>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>