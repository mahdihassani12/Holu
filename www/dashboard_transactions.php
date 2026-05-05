<?php
include("../lib/_configuration.php");
$holu_page_paths = ["Dashboards", "Transactions"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("_head.php"); ?>
</head>
<body>
  <div id="wrapper">
    <?php include("_navbar.php"); ?>
    <?php include("_sidebar.php"); ?>

    <div class="content-page">
      <div class="content">
        <div class="container-fluid"></div>
      </div>
      <?php include("_footer.php"); ?>
    </div>
  </div>

  <?php include("_script.php"); ?>
</body>
</html>
