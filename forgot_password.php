<?php

  require_once 'lib/_db.php';
  
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;

  session_start();

  if(isset($_POST['submit'])){

    // checkInput function takes care of input injections //
    function checkInput($data) {
      $data = htmlspecialchars($data);
      $data = strip_tags($data);
      $data = stripslashes($data);
      return $data;
    }
    $case = 'option';
    // --------  These conditions determine which part of php code should run for a particular form -------- //
    if(isset($_SESSION['verification_code']) && isset($_SESSION['email']) && isset($_GET['verify_code']) ) { $case = 'verification_code'; }
    if(isset($_SESSION['verification_code']) && isset($_SESSION['email']) && isset($_GET['new_password']) ) { $case = 'new_password'; }



    switch ($case) {
      // case verification_code validates the verification code sent to user //
      case 'verification_code':

        $confirm_code = checkInput($_POST['verification_code']);

        if($confirm_code == $_SESSION['verification_code']) {

          header('location:forgot_password.php?new_password');
          exit();
        }else {
          header('location:forgot_password.php?verify_code&error');
          exit();
        }
      break;

      // case new_password encrypts and updates new password //
      case 'new_password':

        $password = checkInput($_POST['password']);
        $conf_password = checkInput($_POST['confirm_password']);

        if($password == $conf_password) {

          $user_uq = $db->prepare("UPDATE users SET password=:password WHERE email=:email AND deleted=0");

          try{
            $user_uqx = $user_uq->execute([
                'password' => encrypt_password($password),
                'email'    => $_SESSION['email']
            ]);
            session_unset($_SESSION['email']);
            session_unset($_SESSION['verification_code']);
            $_SESSION['Password_Changed'] = true;
            header('location:login.php');
            exit();

          }catch(PDOException $e) {

            header('location:forgot_password.php?new_password&error');
            exit();
          }
        }
      break;

      // case default happens when user clicks forget password //
      default:
        if( preg_match('/\s/', $_POST['email'])){
          header('location:forgot_password.php?error');
          exit();
        }
        else{
          $email = checkInput($_POST['email']);

          $user_sq = $db->prepare('SELECT * FROM users WHERE email=:email AND deleted=0 LIMIT 1');
          $user_sqx = $user_sq->execute([ 'email' => $email ]);

          $user = $user_sq->fetch();
          if($user_sq->rowCount()) {

            $_SESSION['email'] = $email;   // email stored in session storage


            // appending phpmailer required classes
            require 'lib/PHPMailer/src/Exception.php';
            require 'lib/PHPMailer/src/PHPMailer.php';
            require 'lib/PHPMailer/src/SMTP.php';


            // initializing PHPMailer class with Exception handler
            $mail = new PHPMailer(false);

            $verification_code = mt_rand(100000, 999999);       // generating verification code
            $_SESSION['verification_code'] = $verification_code;// and storing it in session storage


            // email body to be sent for user
            $email_body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>

            </head>
            <body>
              <div class='card'>

                <div class='card-body p-4' style='border:3px solid #00b8a5;'>

                  <div class='text-center w-80 m-auto'>
                    <a href='index-2.html'>
                      <span class='logo-lg'><img src='www/assets/images/logo-lg.png' alt=''></span>
                    </a>

                    <h4 class='text-muted mb-4 mt-3'>Verification Code</h4>
                    <h3 class='text-primary mb-4 mt-3'>" . $_SESSION['verification_code'] . " </h3>
                    <p class='text-muted mb-4 mt-3'>Use this code to reset your password <br>
                                                    Do not share this code with anyone else. </br>
                  </div>

                  <div class='row mt-3'>
                    <div class='col-12 text-center'>
                      <p class='text-muted'>2019 &copy; All rights reserved by <a href='http://www.ariyabod.af'  class='text-primary font-weight-medium ml-1'>Ariyabod</a> </p>
                    </div> <!-- end col -->
                  </div>

                </div> <!-- end card-body -->
              </div>
            </body>
            </html>";


            // SERVER Settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // this method is used for debugging
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'mail.ariyabod.af';
            $mail->SMTPAuth = true;
            $mail->Username = 'tms.mailer@ariyabod.af';
            $mail->Password = 'Harchi@85';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Recipents Options
            $mail->setFrom('tms.mailer@ariyabod.af' , 'Ariyabod ISP');
            $mail->addAddress($user['email']);     // Add a recipient // Name is optional
            $mail->addReplyTo('tms.mailer@ariyabod.af', 'Reset Password');

            // content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
            $mail->Body = $email_body;
            try {
              $mail->send();
              // if email is successfully sent it gets the verification page content
              header('location:forgot_password.php?verify_code');
              exit();

            }catch(Exception $e){
              // in case of error in sending email, check error=2 in _verify_code.php
              header('location:forgot_password.php?error=1');
              exit();
            }
          }else{
            // if email is not available in database
            header('location:forgot_password.php?error');
            exit();
          }
        }
      break;

    } // switch

  } // if submit is clicked
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - Ariyabod Holu</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="www/assets/images/logo-sm.png">
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

                <div class="card-body p-4" style="border:3px solid #00b8a5;">

                  <div class="text-center w-80 m-auto">
                    <a href="index-2.html">
                      <span class="logo-lg"><img src="www/assets/images/logo-lg.png" alt=""></span>
                    </a>

                  </div>

                  <?php
                  // if verification email is sent to user content of this page will be displayed
                  if(isset($_SESSION['email']) && isset($_GET['verify_code']) && isset($_SESSION['verification_code']) ) {

                    include 'www/f_p_parts/_verify_code.php';

                  // if user entered the correct verification code content of this page will be display
                  }else if(isset($_SESSION['email']) && isset($_GET['new_password']) && isset($_SESSION['verification_code'])) {

                    include 'www/f_p_parts/_new_password.php';

                  // this is the page content where user can inserts email
                  }else {

                    include 'www/f_p_parts/_email.php';

                  }

                  ?>

                  <div class="row mt-3">
                    <div class="col-12 text-center">
                      <p class="text-muted">2019 &copy; All rights reserved by <a href="http://www.ariyabod.af"  class="text-primary font-weight-medium ml-1">Ariyabod</a> </p>
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
      <script src="www/assets/js/vendor.min.js"></script>

      <script src="www/assets/libs/jquery-knob/jquery.knob.min.js"></script>
      <script src="www/assets/libs/peity/jquery.peity.min.js"></script>

      <!-- Sparkline charts -->
      <script src="www/assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>

      <!-- init js -->
      <script src="www/assets/js/pages/dashboard-1.init.js"></script>

      <!-- App js -->
      <script src="www/assets/js/app.min.js"></script>

      <!-- Validation js (Parsleyjs) -->
      <script src="www/assets/libs/parsleyjs/parsley.min.js"></script>

      <!-- validation init -->
      <script src="www/assets/js/pages/form-validation.init.js"></script>

      <script src="www/assets/additional/bootoast/bootoast.min.js"></script>

      <!-- Additional js -->
      <script src="www/assets/js/custom.js"></script>
</body>
</html>
