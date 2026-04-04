<ul class="list-unstyled topnav-menu float-right mb-0">

  <li class="dropdown notification-list">
    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
      <i class="fa fa-user noti-icon"></i>
      <span class="pro-user-name ml-1">
        <?php echo $_SESSION['holu_username']; ?> <i class="mdi mdi-chevron-down"></i> 
      </span>
    </a>
    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
      <!-- item-->
      <a href="_logout.php" class="dropdown-item notify-item">
        <i class="remixicon-logout-box-line"></i>
        <span>Logout</span>
      </a>

    </div>
  </li>

  
</ul>

<!-- LOGO -->
<div class="logo-box">
  <a href="home.php" class="logo text-center">
    <span class="logo-lg">
      <img src="assets/images/white-logo.png" alt="">
      <!-- <span class="logo-lg-text-light">Xeria</span> -->
    </span>
    <span class="logo-sm">
      <!-- <span class="logo-sm-text-dark">X</span> -->
      <img src="assets/images/white-logo.png" alt="">
    </span>
  </a>
</div>

<ul class="list-unstyled topnav-menu topnav-menu-left m-0">
  <li>
    <button class="button-menu-mobile waves-effect waves-light">
      <i class="fe-menu"></i>
    </button>
  </li>

</ul>

