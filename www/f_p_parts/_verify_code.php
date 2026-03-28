<p class="text-success text-center mb-3 mt-3">Please check your Email for verification code <?php echo $_SESSION['email']; ?></p>
<?php 
if(isset($_GET['error'])){
    ?>
    <p class="text-danger text-center mb-3">Verification code is incorrect</p>
    <?php
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?verify_code" method="post">

    <div class="form-group mb-3">
    <input class="form-control remove_space" type="text" id="verification_code" name="verification_code" required="" placeholder="Verification Code">
    </div>

    <div class="form-group mb-0 text-center">
    <button class="btn btn-primary btn-block" type="submit" id="submit" name="submit"> Verfiy Code </button>
    </div>

</form>