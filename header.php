<?php 
  include_once('config/config.php'); 
  include_once('controllers/isAuthenticated.php'); 
  include_once('svc/group.php');
  include('controllers/header.php'); ?>
  
<?php 
  $userIsAuthenticated = isAppAuthenticated(); 
  $isManager = isManager($$a['r'], $$a['k']);
?>

  <nav class="navbar navbar-expand navbar-dark bg-primary fixed-top text-nowrap">
    <div class="container">
        <a class="navbar-brand" href="#">Sides Family Euchre <span style="font-size: 9px">(Version <?php echo $version ?>)</span></a>
        
        <div id="navbarColor02">
          <?php if($userIsAuthenticated): ?>
            <?php if(strlen($user['ThumbnailURL']) > 0): ?>
              <span class="headerText"><img src="<?php echo $user['ThumbnailURL']; ?>" alt="Thumbnail"></span>&nbsp;
            <?php endif; ?>
            <span class="headerText"><?php echo 'Hi '.$user['Name'].'!' ?></span>&nbsp;
            <?php if(empty($$a['k'])): ?>
              <span class="headerText"><?php echo 'You are not logged in to any group.' ?></span>&nbsp;
            <?php else: ?>
              <span class="headerText"><?php echo 'You are logged in to group '.$$a['l'] ?></span>&nbsp;
            <?php endif; ?>
          <?php endif; ?>
          <ul class="navbar-nav me-auto">
            <li class="nav-item <?php echo ($userIsAuthenticated) ? "hide" : ""; ?>">
              <?php echo '<a class="nav-link" href="index.php">Sign in</a>'; ?>
            </li>
            <li class="nav-item <?php echo (!$userIsAuthenticated) ? "hide" : ""; ?>">
              <?php echo '<a class="nav-link" href="logout.php">Sign out</a>'; ?>
            </li>
            <li class="nav-item <?php echo (!$userIsAuthenticated) ? "hide" : ""; ?>">
              <?php echo '<a class="nav-link" href="setProfile.php">Profile</a>'; ?>
            </li>
            <li class="nav-item <?php echo (!$userIsAuthenticated || !$isManager) ? "hide" : ""; ?>">
              <?php echo '<a class="nav-link" href="manageGroups.php">Groups</a>'; ?>
            </li>
            <li class="nav-item <?php echo (!$userIsAuthenticated) ? "hide" : ""; ?>">
              <?php echo '<a class="nav-link" href="dashboard.php">Start</a>'; ?>
            </li>
            <li class="nav-item">
              <?php echo '<a class="nav-link" href="signup.php">Sign up</a>'; ?>
            </li>
          </ul>
      </div>
    </div>
  </nav>

<script type="text/javascript">
  var hdrCtrlrError = '<?php echo str_replace("'", "\'", $hdrCtrlrError); ?>';
  if (hdrCtrlrError.length > 0) {
    console.log('header: ', hdrCtrlrError);
  }
</script>