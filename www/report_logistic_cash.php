<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Logistic Cashes"];

  $logistic_cash_sq = $db->query("SELECT * FROM `logistic_cashes` WHERE deleted='0' ORDER BY id DESC");

  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("_head.php"); ?>
</head>
<body class="left-side-menu-dark">
  <!-- Begin page -->
  <div id="wrapper">
    <!-- Topbar Start -->
    <div class="navbar-custom">
      <?php include("_navbar.php"); ?>
    </div>
    <!-- end Topbar -->
    <!-- ========== Left Sidebar Start ========== -->
    <div class="left-side-menu">
      <?php include("_sidebar.php"); ?>
    </div>
    <!-- Left Sidebar End -->
    <div class="content-page">
      <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
          <!-- start page title -->
          <div class="row">
            <?php include("_page_title.php"); ?>
          </div>     
          <!-- end page title -->

          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">
                <h4 class="header-title"><i class="fa fa-list"></i> Report of Logistic Cash</h4>

              </div>
              <div class="card-box">
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Name</th>
                        <th>Total Cash Reservation</th>
                        <th>Total Approved Cash Reservation</th>
                        <th>Total Purchases</th>
                        <th>Total Approved Purchases</th>
                        <th>Total Included Purchases</th>
                        <th>Remaining</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($logistic_cash_sq->rowCount()>0){
                        $row_count = 0;
                        while($logistic_cash_row = $logistic_cash_sq->fetch()){

                          $logistic_cashes_id = $logistic_cash_row['id'];

                          $cash_reservation_sq = $db->query(
                            "SELECT 
                              SUM(CASE WHEN currency='AFN' THEN reservation_amount ELSE 0 END) as total_cash_reservation_afn,
                              SUM(CASE WHEN currency='USD' THEN reservation_amount ELSE 0 END) as total_cash_reservation_usd,
                              SUM(CASE WHEN currency='IRT' THEN reservation_amount ELSE 0 END) as total_cash_reservation_irt,
                              SUM(CASE WHEN currency='AFN' AND is_approved='1' THEN reservation_amount ELSE 0 END) as total_approved_cash_reservation_afn,
                              SUM(CASE WHEN currency='USD' AND is_approved='1' THEN reservation_amount ELSE 0 END) as total_approved_cash_reservation_usd,
                              SUM(CASE WHEN currency='IRT' AND is_approved='1' THEN reservation_amount ELSE 0 END) as total_approved_cash_reservation_irt
                            FROM `cash_reservations` 
                            WHERE deleted='0' 
                            AND logistic_cashes_id = '$logistic_cashes_id' "
                          );
                          $cash_reservation_row = $cash_reservation_sq->fetch();

                          $purchase_sq = $db->query(
                            "SELECT 
                              SUM(CASE WHEN currency='AFN' THEN purchase_amount ELSE 0 END) as total_purchase_afn,
                              SUM(CASE WHEN currency='USD' THEN purchase_amount ELSE 0 END) as total_purchase_usd,
                              SUM(CASE WHEN currency='IRT' THEN purchase_amount ELSE 0 END) as total_purchase_irt,
                              SUM(CASE WHEN currency='AFN' AND is_approved='1' THEN purchase_amount ELSE 0 END) as total_approved_purchase_afn,
                              SUM(CASE WHEN currency='USD' AND is_approved='1' THEN purchase_amount ELSE 0 END) as total_approved_purchase_usd,
                              SUM(CASE WHEN currency='IRT' AND is_approved='1' THEN purchase_amount ELSE 0 END) as total_approved_purchase_irt,
                              SUM(CASE WHEN currency='AFN' AND is_approved='1' AND is_included='1' THEN purchase_amount ELSE 0 END) as total_included_purchase_afn,
                              SUM(CASE WHEN currency='USD' AND is_approved='1' AND is_included='1' THEN purchase_amount ELSE 0 END) as total_included_purchase_usd,
                              SUM(CASE WHEN currency='IRT' AND is_approved='1' AND is_included='1' THEN purchase_amount ELSE 0 END) as total_included_purchase_irt
                            FROM `purchases` 
                            WHERE deleted='0' 
                            AND logistic_cashes_id = '$logistic_cashes_id' "
                          );
                          $purchase_row = $purchase_sq->fetch();

                          ?>
                          <tr>
                            <th class="text-center"><?php echo ++$row_count; ?></th>
                            <td><?php echo $logistic_cash_row['name']; ?></td>
                            <td class="text-right">
                              <?php echo $cash_reservation_row['total_cash_reservation_afn'].' AFN'.'<br/>'; ?>
                              <?php echo $cash_reservation_row['total_cash_reservation_usd'].' USD'.'<br/>'; ?>
                              <?php echo $cash_reservation_row['total_cash_reservation_irt'].' IRT'.'<br/>'; ?>
                            </td>
                            <td class="text-right">
                              <?php echo $cash_reservation_row['total_approved_cash_reservation_afn'].' AFN'.'<br/>'; ?>
                              <?php echo $cash_reservation_row['total_approved_cash_reservation_usd'].' USD'.'<br/>'; ?>
                              <?php echo $cash_reservation_row['total_approved_cash_reservation_irt'].' IRT'.'<br/>'; ?>
                            </td>
                            <td class="text-right">
                              <?php echo $purchase_row['total_purchase_afn'].' AFN'.'<br/>'; ?>
                              <?php echo $purchase_row['total_purchase_usd'].' USD'.'<br/>'; ?>
                              <?php echo $purchase_row['total_purchase_irt'].' IRT'.'<br/>'; ?>
                            </td>
                            <td class="text-right">
                              <?php echo $purchase_row['total_approved_purchase_afn'].' AFN'.'<br/>'; ?>
                              <?php echo $purchase_row['total_approved_purchase_usd'].' USD'.'<br/>'; ?>
                              <?php echo $purchase_row['total_approved_purchase_irt'].' IRT'.'<br/>'; ?>
                            </td>
                            <td class="text-right">
                              <?php echo $purchase_row['total_included_purchase_afn'].' AFN'.'<br/>'; ?>
                              <?php echo $purchase_row['total_included_purchase_usd'].' USD'.'<br/>'; ?>
                              <?php echo $purchase_row['total_included_purchase_irt'].' IRT'.'<br/>'; ?>
                            </td>
                            <td class="text-right">
                              <?php echo ($cash_reservation_row['total_cash_reservation_afn']-$purchase_row['total_included_purchase_afn']).' AFN'.'<br/>'; ?>
                              <?php echo ($cash_reservation_row['total_cash_reservation_usd']-$purchase_row['total_included_purchase_usd']).' USD'.'<br/>'; ?>
                              <?php echo ($cash_reservation_row['total_cash_reservation_irt']-$purchase_row['total_included_purchase_irt']).' IRT'.'<br/>'; ?>
                            </td>
                          </tr>
                          <?php
                        }
                      }else
                      {
                        ?>
                        <tr>
                          <th class="text-center" colspan="100">No data to show</th>
                        </tr>
                        <?php
                      }
                      ?>
                      
                    </tbody>
                  </table>
                </div> <!-- end table-responsive-->

              </div> <!-- end card-box -->
            </div> <!-- end col -->


          </div>
        </div> <!-- container -->
      </div> <!-- content -->
      <!-- Footer Start -->
      <footer class="footer">
        <?php include("_footer.php"); ?>
      </footer>
      <!-- end Footer -->
    </div>
  </div>
  <!-- END wrapper -->
  <div class="rightbar-overlay"></div>
  <?php include("_script.php"); ?>
</body>
</html>
<?php include("_additional_elements.php"); ?>