<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Transaction", "Expense"];

  set_filtering_data('expense_province');
  set_filtering_data('expense_branch');
  set_filtering_data('expense_date');
  set_filtering_data('expense_categories_id');
  set_filtering_data('expense_sub_categories_id');
  set_filtering_data('expense_is_printed');
  set_filtering_data('expense_currency');
  set_filtering_data('expense_amount');

  set_pagination();
  $expense_access_condition = set_province_branch_portion('province', 'branch');

  $expense_sq = $db->query("SELECT * FROM `expenses` WHERE deleted='0' $holu_filtering_data AND $expense_access_condition AND sub_categories_id IN ($accessed_sub_categories_expense) ORDER BY id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `expenses` WHERE deleted='0' $holu_filtering_data AND $expense_access_condition AND sub_categories_id IN ($accessed_sub_categories_expense)");
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
          if(check_access("system_accessibility/transaction/expense/view_expense")==1){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">
                  <?php echo get_table_header('fa fa-list', 'List of Expenses', $expense_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php
                if(check_access("system_accessibility/transaction/expense/filter_table")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_expense.php', 'filter_table', 'general_lg', '0');"><i class="fa fa-filter"></i> Filter the Table</button>
                <?php
                }
                ?>

                <?php
                if(check_access("system_accessibility/transaction/expense/add_expense")){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_expense.php', 'add_expense_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add Expense</button>
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
                        <th>Created By</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($expense_sq->rowCount()>0){
                        while($expense_row = $expense_sq->fetch()){

                          $check_number_container = '';
                          if($expense_row['check_number']!=""){
                            $check_number_container = '
                            <p>'.$expense_row['check_number'].'</p>
                            ';
                          }
                          if(check_access("system_accessibility/transaction/expense/edit_check_number/")){
                            $check_number_container .= '
                              <span onclick="edit_check_number(\''.$expense_row['id'].'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
                            ';
                          }

                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo $expense_row['province']; ?></td>
                            <td><?php echo $expense_row['branch']; ?></td>
                            <td><?php echo get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $expense_row['sub_categories_id'])); ?></td>
                            <td><?php echo get_col('sub_categories', 'sub_category_name', 'id', $expense_row['sub_categories_id']); ?></td>
                            <td><?php echo $expense_row['expense_date']; ?></td>
                            <td><?php echo $expense_row['expense_amount']; ?></td>
                            <td><?php echo $expense_row['currency']; ?></td>
                            <td class="text-center" 
                                id="check_number_containerExpense
                                <?php echo $expense_row['id']; ?>">
                                <?php echo $check_number_container; ?>
                            </td>
                            <td class="text-right">
                                <p lang="fa" dir="rtl">
                                  <?php echo $expense_row['description']; ?>
                                </p>
                            </td>
                            <td class="text-center">
                                <p>
                                <?php
                                  $data = $expense_row['additional_informations'];
                                  echo print_ai_labels(!empty($data) ? json_decode($data) : null);
                                ?>
                                </p>
                            </td>
                            <td><?php echo get_col('users', 'username', 'id', $expense_row['users_id']); ?></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  
                                  <?php
                                  if(check_access("system_accessibility/transaction/expense/edit_expense")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_expense.php', 'edit_expense_form', 'general_lg', '<?php echo holu_encode($expense_row['id']); ?>');"><i class="fas fa-edit"></i> Edit Expense</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/transaction/expense/delete_expense")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_expense.php', 'delete_expense_form', 'general_md', '<?php echo holu_encode($expense_row['id']); ?>');"><i class="fas fa-trash"></i> Delete Expense</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/transaction/expense/add_attachment/")){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_expense.php', 'add_attachment_form', 'general_lg', '<?php echo holu_encode($expense_row['id']); ?>');"><i class="fas fa-file-import"></i> Add Attachment</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/transaction/expense/edit_attachment")){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_expense.php', 'view_attachment', 'general_lg', '<?php echo holu_encode($expense_row['id']); ?>');"><i class="far fa-file-image"></i> View Attachment</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/transaction/expense/print_voucher/")){
                                  ?>
                                  <a class="dropdown-item" href="print_voucher.php?expenses_id=<?php echo holu_encode($expense_row['id']); ?>" target=" _ "><i class="fas fa-print"></i> Print Voucher</a>
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
