
<?php include_once('config/config.php'); ?>
<?php include_once('controllers/isAuthenticated.php'); ?>
<?php include('controllers/header.php'); ?>
<?php $userIsAuthenticated = isAuthenticated($_COOKIE[$cookieName]); ?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Sides Family Euchre</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02"  aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <?php if($userIsAuthenticated): ?>
          <?php if(strlen($user['ThumbnailURL']) > 0): ?>
            <span style="color:white"><img src="<?php echo $user['ThumbnailURL']; ?>" alt="Thumbnail"></span>&nbsp;
          <?php endif; ?>
            <span  style="color:white"><?php echo 'Hi '.$user['Name'].'!' ?></span>&nbsp;
        <?php endif; ?>
        <div class="collapse navbar-collapse" id="navbarColor02">
          <ul class="navbar-nav ml-auto">
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

