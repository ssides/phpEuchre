<?php include_once('../config/config.php'); ?>

  <nav class="navbar navbar-expand navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">SFE Reports</a>
        
        <div id="navbarColor02">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
            <?php echo '<a class="nav-link" href="'.$appUrl.'dashboard.php">Start</a>'; ?>
            </li>
            <li class="nav-item">
            <?php echo '<a class="nav-link" href="'.$appUrl.'/reports/">Reports</a>'; ?>
            </li>
          </ul>
      </div>
    </div>
  </nav>

