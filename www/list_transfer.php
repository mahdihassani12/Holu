<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Transaction", "Transfer"];

  set_filtering_data('transfer_province');
  set_filtering_data('transfer_date');

  set_pagination();

  

  $transfer_sq = $db->query("SELECT * FROM `transfers` WHERE deleted='0' $holu_filtering_data AND (from_province IN ($accessed_provinces) OR to_province IN ($accessed_provinces)) ORDER BY id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `transfers` WHERE deleted='0' $holu_filtering_data AND (from_province IN ($accessed_provinces) OR to_province IN ($accessed_provinces))");
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


          <?php
          if(check_access("system_accessibility/transaction/transfer/view_transfer")==1){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">
                  <?php echo get_table_header('fa fa-list', 'List of Transfers', $transfer_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php
                if(check_access("system_accessibility/transaction/transfer/filter_table")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_transfer.php', 'filter_table', 'general_lg', '0');"><i class="fa fa-filter"></i> Filter the Table</button>
                <?php
                }
                ?>

                <?php
                if(check_access("system_accessibility/transaction/transfer/add_transfer")){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_transfer.php', 'add_transfer_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add Transfer</button>
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
                        <th>Added By</th>
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
                            <td><?php echo get_col('users', 'username', 'id', $transfer_row['users_id']); ?></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  
                                  <?php
                                  if(check_access("system_accessibility/transaction/transfer/edit_transfer")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_transfer.php', 'edit_transfer_form', 'general_lg', '<?php echo holu_encode($transfer_row['id']); ?>');"><i class="fas fa-edit"></i> Edit Transfer</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/transaction/transfer/delete_transfer")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_transfer.php', 'delete_transfer_form', 'general_md', '<?php echo holu_encode($transfer_row['id']); ?>');"><i class="fas fa-trash"></i> Delete Transfer</a>
                                  <?php
                                  }
                                  ?>

                                  <!-- Begin Item __ View Attachment __ Added By Mohsen __ 2021-04-04 -->
                                  <?php
                                  if(check_access("system_accessibility/transaction/transfer/view_attachment")){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_transfer.php', 'view_attachment', 'general_lg', '<?php echo holu_encode($transfer_row['id']); ?>');"><i class="far fa-file-image"></i> View Attachment</a>
                                  <?php
                                  }
                                  ?>
                                  <!-- End Item -->

                                  <!-- Begin Item __ Print Voucher __ Added By Mohsen __ 2021-04-04 -->
                                  <?php
                                  if(check_access("system_accessibility/transaction/transfer/print_voucher")==1){
                                  ?>
                                  <a class="dropdown-item" href="print_voucher.php?transfer_id=<?php echo holu_encode($transfer_row['id']); ?>" target=" _ "><i class="fas fa-print"></i> Print Voucher</a>
                                  <?php
                                  }
                                  ?>
                                  <!-- End Item -->

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
          <?php
          }
          ?>
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
