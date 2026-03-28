<?php

  include("../lib/_configuration.php");
  $holu_page_paths = ["Requests", "Report of Cash Reservations"];

  set_pagination();

  $cash_reservation_sq = $db->query("SELECT * FROM `cash_reservations` WHERE deleted='0' AND logistic_cashes_id IN ($accessed_logistic_cashes) ORDER BY is_approved ASC, id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `cash_reservations` WHERE deleted='0' AND logistic_cashes_id IN ($accessed_logistic_cashes)");
  extract($Pagenation->fetch());

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
                <h4 class="header-title"><i class="fa fa-list"></i> List of Cash Reservations</h4>

                
              </div>
              <div class="card-box">
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Logistic Cash</th>
                        <th>Reservation Date</th>
                        <th>Reservation Amount</th>
                        <th>Currency</th>
                        <th>Description</th>
                        <th>Is Approved</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($cash_reservation_sq->rowCount()>0){
                        while($cash_reservation_row = $cash_reservation_sq->fetch()){
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo get_col('logistic_cashes', 'name', 'id', $cash_reservation_row['logistic_cashes_id']); ?></td>
                            <td><?php echo $cash_reservation_row['reservation_date']; ?></td>
                            <td><?php echo $cash_reservation_row['reservation_amount']; ?></td>
                            <td><?php echo $cash_reservation_row['currency']; ?></td>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $cash_reservation_row['description']; ?></p></td>
                            <td><?php echo ($cash_reservation_row['is_approved']==0?"No":"Yes"); ?></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">

                                  <?php
                                  if(check_access("system_accessibility/request/report_cash_reservation/approve_cash_reservation")==1 AND $cash_reservation_row['is_approved']==0){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_cash_reservation.php', 'approve_cash_reservation_form', 'general_md', '<?php echo $cash_reservation_row['id']; ?>');"><i class="fas fa-circle"></i> Approve Cash Reservation</a>
                                  <?php
                                  }
                                  ?>

                                </div>
                              </div>
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
                  <div style="text-align: center;">
                    <?php
                      set_page_numbers();
                    ?>
                  </div>
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