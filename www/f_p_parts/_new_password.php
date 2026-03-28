<p class="text-muted text-center mb-3 mt-3">Create new password</p>
<?php 
if(isset($_GET['error'])){
    ?>
    <p class="text-danger text-center mb-3 mt-3">Failed to change password</p>
    <?php
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?new_password" method="post">

    <div class="form-group mb-3">
    <input class="form-control remove_space" type="password" onclick="password_strength($(this))" id="password" name="password" required="" placeholder="New Password">
    </div>
    <div class="form-group mb-3">
    <input class="form-control remove_space" type="password" onclick="password_strength($(this))" id="confirm_password" name="confirm_password" required="" placeholder="Confirm Password">
    </div>

    <div class="form-group mb-0 text-center">
    <button class="btn btn-primary btn-block" type="submit" id="submit" name="submit"> Change Password </button>
    </div>

</form>