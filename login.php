<?php
include("lib/_db.php");
session_start();

if(isset($_POST['submit'])){

  $email = $_POST['email'];
  $password = md5($_POST['password']);

  $user_sq = $db->prepare("SELECT * FROM `users` WHERE email=:email AND password=:password AND deleted='0' LIMIT 1");

  $user_sqx = $user_sq->execute([
    'email'=>$email,
    'password'=>$password
  ]);

  if($user_sq->rowCount()){
    $user_row = $user_sq->fetch();
    $_SESSION['holu_users_id'] = $user_row['id'];
    $_SESSION['holu_username'] = $user_row['username'];

    $users_id = $_SESSION['holu_users_id'];

    $holu_accessibilities = '';
    $accessibility_sq = $db->query("SELECT access_point FROM `accessibilities` WHERE system_users_id='$users_id' AND deleted='0' AND is_accessed='1'");
    if($accessibility_sq->rowCount()>0){
      while($accessibility_row = $accessibility_sq->fetch()){
        $holu_accessibilities .= $accessibility_row['access_point'].",";
      }
    }

    $_SESSION['holu_accessibilities'] = $holu_accessibilities;

    header("location:www/home.php");
    exit();
  }else{
    header("location:login.php?error");
    exit();
  }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>FlowBook</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Zalmai" name="author" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="www/assets/images/fav.png">
  <!-- App css -->
  <link href="www/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="www/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <link href="www/assets/css/app.min.css" rel="stylesheet" type="text/css" />
  <link href="www/assets/additional/bootoast/bootoast.css" rel="stylesheet" type="text/css" />
  <!-- Jquery Toast css -->
  <link href="www/assets/css/custom.css" rel="stylesheet" type="text/css" />
  <style type="text/css">
    #login_form #submit{
      background: #f8a39d;
      border-color: #f8a39d;
    }
    .login_details a{
      color: #f8a39d;
    }
  </style>
</head>
<body>
  <div class="account-pages mt-5 mb-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          <div class="card">

            <div class="card-body p-4" style="border:2px solid #f8a39d;">

              <div class="text-center w-75 m-auto">
                <a href="/">
                  <span class="logo-lg"><img src="www/assets/images/logo.png" alt=""></span>
                </a>

                <p class="text-muted mb-4 mt-3">Enter your username and password to access admin panel.</p>
                
                <?php 
                if(isset($_GET['error'])){
                  ?>
                  <p class="text-danger mb-4 mt-3">Wrong username or password! Try again.</p>
                  <?php
                }
                ?>
                
              </div>

              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="login_form">

                <div class="form-group mb-3">
                  <input class="form-control" 
                         type="email" 
                         id="email" 
                         name="email" 
                         required 
                         placeholder="Enter your email" 
                         autocomplete="off" 
                         />
                </div>

                <div class="form-group mb-3">
                  <input class="form-control" 
                         type="password" 
                         required 
                         id="password" 
                         name="password" 
                         placeholder="Enter your password"
                         autocomplete="off" 
                        />
                </div>

                <div class="form-group mb-0 text-center">
                  <button class="btn btn-primary btn-block" type="submit" id="submit" name="submit"> Log In </button>
                </div>

              </form>

              <div class="row mt-3 login_details">
                <div class="col-12 text-center">
                  <p>
                    <a href="forgot_password.php" class="font-weight-medium ml-1">Forgot Password?
                    </a>
                  </p>
                  <p class="text-muted">
                    2026 &copy; All rights reserved by Benyamin Hope.
                    </a>
                  </p>
                </div> <!-- end col -->
              </div>

            </div> <!-- end card-body -->
          </div>
          <!-- end card -->

          
          <!-- end row -->

        </div> <!-- end col -->
      </div>
      <!-- end row -->
    </div>
    <!-- end container -->
  </div>
      
  <!-- END wrapper -->
  <div class="rightbar-overlay"></div>
  <!-- Vendor js -->
  <script src="assets/js/vendor.min.js"></script>

  <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
  <script src="assets/libs/peity/jquery.peity.min.js"></script>

  <!-- Sparkline charts -->
  <script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>

  <!-- init js -->
  <script src="assets/js/pages/dashboard-1.init.js"></script>

  <!-- App js -->
  <script src="assets/js/app.min.js"></script>

  <!-- Validation js (Parsleyjs) -->
  <script src="assets/libs/parsleyjs/parsley.min.js"></script>

  <!-- validation init -->
  <script src="assets/js/pages/form-validation.init.js"></script>

  <script src="assets/additional/bootoast/bootoast.min.js"></script>

  <!-- Additional js -->
  <script src="assets/js/custom.js"></script>
</body>
</html>