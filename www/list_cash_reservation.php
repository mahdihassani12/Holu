<?php

  include("../lib/_configuration.php");
  $holu_page_paths = ["Management", "List of Cash Reservations"];

  set_filtering_data('reservation_logistic_cashes_id');
  set_filtering_data('reservation_province');
  set_filtering_data('reservation_date');
  set_filtering_data('reservation_currency');
  set_filtering_data('reservation_amount');

  set_pagination();

  $cash_reservation_sq = $db->query("SELECT * FROM `cash_reservations` WHERE deleted='0' 
    $holu_filtering_data AND logistic_cashes_id IN ($accessed_logistic_cashes) 
    ORDER BY id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `cash_reservations` WHERE deleted='0' 
    $holu_filtering_data AND logistic_cashes_id IN ($accessed_logistic_cashes)");
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
                
                <h4 class="header-title">
                  <?php echo get_table_header('fa fa-list', 'List of Cash Reservations', $cash_reservation_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php
                if(check_access("system_accessibility/logistic/list_cash_reservation/filter_table")){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_cash_reservation.php', 'filter_table', 'general_lg', '0');"><i class="fa fa-filter"></i> Filter the Table</button>
                <?php
                }
                ?>

                <?php
                if(check_access("system_accessibility/logistic/list_cash_reservation/add_cash_reservation")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_cash_reservation.php', 'add_cash_reservation_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add New</button>
                <?php
                }
                ?>
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
                        <th>Added by</th>
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
                            <td><?php echo get_col('users', 'username', 'id', $cash_reservation_row['users_id']); ?></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  <?php
                                  if(check_access("system_accessibility/logistic/list_cash_reservation/edit_cash_reservation")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_cash_reservation.php', 'edit_cash_reservation_form', 'general_lg', '<?php echo $cash_reservation_row['id']; ?>');"><i class="fas fa-edit"></i> Edit Cash Reservation</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/logistic/list_cash_reservation/delete_cash_reservation")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_cash_reservation.php', 'delete_cash_reservation_form', 'general_md', '<?php echo $cash_reservation_row['id']; ?>');"><i class="fas fa-trash"></i> Delete Cash Reservation</a>
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