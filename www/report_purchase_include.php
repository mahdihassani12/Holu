<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Requests", "Report of Purchase"];

  set_filtering_data('purchase_province');
  set_filtering_data('purchase_date');
  set_filtering_data('purchase_categories_id');
  set_filtering_data('purchase_sub_categories_id');
  set_filtering_data('purchase_is_printed');
  set_filtering_data('purchase_currency');
  set_filtering_data('purchase_amount');
  set_filtering_data('purchase_users_id');

  set_pagination();

  $purchase_sq = $db->query("SELECT * FROM `purchases` WHERE deleted='0' AND is_approved='1' $holu_filtering_data AND province IN ($accessed_provinces) AND logistic_cashes_id IN ($accessed_logistic_cashes) ORDER BY is_included ASC, id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `purchases` WHERE deleted='0' AND is_approved='1' $holu_filtering_data AND province IN ($accessed_provinces) AND logistic_cashes_id IN ($accessed_logistic_cashes)");
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
                  <?php echo get_table_header('fa fa-list', 'List of Purchases', $purchase_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php
                if(check_access("system_accessibility/request/report_purchase_include/filter_table")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_purchase.php', 'filter_table_purchase_include', 'general_lg', '0');"><i class="fa fa-filter"></i> Filter the Table</button>
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
                        <th>Province</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Description</th>
                        <th>Is Approved</th>
                        <th>Approve Description</th>
                        <th>Is Included</th>
                        <th>include Description</th>
                        <th>Added By</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($purchase_sq->rowCount()>0){
                        while($purchase_row = $purchase_sq->fetch()){
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo get_col('logistic_cashes', 'name', 'id', $purchase_row['logistic_cashes_id']); ?></td>
                            <td><?php echo $purchase_row['province']; ?></td>
                            <td><?php echo get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $purchase_row['sub_categories_id'])); ?></td>
                            <td><?php echo get_col('sub_categories', 'sub_category_name', 'id', $purchase_row['sub_categories_id']); ?></td>
                            <td><?php echo $purchase_row['purchase_date']; ?></td>
                            <td><?php echo $purchase_row['purchase_amount']; ?></td>
                            <td><?php echo $purchase_row['currency']; ?></td>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $purchase_row['description']; ?></p></td>
                            <td><?php echo ($purchase_row['is_approved']==0?"No":"Yes"); ?></td>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $purchase_row['approve_description']; ?></p></td>
                            <td><?php echo ($purchase_row['is_included']==0?"No":"Yes"); ?></td>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $purchase_row['include_description']; ?></p></td>
                            <td class="text-center">
                              <?php echo get_col('users', 'username', 'id', $purchase_row['users_id']); ?>
                            </td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  
                                  <?php
                                  if(check_access("system_accessibility/request/report_purchase_include/include_purchase/")==1 AND $purchase_row['is_included']==0){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_purchase.php', 'include_purchase_form', 'general_lg', '<?php echo holu_encode($purchase_row['id']); ?>');"><i class="fas fa-circle"></i> Include Purchase</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/request/report_purchase_include/view_attachment")){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_purchase.php', 'view_attachment', 'general_lg', '<?php echo holu_encode($purchase_row['id']); ?>');"><i class="far fa-file-image"></i> View Attachment</a>
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