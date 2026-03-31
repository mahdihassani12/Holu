<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Transaction", "Income"];

  set_filtering_data('income_province');
  set_filtering_data('income_branch');
  set_filtering_data('income_date');
  set_filtering_data('income_categories_id');
  set_filtering_data('income_sub_categories_id');
  set_filtering_data('income_customer_name');
  set_filtering_data('income_customer_id');
  set_filtering_data('income_currency');
  set_filtering_data('income_amount');

  set_pagination();

  $income_sq = $db->query("SELECT * FROM `incomes` WHERE deleted='0' $holu_filtering_data AND province IN ($accessed_provinces) AND sub_categories_id IN ($accessed_sub_categories_income) ORDER BY id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `incomes` WHERE deleted='0' $holu_filtering_data AND province IN ($accessed_provinces) AND sub_categories_id IN ($accessed_sub_categories_income)");
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
          if(check_access("system_accessibility/transaction/income/view_income")==1){
            ?>
            <div class="row">
              <div class="col-lg-12">
                <div class="card-box card-box-header">

                  <h4 class="header-title">
                    <?php echo get_table_header('fa fa-list', 'List of Incomes', $income_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                  </h4>

                  <?php
                  if(check_access("system_accessibility/transaction/income/filter_table")){
                  ?>
                  <button type="button" class="btn waves-effect waves-light adder_button" 
                          onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>',
                          'controller_income.php', 'filter_table', 'general_lg', '0');">
                    <i class="fa fa-filter"></i> Filter the Table
                  </button>
                  <?php
                  }
                  ?>

                  <?php
                  if(check_access("system_accessibility/transaction/income/add_income")){
                  ?>
                  <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_income.php', 'add_income_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add Income</button>
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
                          <th>Province</th>
                          <th>Branch</th>
                          <th>Category</th>
                          <th>Sub Category</th>
                          <th>Date</th>
                          <th>Amount</th>
                          <th>Currency</th>
                          <th>Check Number</th>
                          <th>Description</th>
                          <th>Additional Information</th>
                          <th>Added By</th>
                          <th class="text-center">Operation</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if($income_sq->rowCount()>0){
                          while($income_row = $income_sq->fetch()){

                            ?>
                            <tr>
                              <th class="text-center"><?php echo $holu_count++; ?></th>
                              <td><?php echo $income_row['province']; ?></td>
                              <td><?php echo $income_row['branch']; ?></td>
                              <td><?php echo get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $income_row['sub_categories_id'])); ?></td>
                              <td><?php echo get_col('sub_categories', 'sub_category_name', 'id', $income_row['sub_categories_id']); ?></td>
                              <td><?php echo $income_row['income_date']; ?></td>
                              <td><?php echo $income_row['income_amount']; ?></td>
                              <td><?php echo $income_row['currency']; ?></td>
                              <td><?php echo $income_row['check_number']; ?></td>
                              <td class="text-right"><p lang="fa" dir="rtl"><?php echo $income_row['description']; ?></p></td>
                              <td class="text-center"><?php echo print_ai_labels(json_decode($income_row['additional_informations'] ?? '{}')); ?></p></td>
                              <td><?php echo get_col('users', 'username', 'id', $income_row['users_id']); ?></td>
                              <td class="text-center">
                                <div class="dropdown mt-1 opertation_container">
                                  <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                  <div class="dropdown-content dropdown-menu-right operation_list">
                                    
                                    <?php
                                    if(check_access("system_accessibility/transaction/income/edit_income")){
                                    ?>
                                    <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_income.php', 'edit_income_form', 'general_lg', '<?php echo holu_encode($income_row['id']); ?>');"><i class="fas fa-edit"></i> Edit Income</a>
                                    <?php
                                    }
                                    ?>
                                    
                                    <?php
                                    if(check_access("system_accessibility/transaction/income/delete_income")){
                                    ?>
                                    <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_income.php', 'delete_income_form', 'general_md', '<?php echo holu_encode($income_row['id']); ?>');"><i class="fas fa-trash"></i> Delete Income</a>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if(check_access("system_accessibility/transaction/income/add_attachment/")){
                                    ?>
                                    <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_income.php', 'add_attachment_form', 'general_lg', '<?php echo holu_encode($income_row['id']); ?>');"><i class="fas fa-file-import"></i> Add Attachment</a>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if(check_access("system_accessibility/transaction/income/edit_attachment")){
                                    ?>
                                    <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_income.php', 'view_attachment', 'general_lg', '<?php echo holu_encode($income_row['id']); ?>');"><i class="far fa-file-image"></i> View Attachment</a>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if(check_access("system_accessibility/transaction/income/print_receipt/")){
                                    ?>
                                    <a class="dropdown-item" href="print_receipt.php?incomes_id=<?php echo holu_encode($income_row['id']); ?>" target=" _ "><i class="fas fa-print"></i> Print Receipt</a>
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
