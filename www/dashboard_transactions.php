<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Dashboards", "Transactions"];

  set_pagination();


  $dashboard_date_range_options = [
    'last_7_days' => 'Last 7 days',
    'last_30_days' => 'Last 30 days',
    'last_60_days' => 'Last 60 days',
    'last_90_days' => 'Last 90 days',
    'last_6_months' => 'Last 6 months',
    'last_year' => 'Last Year',
    'life_time' => 'Life time',
    'custom' => 'Custom',
  ];

  $dashboard_date_range = isset($_GET['date_range']) ? holu_escape($_GET['date_range']) : 'last_90_days';
  if(!array_key_exists($dashboard_date_range, $dashboard_date_range_options)){
    $dashboard_date_range = 'last_90_days';
  }

  $dashboard_custom_from_date = isset($_GET['from_date']) ? holu_escape($_GET['from_date']) : '';
  $dashboard_custom_to_date = isset($_GET['to_date']) ? holu_escape($_GET['to_date']) : '';
  $dashboard_from_date = '';
  $dashboard_to_date = '';
  $dashboard_today = date('Y-m-d');

  switch($dashboard_date_range){
    case 'last_7_days':{
      $dashboard_from_date = date('Y-m-d', strtotime('-6 days'));
      $dashboard_to_date = $dashboard_today;
    }break;

    case 'last_30_days':{
      $dashboard_from_date = date('Y-m-d', strtotime('-29 days'));
      $dashboard_to_date = $dashboard_today;
    }break;

    case 'last_60_days':{
      $dashboard_from_date = date('Y-m-d', strtotime('-59 days'));
      $dashboard_to_date = $dashboard_today;
    }break;

    case 'last_90_days':{
      $dashboard_from_date = date('Y-m-d', strtotime('-89 days'));
      $dashboard_to_date = $dashboard_today;
    }break;

    case 'last_6_months':{
      $dashboard_from_date = date('Y-m-d', strtotime('-6 months'));
      $dashboard_to_date = $dashboard_today;
    }break;

    case 'last_year':{
      $dashboard_from_date = date('Y-m-d', strtotime('-1 year'));
      $dashboard_to_date = $dashboard_today;
    }break;

    case 'custom':{
      $dashboard_from_date = $dashboard_custom_from_date;
      $dashboard_to_date = $dashboard_custom_to_date;
    }break;

    default:{
      $dashboard_from_date = '';
      $dashboard_to_date = '';
    }break;
  }

  $dashboard_date_filtering_data = '';
  $dashboard_excel_data = '&date_range='.$dashboard_date_range;
  if($dashboard_date_range=='custom'){
    $dashboard_excel_data .= '&from_date='.urlencode($dashboard_custom_from_date).'&to_date='.urlencode($dashboard_custom_to_date);
  }
  if($dashboard_from_date!=''){
    $dashboard_date_filtering_data .= " AND transaction_date>='".$dashboard_from_date."' ";
  }
  if($dashboard_to_date!=''){
    $dashboard_date_filtering_data .= " AND transaction_date<='".$dashboard_to_date."' ";
  }
  if($dashboard_from_date!='' || $dashboard_to_date!=''){
    $dashboard_date_range_label = $dashboard_date_range_options[$dashboard_date_range];
    $dashboard_date_label_dates = trim($dashboard_from_date.' - '.$dashboard_to_date, ' -');
    $holu_filtering_array[] = 'Date: '.$dashboard_date_range_label.($dashboard_date_label_dates!='' ? ' ('.$dashboard_date_label_dates.')' : '');
  }

  $income_access_condition = set_province_branch_portion('incomes.province', 'incomes.branch');
  $expense_access_condition = set_province_branch_portion('expenses.province', 'expenses.branch');
  $exchange_access_condition = set_province_branch_portion('exchanges.province', 'exchanges.branch');
  $transfer_from_access_condition = set_province_branch_portion('transfers.from_province', 'transfers.from_branch');
  $transfer_to_access_condition = set_province_branch_portion('transfers.to_province', 'transfers.to_branch');

  $transactions_query = "
    SELECT * FROM (
      SELECT 
        incomes.id AS transaction_id,
        'Income' AS transaction_type,
        incomes.province AS transaction_province,
        incomes.branch AS transaction_branch,
        incomes.income_date AS transaction_date,
        incomes.income_amount AS transaction_amount,
        incomes.currency AS transaction_currency,
        incomes.description AS transaction_description,
        incomes.users_id AS transaction_users_id,
        incomes.sub_categories_id AS transaction_sub_categories_id,
        incomes.check_number AS transaction_check_number
      FROM `incomes`
      WHERE incomes.deleted='0'
      AND $income_access_condition
      AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
      UNION ALL
      SELECT 
        expenses.id AS transaction_id,
        'Expense' AS transaction_type,
        expenses.province AS transaction_province,
        expenses.branch AS transaction_branch,
        expenses.expense_date AS transaction_date,
        expenses.expense_amount AS transaction_amount,
        expenses.currency AS transaction_currency,
        expenses.description AS transaction_description,
        expenses.users_id AS transaction_users_id,
        expenses.sub_categories_id AS transaction_sub_categories_id,
        expenses.check_number AS transaction_check_number
      FROM `expenses`
      WHERE expenses.deleted='0'
      AND $expense_access_condition
      AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
      UNION ALL
      SELECT 
        exchanges.id AS transaction_id,
        'Exchange' AS transaction_type,
        exchanges.province AS transaction_province,
        exchanges.branch AS transaction_branch,
        exchanges.exchange_date AS transaction_date,
        CONCAT(exchanges.from_amount, ' to ', exchanges.to_amount) AS transaction_amount,
        CONCAT(exchanges.from_currency, ' to ', exchanges.to_currency) AS transaction_currency,
        exchanges.description AS transaction_description,
        exchanges.users_id AS transaction_users_id,
        0 AS transaction_sub_categories_id,
        '' AS transaction_check_number
      FROM `exchanges`
      WHERE exchanges.deleted='0'
      AND $exchange_access_condition
      $accessed_sub_categories_exchange
      UNION ALL
      SELECT 
        transfers.id AS transaction_id,
        'Transfers' AS transaction_type,
        CONCAT(transfers.from_province, ' to ', transfers.to_province) AS transaction_province,
        CONCAT(transfers.from_branch, ' to ', transfers.to_branch) AS transaction_branch,
        transfers.transfer_date AS transaction_date,
        transfers.transfer_amount AS transaction_amount,
        transfers.currency AS transaction_currency,
        transfers.description AS transaction_description,
        transfers.users_id AS transaction_users_id,
        0 AS transaction_sub_categories_id,
        transfers.check_number AS transaction_check_number
      FROM `transfers`
      WHERE transfers.deleted='0'
      AND ((($transfer_from_access_condition) OR ($transfer_to_access_condition)) OR transfers.users_id='$holu_users_id')
      $accessed_sub_categories_transfer
    ) AS dashboard_transactions
  ";

  $transaction_sq = $db->query("$transactions_query WHERE 1 $dashboard_date_filtering_data ORDER BY transaction_date DESC, transaction_id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(transaction_id) as record FROM ($transactions_query WHERE 1 $dashboard_date_filtering_data) AS counted_dashboard_transactions");
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
          if(check_access("system_accessibility/dashboard/transactions/view_transactions/")==1){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <div class="dashboard-transactions-header">
                  <h4 class="header-title dashboard-transactions-title">
                    <i class="fa fa-list"></i>
                    Report of Transactions
                    <span class="dashboard-header-separator">•</span>
                    <span class="dashboard-header-context"><?php echo htmlspecialchars($dashboard_header_context, ENT_QUOTES, 'UTF-8'); ?></span>
                  </h4>

                  <div class="dashboard-header-actions">
                    <form id="dashboard_date_range_form" class="dashboard-date-range-form" method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" id="dashboard_date_range" name="date_range" value="<?php echo $dashboard_date_range; ?>">
                      <div class="dashboard-range-dropdown">
                        <button type="button" class="dashboard-range-toggle" onclick="dashboardTransactionsToggleDateRangeMenu();">
                          <span class="dashboard-range-dates"><?php echo htmlspecialchars($dashboard_range_button_dates, ENT_QUOTES, 'UTF-8'); ?></span>
                          <span class="dashboard-range-label"><?php echo htmlspecialchars($dashboard_date_range_label, ENT_QUOTES, 'UTF-8'); ?></span>
                          <i class="fa fa-chevron-down dashboard-range-caret"></i>
                        </button>
                        <div id="dashboard_date_range_menu" class="dashboard-range-menu">
                          <?php foreach($dashboard_date_range_options as $dashboard_date_range_key => $dashboard_date_range_value){ ?>
                          <button type="button" class="dashboard-range-menu-item" onclick="dashboardTransactionsSelectDateRange('<?php echo $dashboard_date_range_key; ?>');"><?php echo $dashboard_date_range_value; ?></button>
                          <?php } ?>
                        </div>
                      </div>
                      <div id="dashboard_custom_date_fields" class="dashboard-custom-date-fields <?php echo ($dashboard_date_range=='custom'?'':'hidden'); ?>">
                        <input type="text" name="from_date" class="form-control date_picker dashboard-custom-date-input" placeholder="From" value="<?php echo $dashboard_custom_from_date; ?>">
                        <input type="text" name="to_date" class="form-control date_picker dashboard-custom-date-input" placeholder="To" value="<?php echo $dashboard_custom_to_date; ?>">
                      </div>
                      <button type="submit" class="btn waves-effect waves-light dashboard-filter-btn"><i class="fa fa-filter"></i> Filter</button>
                    </form>

                    <a id="export_excel_btn" class="btn waves-effect waves-light dashboard-export-btn" href="controller_excel.php?excel_type=dashboard_transactions<?php echo $dashboard_excel_data; ?>"><i class="far fa-file-excel"></i> Export Excel</a>
                  </div>
                </div>

                <a id="export_excel_btn" href="controller_excel.php?excel_type=dashboard_transactions<?php echo $dashboard_excel_data; ?>"><button type="button" class="btn waves-effect waves-light adder_button"><i class="far fa-file-excel"></i> Export Excel</button></a>

                <form id="dashboard_date_range_form" class="dashboard-date-range-form" method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
                  <select id="dashboard_date_range" name="date_range" class="form-control dashboard-date-range-select" onchange="dashboardTransactionsDateRangeChanged();">
                    <?php foreach($dashboard_date_range_options as $dashboard_date_range_key => $dashboard_date_range_value){ ?>
                    <option value="<?php echo $dashboard_date_range_key; ?>" <?php echo ($dashboard_date_range==$dashboard_date_range_key?'selected':''); ?>><?php echo $dashboard_date_range_value; ?></option>
                    <?php } ?>
                  </select>
                  <div id="dashboard_custom_date_fields" class="dashboard-custom-date-fields <?php echo ($dashboard_date_range=='custom'?'':'hidden'); ?>">
                    <input type="text" name="from_date" class="form-control date_picker dashboard-custom-date-input" placeholder="From" value="<?php echo $dashboard_custom_from_date; ?>">
                    <input type="text" name="to_date" class="form-control date_picker dashboard-custom-date-input" placeholder="To" value="<?php echo $dashboard_custom_to_date; ?>">
                    <button type="submit" class="btn waves-effect waves-light adder_button dashboard-date-apply-btn"><i class="fa fa-filter"></i> Apply</button>
                  </div>
                </form>

              </div>
              <div class="card-box">
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Type</th>
                        <th>Province</th>
                        <th>Branch</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Check Number</th>
                        <th>Description</th>
                        <th>Created By</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($transaction_sq->rowCount()>0){
                        while($transaction_row = $transaction_sq->fetch()){
                          $transaction_category = '';
                          $transaction_sub_category = '';
                          if($transaction_row['transaction_sub_categories_id']!=0){
                            $transaction_category = get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $transaction_row['transaction_sub_categories_id']));
                            $transaction_sub_category = get_col('sub_categories', 'sub_category_name', 'id', $transaction_row['transaction_sub_categories_id']);
                          }
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo $transaction_row['transaction_type']; ?></td>
                            <td><?php echo $transaction_row['transaction_province']; ?></td>
                            <td><?php echo $transaction_row['transaction_branch']; ?></td>
                            <td><?php echo $transaction_category; ?></td>
                            <td><?php echo $transaction_sub_category; ?></td>
                            <td><?php echo $transaction_row['transaction_date']; ?></td>
                            <td><?php echo $transaction_row['transaction_amount']; ?></td>
                            <td><?php echo $transaction_row['transaction_currency']; ?></td>
                            <td><?php echo htmlspecialchars($transaction_row['transaction_check_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $transaction_row['transaction_description']; ?></p></td>
                            <td><?php echo get_col('users', 'username', 'id', $transaction_row['transaction_users_id']); ?></td>
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
  <script>
    function dashboardTransactionsDateRangeChanged(){
      var selectedDateRange = $('#dashboard_date_range').val();
      if(selectedDateRange==='custom'){
        $('#dashboard_custom_date_fields').removeClass('hidden');
      }else{
        $('#dashboard_date_range_form').submit();
      }
    }
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
