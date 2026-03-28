<p class="text-muted text-center mb-3 mt-3">Enter Your Email Address<br></p>
                    
<?php 
if(isset($_GET['error'])){
    if($_GET['error'] == 1) {
        ?>
        <p class="text-danger text-center mb-4 mt-3">Error! While Sending Email.</p>
        <?php
    }else {
        ?>
        <p class="text-danger text-center mb-4 mt-3">Email address not found.</p>
        <?php
    }
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

    <div class="form-group mb-3">
    <input class="form-control remove_space" type="email" id="email" name="email" required="" placeholder="Email">
    </div>

    <div class="form-group mb-3 text-center">
    <button class="btn btn-primary btn-block" type="submit" id="submit" name="submit"> Send Email </button>
    </div>

</form>
<script type="text/javascript">
    var form = document.querySelector('form');
    form.onsubmit = function() {
        document.querySelector('#submit').innerText = 'Sending Email ...';
    };
</script>
<?php