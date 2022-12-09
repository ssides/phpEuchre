<?php include_once('config/config.php'); ?>
<?php include_once('controllers/isAuthenticated.php'); ?>
<?php include('controllers/header.php'); ?>
<?php $userIsAuthenticated = isAuthenticated($_COOKIE[$cookieName]); ?>

  <nav class="navbar navbar-expand navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Sides Family Euchre <span style="font-size: 9px">(Version <?php echo $version ?>)</span></a>
        
        <div id="navbarColor02">
          <?php if($userIsAuthenticated): ?>
            <?php if(strlen($user['ThumbnailURL']) > 0): ?>
              <span style="color:white"><img src="<?php echo $user['ThumbnailURL']; ?>" alt="Thumbnail"></span>&nbsp;
            <?php endif; ?>
              <span  style="color:white"><?php echo 'Hi '.$user['Name'].'!' ?></span>&nbsp;
          <?php endif; ?>
          <ul class="navbar-nav me-auto">
            <li class="nav-item <?php echo ($userIsAuthenticated) ? "hide" : ""; ?>">
            <?php echo '<a class="nav-link" href="'.$appUrl.'index.php">Sign in</a>'; ?>
            </li>
            <li class="nav-item <?php echo (!$userIsAuthenticated) ? "hide" : ""; ?>">
            <?php echo '<a class="nav-link" href="'.$appUrl.'logout.php">Sign out</a>'; ?>
            </li>
            <li class="nav-item <?php echo (!$userIsAuthenticated) ? "hide" : ""; ?>">
            <?php echo '<a class="nav-link" href="'.$appUrl.'setProfile.php">Profile</a>'; ?>
            </li>
            <li class="nav-item <?php echo (!$userIsAuthenticated) ? "hide" : ""; ?>">
            <?php echo '<a class="nav-link" href="'.$appUrl.'dashboard.php">Start</a>'; ?>
            </li>
            <li class="nav-item">
            <?php echo '<a class="nav-link" href="'.$appUrl.'signup.php">Sign up</a>'; ?>
            </li>
          </ul>
      </div>
    </div>
  </nav>

