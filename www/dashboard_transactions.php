<?php
include("../lib/_configuration.php");
$holu_page_paths = ["Dashboards", "Transactions"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("_head.php"); ?>
  <style>
    .currency-lines {
      margin: 0;
      padding-left: 0;
      list-style: none;
      line-height: 1.8;
      font-weight: 600;
    }
    .summary-card .card-title {
      margin-bottom: 14px;
      font-size: 1rem;
      font-weight: 700;
    }
  </style>
</head>
<body>
  <div id="wrapper">
    <?php include("_navbar.php"); ?>
    <?php include("_sidebar.php"); ?>

    <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row mt-3">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
              <div class="card summary-card h-100">
                <div class="card-body">
                  <h5 class="card-title">Income</h5>
                  <ul class="currency-lines">
                    <li>35,000,000 AFN</li>
                    <li>3000 USD</li>
                    <li>90 IRT</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
              <div class="card summary-card h-100">
                <div class="card-body">
                  <h5 class="card-title">Expense</h5>
                  <ul class="currency-lines">
                    <li>35,000,000 AFN</li>
                    <li>3000 USD</li>
                    <li>90 IRT</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
              <div class="card summary-card h-100">
                <div class="card-body">
                  <h5 class="card-title">Exchange</h5>
                  <ul class="currency-lines">
                    <li>35,000,000 AFN to 30,000 USD</li>
                    <li>3000 USD to 0000 AFN</li>
                    <li>90 IRT to 0999 AFN</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
              <div class="card summary-card h-100">
                <div class="card-body">
                  <h5 class="card-title">Transfer</h5>
                  <ul class="currency-lines">
                    <li>35,000,000 AFN</li>
                    <li>3000 USD</li>
                    <li>90 IRT</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
              <div class="card summary-card h-100">
                <div class="card-body">
                  <h5 class="card-title">Total</h5>
                  <ul class="currency-lines">
                    <li>35,000,000 AFN</li>
                    <li>3000 USD</li>
                    <li>90 IRT</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
              <div class="card summary-card h-100">
                <div class="card-body">
                  <h5 class="card-title">Total With Closing</h5>
                  <ul class="currency-lines">
                    <li>35,000,000 AFN</li>
                    <li>3000 USD</li>
                    <li>90 IRT</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include("_footer.php"); ?>
    </div>
  </div>

  <?php include("_script.php"); ?>
</body>
</html>
