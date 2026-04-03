<?php

require_once 'lib/_db.php';
session_start();

if (isset($_POST['submit'])) {

  function checkInput($data) {
    $data = htmlspecialchars($data);
    $data = strip_tags($data);
    $data = stripslashes($data);
    return trim($data);
  }

  $email = checkInput($_POST['email'] ?? '');
  $old_password = checkInput($_POST['old_password'] ?? '');
  $new_password = checkInput($_POST['new_password'] ?? '');
  $confirm_password = checkInput($_POST['confirm_password'] ?? '');

  if (
    $email === '' ||
    $old_password === '' ||
    $new_password === '' ||
    $confirm_password === '' ||
    preg_match('/\s/', $email)
  ) {
    header('location:forgot_password.php?error=invalid_input');
    exit();
  }

  if ($new_password !== $confirm_password) {
    header('location:forgot_password.php?error=password_mismatch');
    exit();
  }

  if ($old_password === $new_password) {
    header('location:forgot_password.php?error=same_password');
    exit();
  }

  $update_password_q = $db->prepare(
    "UPDATE users
     SET password = :new_password
     WHERE email = :email
       AND password = :old_password
       AND deleted = 0"
  );

  try {
    $update_password_q->execute([
      'new_password' => md5($new_password),
      'email' => $email,
      'old_password' => md5($old_password)
    ]);

    if ($update_password_q->rowCount() > 0) {
      header('location:login.php?password_changed=1');
      exit();
    }

    header('location:forgot_password.php?error=invalid_credentials');
    exit();
  } catch (PDOException $e) {
    header('location:forgot_password.php?error=update_failed');
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - FlowBook</title>

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
</head>
<body>
    <div class="account-pages mt-5 mb-5">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
              <div class="card">

                <div class="card-body p-4" style="border:3px solid #36b8cb;">

                  <div class="text-center w-80 m-auto">
                    <a href="index-2.html">
                      <span class="logo-lg"><img src="www/assets/images/blue-logo.png" alt=""></span>
                    </a>

                  </div>

                  <?php include 'www/f_p_parts/_email.php'; ?>

                  <div class="row mt-3">
                    <div class="col-12 text-center">
                      <p class="text-muted">2026 &copy; All rights reserved by Benyamin Hope. </p>
                      <p class="text-muted"  class="text-primary font-weight-medium ml-1">Developed by <span class="text-primary font-weight-medium ml-1">Mahdi Hassani</span></p>
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
</body>
</html>
