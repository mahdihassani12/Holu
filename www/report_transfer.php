<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Transfer"];

  
  set_filtering_data('transfer_province');
  set_filtering_data('transfer_branch');
  set_filtering_data('transfer_date');

  set_pagination();

  $transfer_sq = $db->query("SELECT * FROM `transfers` WHERE deleted='0' $holu_filtering_data AND (to_province IN ($accessed_provinces) OR users_id='$holu_users_id') ORDER BY is_approved ASC, id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `transfers` WHERE deleted='0' $holu_filtering_data AND (to_province IN ($accessed_provinces) OR users_id='$holu_users_id')");
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
                  <?php echo get_table_header('fa fa-list', 'List of Transfers', $transfer_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php
                if(check_access("system_accessibility/request/report_transfer/filter_table")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_transfer.php', 'filter_table_report_transfer', 'general_lg', '0');"><i class="fa fa-filter"></i> Filter the Table</button>
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
                        <th>From Province</th>
                        <th>From Branch</th>
                        <th>To Province</th>
                        <th>To Branch</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Description</th>
                        <th>Is Approved</th>
                        <th>Approve Description</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($transfer_sq->rowCount()>0){
                        while($transfer_row = $transfer_sq->fetch()){
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo $transfer_row['from_province']; ?></td>
                            <td><?php echo $transfer_row['from_branch']; ?></td>
                            <td><?php echo $transfer_row['to_province']; ?></td>
                            <td><?php echo $transfer_row['to_branch']; ?></td>
                            <td><?php echo $transfer_row['transfer_date']; ?></td>
                            <td><?php echo $transfer_row['transfer_amount']; ?></td>
                            <td><?php echo $transfer_row['currency']; ?></td>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $transfer_row['description']; ?></p></td>
                            <td><?php echo ($transfer_row['is_approved']==0?"No":"Yes"); ?></td>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $transfer_row['approve_description']; ?></p></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  
                                  <?php
                                  if(check_access("system_accessibility/request/report_transfer/approve_transfer/")==1 AND $transfer_row['is_approved']==0){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_transfer.php', 'approve_transfer_form', 'general_lg', '<?php echo holu_encode($transfer_row['id']); ?>');"><i class="fas fa-circle"></i> Approve Transfer</a>
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
