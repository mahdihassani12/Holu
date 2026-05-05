<?php
include("../lib/_configuration.php");
$holu_page_paths = ["Dashboards", "Transactions"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("_head.php"); ?>
  <style>
    .dashboard-summary-card {
      background-color: #2fa9c0;
      border-radius: 8px;
      color: #fff;
      padding: 12px 14px;
      min-height: 112px;
      margin-bottom: 16px;
    }
    .dashboard-summary-title {
      font-weight: 700;
      font-size: 22px;
      line-height: 1.1;
      margin-bottom: 6px;
    }
    .dashboard-summary-lines {
      font-size: 15px;
      line-height: 1.4;
      font-weight: 500;
    }
  </style>
</head>
<body class="left-side-menu-dark">
  <div id="wrapper">
    <div class="navbar-custom"><?php include("_navbar.php"); ?></div>
    <div class="left-side-menu"><?php include("_sidebar.php"); ?></div>
    <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row"><?php include("_page_title.php"); ?></div>

          <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-6">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Income:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Expense:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Exchange:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN to 30,000 USD</div>
                <div class="dashboard-summary-lines">3000 USD to 0000 AFN</div>
                <div class="dashboard-summary-lines">90 IRT to 0999 AFN</div>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Transfer:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Total:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Total With Closing:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <?php include("_footer.php"); ?>
    </div>
  </div>
</body>
</html>
