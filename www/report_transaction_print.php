<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Transaction Print"];

  $general_filtering_data = '';
  $income_filtering_data = "";
  $expense_filtering_data = "";
  $exchange_filtering_data = "";

  $province = "0";
  $from_date = "";
  $to_date = "";
  $transaction_type = "";
  $check_number = "";
 

  if(isset($_GET['province']) AND !empty($_GET['province'])){
    $province = $_GET['province'];
    $income_filtering_data .= " AND incomes.province='".$province."' ";
    $expense_filtering_data .= " AND expenses.province='".$province."' ";
    $exchange_filtering_data .= " AND exchanges.province='".$province."' ";
  }

  if(isset($_GET['from_date']) AND !empty($_GET['from_date'])){
    $from_date = $_GET['from_date'];
    $income_filtering_data .= " AND incomes.income_date>='".$from_date."' ";
    $expense_filtering_data .= " AND expenses.expense_date>='".$from_date."' ";
    $exchange_filtering_data .= " AND exchanges.exchange_date>='".$from_date."' ";
  }

  if(isset($_GET['to_date']) AND !empty($_GET['to_date'])){
    $to_date = $_GET['to_date'];
    $income_filtering_data .= " AND incomes.income_date<='".$to_date."' ";
    $expense_filtering_data .= " AND expenses.expense_date<='".$to_date."' ";
    $exchange_filtering_data .= " AND exchanges.exchange_date<='".$to_date."' ";
  }

  if(isset($_GET['transaction_type']) AND !empty($_GET['transaction_type'])){
    $transaction_type = $_GET['transaction_type'];
    if($transaction_type=="Income"){
      $income_filtering_data .= "";
      $expense_filtering_data .= " AND 0 ";
      $exchange_filtering_data .= " AND 0 ";
    }else if($transaction_type=="Expense"){
      $income_filtering_data .= " AND 0 ";
      $expense_filtering_data .= "";
      $exchange_filtering_data .= " AND 0 ";
    }else if($transaction_type=="Exchange"){
      $income_filtering_data .= " AND 0 ";
      $expense_filtering_data .= " AND 0 ";
      $exchange_filtering_data .= "";
    }
  }

  if(isset($_GET['check_number']) AND !empty($_GET['check_number'])){
    $check_number = $_GET['check_number'];
    $income_filtering_data .= " AND incomes.check_number='$check_number' ";
    $expense_filtering_data .= " AND expenses.check_number='$check_number' ";
    $exchange_filtering_data .= " AND 0 ";
  }

  set_pagination();

  $transaction_print_sq = $db->query(
    "SELECT invoices.*
    FROM `invoices`
    WHERE deleted='0' 
    AND (
      (
        invoices.reference_type = 'Income'
        AND invoices.reference_id IN (
          SELECT incomes.id 
          FROM `incomes` 
          WHERE incomes.province IN ($accessed_provinces) 
          AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
          $income_filtering_data
        )
      )
      OR (
        invoices.reference_type = 'Expense'
        AND invoices.reference_id IN (
          SELECT expenses.id 
          FROM `expenses` 
          WHERE expenses.province IN ($accessed_provinces) 
          AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
          $expense_filtering_data
        )
      )
      OR (
        invoices.reference_type = 'Exchange'
        AND invoices.reference_id IN (
          SELECT exchanges.id 
          FROM `exchanges` 
          WHERE exchanges.province IN ($accessed_provinces) 
          $accessed_sub_categories_exchange
          $exchange_filtering_data
        )
      )
    )
    $general_filtering_data
    ORDER BY invoices.insertion_date DESC, invoices.insertion_time DESC
     LIMIT $holu_to OFFSET $holu_from"
  );

  $Pagenation = $db->query(
    "SELECT count(id) as record 
    FROM `invoices`
    WHERE deleted='0' 
    AND (
      (
        invoices.reference_type = 'Income'
        AND invoices.reference_id IN (
          SELECT incomes.id 
          FROM `incomes` 
          WHERE incomes.province IN ($accessed_provinces) 
          AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
          $income_filtering_data
        )
      )
      OR (
        invoices.reference_type = 'Expense'
        AND invoices.reference_id IN (
          SELECT expenses.id 
          FROM `expenses` 
          WHERE expenses.province IN ($accessed_provinces) 
          AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
          $expense_filtering_data
        )
      )
      OR (
        invoices.reference_type = 'Exchange'
        AND invoices.reference_id IN (
          SELECT exchanges.id 
          FROM `exchanges` 
          WHERE exchanges.province IN ($accessed_provinces) 
          $accessed_sub_categories_exchange
          $exchange_filtering_data
        )
      )
    )
    $general_filtering_data"
  );
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

                <h4 class="header-title"><i class="fa fa-filter"></i> Filter Panel</h4>

              </div>
              <div class="card-box">
                <form class="form-horizontal" id="form_report_transaction_print" role="form" action="report_transaction_print.php" method="GET" enctype="multipart/form-data">

                  <input type="hidden" name="flag_request" id="flag_request" value="operation"/>

                  <input type="hidden" name="flag_operation" id="flag_operation" value="report_transaction_print"/>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="province">Province</label>
                    <div class="col-sm-6">
                      <select id="province" name="province" class="form-control" required>
                        <option selected hidden value="">Select an option</option>
                        <?php echo get_province_option($province); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="from_date">Date</label>
                    <div class="col-sm-3">
                      <input type="text" id="from_date" name="from_date" class="form-control date_picker" placeholder="From" value="<?php echo $from_date; ?>" required >
                    </div>
                    <div class="col-sm-3">
                      <input type="text" id="to_date" name="to_date" class="form-control date_picker" placeholder="To" value="<?php echo $to_date; ?>" required >
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="transaction_type">Transaction Type</label>
                    <div class="col-sm-6">
                      <select id="transaction_type" name="transaction_type" class="form-control">
                        <option selected value="">Select an option</option>
                        <?php echo get_transaction_type_option($transaction_type); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="check_number">Check Number</label>
                    <div class="col-sm-6">
                      <input type="text" id="check_number" name="check_number" class="form-control" placeholder="Type here..." value="<?php echo $check_number; ?>">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="submit"></label>
                    <div class="col-sm-6">
                      <button type="submit" id="submit" name="submit" class="btn success_btn waves-effect waves-light mr-1">Filter</button>
                      <button type="reset" class="btn btn-secondary waves-effect waves-light">Reset</button>
                    </div>
                  </div>

                </form>

              </div> <!-- end card-box -->
            </div> <!-- end col -->


          </div>

          
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">
                <h4 class="header-title">
                  <?php echo get_table_header('fa fa-list', 'Report of Transaction Edition', $transaction_print_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>
              </div>
              <div class="card-box">
                
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0" style="margin-bottom: 20px !important;">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Transaction Type</th>
                        <th>Printed By</th>
                        <th>Print Date</th>
                        <th>Print Time</th>
                        <th>Operation</th>
                      </tr>
                    </thead>
                    <tbody id="report_comment_tbody">
                      <?php
                        if($transaction_print_sq->rowCount()>0){
                          while($transaction_print_row = $transaction_print_sq->fetch()){

                            

                            switch ($transaction_print_row['reference_type']) {
                              case 'Income':{
                                $operation_destination = 'controller_income.php';
                              }break;

                              case 'Expense':{
                                $operation_destination = 'controller_expense.php';
                              }break;

                              case 'Exchange':{
                                $operation_destination = 'controller_exchange.php';
                              }break;
                              
                              default:{
                                $operation_destination = '';
                              }break;
                            }
                            

                            echo '
                              <tr>
                                <th class="text-center">'.($holu_count++).'</th>
                                <td>'.$transaction_print_row['reference_type'].'</td><td>'.get_col('users', 'username', 'id', $transaction_print_row['users_id']).'</td>
                                <td>'.$transaction_print_row['insertion_date'].'</td>
                                <td>'.$transaction_print_row['insertion_time'].'</td>
                                <td class="text-center">
                                  <div class="dropdown mt-1 opertation_container">
                                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                    <div class="dropdown-content dropdown-menu-right operation_list">
                                      <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \''.$operation_destination.'\', \'view_full_info\', \'general_lg\', \''.holu_encode($transaction_print_row['reference_id']).'\');"><i class="fas fa-info-circle"></i> View Full Info</a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                            ';
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