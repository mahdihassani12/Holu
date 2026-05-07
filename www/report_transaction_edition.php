<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Transaction Edition"];

  $general_filtering_data = '';
  $income_filtering_data = "";
  $expense_filtering_data = "";
  $exchange_filtering_data = "";
  $transfer_filtering_data = "";

  $province = "0";
  $branch = "";
  $start_edition_date = "";
  $end_edition_date = "";
  $transaction_type = "";
  $customer_name = "";
  $customer_id = "";
  $check_number = "";
  $markup = "";
  $unmark = "";
  $amount = "";
  $currency = "";
  $description = "";

  if(isset($_GET['province']) AND !empty($_GET['province'])){
    $province = $_GET['province'];
    $income_filtering_data .= " AND incomes.province='".$province."' ";
    $expense_filtering_data .= " AND expenses.province='".$province."' ";
    $exchange_filtering_data .= " AND exchanges.province='".$province."' ";
    $transfer_filtering_data .= " AND (transfers.from_province='".$province."' OR to_province='".$province."') ";
  }
  if(isset($_GET['branch']) AND !empty($_GET['branch'])){
    $branch = $_GET['branch'];
    $income_filtering_data .= " AND incomes.branch='".$branch."' ";
    $expense_filtering_data .= " AND expenses.branch='".$branch."' ";
    $exchange_filtering_data .= " AND exchanges.branch='".$branch."' ";
    $transfer_filtering_data .= " AND (transfers.from_branch='".$branch."' OR transfers.to_branch='".$branch."') ";
  }
  if(isset($_GET['start_edition_date']) AND !empty($_GET['start_edition_date'])){
    $start_edition_date = $_GET['start_edition_date'];
    $general_filtering_data .= " AND transaction_editions.insertion_date>='$start_edition_date'";
  }
  if(isset($_GET['end_edition_date']) AND !empty($_GET['end_edition_date'])){
    $end_edition_date = $_GET['end_edition_date'];
    $general_filtering_data .= " AND transaction_editions.insertion_date<='$end_edition_date'";
  }
  if(isset($_GET['transaction_type']) AND !empty($_GET['transaction_type'])){
    $transaction_type = $_GET['transaction_type'];
    if($transaction_type=="Income"){
      $income_filtering_data .= "";
      $expense_filtering_data .= " AND 0 ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
    }else if($transaction_type=="Expense"){
      $income_filtering_data .= " AND 0 ";
      $expense_filtering_data .= "";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
    }else if($transaction_type=="Exchange"){
      $income_filtering_data .= " AND 0 ";
      $expense_filtering_data .= " AND 0 ";
      $exchange_filtering_data .= "";
      $transfer_filtering_data .= " AND 0 ";
    }else if($transaction_type=="Transfer"){
      $income_filtering_data .= " AND 0 ";
      $expense_filtering_data .= " AND 0 ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= "";
    }
  }
  if(isset($_GET['customer_name']) AND !empty($_GET['customer_name'])){
    $customer_name = $_GET['customer_name'];
    $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' ) ";
    $expense_filtering_data .= " AND 0 ";
    $exchange_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";
  }

  if(isset($_GET['customer_id']) AND !empty($_GET['customer_id'])){
    $customer_id = $_GET['customer_id'];
    $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
    $expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
    $exchange_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";
  }
  if(isset($_GET['check_number']) AND !empty($_GET['check_number'])){
    $check_number = $_GET['check_number'];
    $income_filtering_data .= " AND incomes.check_number='".$check_number."' ";
    $expense_filtering_data .= " AND expenses.check_number='".$check_number."' ";
    $exchange_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";
  }
  if(isset($_GET['markup']) AND !empty($_GET['markup'])){
    $markup = $_GET['markup'];
    $general_filtering_data .= " AND transaction_editions.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transaction_Edition' AND markup_type = '$markup' AND deleted = '0') ";
  }
  if(isset($_GET['unmark']) AND !empty($_GET['unmark'])){
    $unmark = $_GET['unmark'];
    $general_filtering_data .= " AND ( transaction_editions.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transaction_Edition' AND markup_type = '$unmark' AND deleted = '1') OR transaction_editions.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Transaction_Edition' AND markup_type = '$unmark')) ";
  }
  if(isset($_GET['amount']) AND !empty($_GET['amount'])){
    $amount = $_GET['amount'];
    $income_filtering_data .= " AND incomes.income_amount='".$amount."' ";
    $expense_filtering_data .= " AND expenses.expense_amount='".$amount."' ";
    $exchange_filtering_data .= " AND (exchanges.from_amount='".$amount."' OR exchanges.to_amount='".$amount."') ";
    $transfer_filtering_data .= " AND transfers.transfer_amount='".$amount."' ";
  }
  if(isset($_GET['currency']) AND !empty($_GET['currency'])){
    $currency = $_GET['currency'];
    $income_filtering_data .= " AND incomes.currency='".$currency."' ";
    $expense_filtering_data .= " AND expenses.currency='".$currency."' ";
    $exchange_filtering_data .= "";
    $transfer_filtering_data .= " AND transfers.currency='".$currency."' ";
  }
  if(isset($_GET['description']) AND !empty($_GET['description'])){
    $description = $_GET['description'];
    $income_filtering_data .= " AND incomes.description LIKE '%".$description."%' ";
    $expense_filtering_data .= " AND expenses.description LIKE '%".$description."%' ";
    $exchange_filtering_data .= " AND exchanges.description LIKE '%".$description."%' ";
    $transfer_filtering_data .= " AND transfers.description LIKE '%".$description."%' ";
  }

  set_pagination();

  $transaction_edition_sq = $db->query(
    "SELECT transaction_editions.*
    FROM `transaction_editions`
    WHERE deleted='0' 
    AND (
      (
        transaction_editions.reference_type = 'Income'
        AND transaction_editions.reference_id IN (
          SELECT incomes.id 
          FROM `incomes` 
          WHERE incomes.province IN ($accessed_provinces) 
          AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
          $income_filtering_data
        )
      )
      OR (
        transaction_editions.reference_type = 'Expense'
        AND transaction_editions.reference_id IN (
          SELECT expenses.id 
          FROM `expenses` 
          WHERE expenses.province IN ($accessed_provinces) 
          AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
          $expense_filtering_data
        )
      )
      OR (
        transaction_editions.reference_type = 'Exchange'
        AND transaction_editions.reference_id IN (
          SELECT exchanges.id 
          FROM `exchanges` 
          WHERE exchanges.province IN ($accessed_provinces) 
          $accessed_sub_categories_exchange
          $exchange_filtering_data
        )
      )
      OR (
        AND transaction_editions.reference_id IN (
        )
      )
      OR (
        transaction_editions.reference_type = 'Transfer'
        AND transaction_editions.reference_id IN (
          SELECT transfers.id 
          FROM `transfers` 
          WHERE (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
          AND transfers.is_approved='1'
          $accessed_sub_categories_transfer
          $transfer_filtering_data
        )
      )
    )
    $general_filtering_data
    ORDER BY transaction_editions.insertion_date DESC, transaction_editions.insertion_time DESC
     LIMIT $holu_to OFFSET $holu_from"
  );

  $Pagenation = $db->query(
    "SELECT count(id) as record 
    FROM `transaction_editions`
    WHERE deleted='0' 
    AND (
      (
        transaction_editions.reference_type = 'Income'
        AND transaction_editions.reference_id IN (
          SELECT incomes.id 
          FROM `incomes` 
          WHERE incomes.province IN ($accessed_provinces) 
          AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
          $income_filtering_data
        )
      )
      OR (
        transaction_editions.reference_type = 'Expense'
        AND transaction_editions.reference_id IN (
          SELECT expenses.id 
          FROM `expenses` 
          WHERE expenses.province IN ($accessed_provinces) 
          AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
          $expense_filtering_data
        )
      )
      OR (
        transaction_editions.reference_type = 'Exchange'
        AND transaction_editions.reference_id IN (
          SELECT exchanges.id 
          FROM `exchanges` 
          WHERE exchanges.province IN ($accessed_provinces) 
          $accessed_sub_categories_exchange
          $exchange_filtering_data
        )
      )
      OR (
        AND transaction_editions.reference_id IN (
        )
      )
      OR (
        transaction_editions.reference_type = 'Transfer'
        AND transaction_editions.reference_id IN (
          SELECT transfers.id 
          FROM `transfers` 
          WHERE (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
          AND transfers.is_approved='1'
          $accessed_sub_categories_transfer
          $transfer_filtering_data
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
                <form class="form-horizontal" id="form_report_transaction_edition" role="form" action="report_transaction_edition.php" method="GET" enctype="multipart/form-data">

                  <input type="hidden" name="flag_request" id="flag_request" value="operation"/>

                  <input type="hidden" name="flag_operation" id="flag_operation" value="report_transaction_edition"/>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="province">Province</label>
                    <div class="col-sm-6">
                      <select id="province" name="province" class="form-control" required data-branch-target="branch" data-branch-value="<?php echo $branch; ?>" onchange="get_branch_option(this.value, this.getAttribute('data-branch-value') || '0', this.getAttribute('data-branch-target') || 'branch', this); this.setAttribute('data-branch-value', '0');">
                        <option selected hidden value="">Select an option</option>
                        <?php echo get_province_option($province); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="branch">Branch</label>
                    <div class="col-sm-6">
                      <select id="branch" name="branch" class="form-control">
                        <?php echo get_branch_option($province, $branch); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="start_edition_date">Edition Date</label>
                    <div class="col-sm-3">
                      <input type="text" id="start_edition_date" name="start_edition_date" class="form-control date_picker" placeholder="Type here..." value="<?php echo $start_edition_date; ?>">
                    </div>
                    <div class="col-sm-3">
                      <input type="text" id="end_edition_date" name="end_edition_date" class="form-control date_picker" placeholder="Type here..." value="<?php echo $end_edition_date; ?>">
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
                    <label class="col-sm-3 col-form-label" for="customer_name">Customer Name</label>
                    <div class="col-sm-6">
                      <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Type here..." value="<?php echo $customer_name; ?>">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="customer_id">Customer ID</label>
                    <div class="col-sm-6">
                      <input type="text" id="customer_id" name="customer_id" class="form-control" placeholder="Type here..." value="<?php echo $customer_id; ?>">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="check_number">Check Number</label>
                    <div class="col-sm-6">
                      <input type="text" id="check_number" name="check_number" class="form-control" placeholder="Type here..." value="<?php echo $check_number; ?>">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="markup">Markup</label>
                    <div class="col-sm-6">
                      <select id="markup" name="markup" class="form-control">
                        <?php echo get_markup_option("system_accessibility/report/report_transaction/", $markup); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="unmark">Unmark</label>
                    <div class="col-sm-6">
                      <select id="unmark" name="unmark" class="form-control">
                        <?php echo get_markup_option("system_accessibility/report/report_transaction/", $unmark); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="amount">Amount</label>
                    <div class="col-sm-6">
                      <input type="text" id="amount" name="amount" class="form-control" placeholder="Type here..." value="<?php echo $amount; ?>">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="currency">Currency</label>
                    <div class="col-sm-6">
                      <select id="currency" name="currency" class="form-control">
                        <option selected value="">Select an option</option>
                        <?php echo get_currency_option($currency); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="description">Description</label>
                    <div class="col-sm-6">
                      <input type="text" id="description" name="description" class="form-control" placeholder="Type here..." value="<?php echo $description; ?>">
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
                  <?php echo get_table_header('fa fa-list', 'Report of Transaction Edition', $transaction_edition_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>
              </div>
              <div class="card-box">
                
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0" style="margin-bottom: 20px !important;">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Transaction Type</th>
                        <th>Edited By</th>
                        <th>Edition Date</th>
                        <th>Edition Time</th>
                        <th>Data</th>
                        <th>Markups</th>
                        <th>Operation</th>
                      </tr>
                    </thead>
                    <tbody id="report_comment_tbody">
                      <?php
                        if($transaction_edition_sq->rowCount()>0){
                          while($transaction_edition_row = $transaction_edition_sq->fetch()){

                            $old_data_array = explode('###', $transaction_edition_row['old_data']);
                            $new_data_array = explode('###', $transaction_edition_row['new_data']);

                            $old_data = '';
                            $new_data = '';

                            if(sizeof($old_data_array)>0){
                              $old_data .= '<h4>Old Data</h4>';
                              foreach($old_data_array as $old_data_item){
                                $old_data_item_array = explode('=>', $old_data_item);
                                if(sizeof($old_data_item_array)>1){
                                  switch (str_replace('`', '', $old_data_item_array[0])) {
                                    case 'Sub Category':{
                                      $old_data .= '<b>'.str_replace('`', '', $old_data_item_array[0]).':</b> '.get_col('sub_categories', 'sub_category_name', 'id', str_replace('`', '', $old_data_item_array[1])).'<br/>';
                                    }break;

                                    case 'Logistic Cash':{
                                      $old_data .= '<b>'.str_replace('`', '', $old_data_item_array[0]).':</b> '.get_col('logistic_cashes', 'name', 'id', str_replace('`', '', $old_data_item_array[1])).'<br/>';
                                    }break;
                                    
                                    default:{
                                      $old_data .= '<b>'.str_replace('`', '', $old_data_item_array[0]).':</b> '.str_replace('`', '', $old_data_item_array[1]).'<br/>';
                                    }break;
                                  }
                                    
                                }
                                
                              }
                            }

                            if(sizeof($new_data_array)>0){
                              $new_data .= '<h4>New Data</h4>';
                              foreach($new_data_array as $new_data_item){
                                $new_data_item_array = explode('=>', $new_data_item);
                                if(sizeof($new_data_item_array)>1){
                                  switch (str_replace('`', '', $new_data_item_array[0])) {
                                    case 'Sub Category':{
                                      $new_data .= '<b>'.str_replace('`', '', $new_data_item_array[0]).':</b> '.get_col('sub_categories', 'sub_category_name', 'id', str_replace('`', '', $new_data_item_array[1])).'<br/>';
                                    }break;

                                    case 'Logistic Cash':{
                                      $new_data .= '<b>'.str_replace('`', '', $new_data_item_array[0]).':</b> '.get_col('logistic_cashes', 'name', 'id', str_replace('`', '', $new_data_item_array[1])).'<br/>';
                                    }break;
                                    
                                    default:{
                                      $new_data .= '<b>'.str_replace('`', '', $new_data_item_array[0]).':</b> '.str_replace('`', '', $new_data_item_array[1]).'<br/>';
                                    }break;
                                  }
                                }
                                
                              }
                            }

                            switch ($transaction_edition_row['reference_type']) {
                              case 'Income':{
                                $operation_destination = 'controller_income.php';
                              }break;

                              case 'Expense':{
                                $operation_destination = 'controller_expense.php';
                              }break;

                              case 'Exchange':{
                                $operation_destination = 'controller_exchange.php';
                              }break;

                              case 'Transfer':{
                                $operation_destination = 'controller_transfer.php';
                              }break;
                              
                              default:{
                                $operation_destination = '';
                              }break;
                            }
                            

                            echo '
                              <tr>
                                <th class="text-center">'.($holu_count++).'</th>
                                <td>'.$transaction_edition_row['reference_type'].'</td><td>'.get_col('users', 'username', 'id', $transaction_edition_row['users_id']).'</td>
                                <td>'.$transaction_edition_row['insertion_date'].'</td>
                                <td>'.$transaction_edition_row['insertion_time'].'</td>
                                <td class="text-center">
                                  <a type="button"
                                    tabindex="0"
                                    class="badge badge-success" 
                                    role="button" 
                                    data-html="true" 
                                    data-toggle="popover" 
                                    data-trigger="focus" 
                                    title="<b>Data</b>" 
                                    data-content="<div>'.$old_data.'</div><hr/><div>'.$new_data.'</div>">i</a>
                                </td>
                                <td class="text-center" id="markupsTransaction_Edition'.$transaction_edition_row['id'].'">
                                  '.get_markups('system_accessibility/report/report_transaction_edition/', 'Transaction_Edition', $transaction_edition_row['id'], $transaction_edition_row['tms_markup'], $transaction_edition_row['qb_markup'], $transaction_edition_row['sib_markup'], $transaction_edition_row['ad_markup']).'
                                </td>
                                <td class="text-center">
                                  <div class="dropdown mt-1 opertation_container">
                                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                    <div class="dropdown-content dropdown-menu-right operation_list">
                                      <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \''.$operation_destination.'\', \'view_full_info\', \'general_lg\', \''.holu_encode($transaction_edition_row['reference_id']).'\');"><i class="fas fa-info-circle"></i> View Full Info</a>
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
  <script type="text/javascript">
    $(function(){
      $("[data-toggle=popover]").popover();
    }); 
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
