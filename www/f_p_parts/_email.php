<p class="text-muted text-center mb-3 mt-3">Change your password</p>

<?php
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid_credentials') {
        ?>
        <p class="text-danger text-center mb-4 mt-3">Email or old password is incorrect.</p>
        <?php
    } else if ($_GET['error'] === 'password_mismatch') {
        ?>
        <p class="text-danger text-center mb-4 mt-3">New password and confirm password do not match.</p>
        <?php
    } else if ($_GET['error'] === 'same_password') {
        ?>
        <p class="text-danger text-center mb-4 mt-3">New password must be different from old password.</p>
        <?php
    } else if ($_GET['error'] === 'update_failed') {
        ?>
        <p class="text-danger text-center mb-4 mt-3">Failed to update password. Please try again.</p>
        <?php
    } else {
        ?>
        <p class="text-danger text-center mb-4 mt-3">Please fill all fields correctly.</p>
        <?php
    }
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">

    <div class="form-group mb-3">
        <input class="form-control remove_space" type="email" id="email" name="email" required="" placeholder="Email">
    </div>

    <div class="form-group mb-3">
        <input class="form-control remove_space" type="password" id="old_password" name="old_password" required="" placeholder="Old Password">
    </div>

    <div class="form-group mb-3">
        <input class="form-control remove_space" type="password" id="new_password" name="new_password" required="" placeholder="New Password">
    </div>

    <div class="form-group mb-3">
        <input class="form-control remove_space" type="password" id="confirm_password" name="confirm_password" required="" placeholder="Confirm New Password">
    </div>

    <div class="form-group mb-3 text-center">
        <button class="btn btn-primary btn-block" type="submit" id="submit" name="submit"> Change Password </button>
    </div>

</form>
