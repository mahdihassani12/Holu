<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Dashboards", "Transactions"];

  set_pagination();


  $dashboard_date_range_data = resolve_dashboard_transaction_date_range();
  $dashboard_date_range_options = $dashboard_date_range_data['options'];
  $dashboard_date_range = $dashboard_date_range_data['selected'];
  $dashboard_custom_from_date = $dashboard_date_range_data['custom_from_date'];
  $dashboard_custom_to_date = $dashboard_date_range_data['custom_to_date'];
  $dashboard_date_filtering_data = $dashboard_date_range_data['sql_filter'];
  $dashboard_excel_data = $dashboard_date_range_data['query_string'];
  $dashboard_date_range_display = $dashboard_date_range_data['display_date_range'];
  $dashboard_date_range_label = $dashboard_date_range_data['label'];
  $holu_filtering_array[] = $dashboard_date_range_data['filter_label'];


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
              <div class="card-box card-box-header dashboard-transactions-header">
                <h4 class="header-title">
                  <?php echo get_table_header('fa fa-list', 'Report of Transactions • '.$dashboard_date_range_display, $transaction_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <a id="dashboard_export_excel_btn" href="controller_excel.php?excel_type=dashboard_transactions<?php echo $dashboard_excel_data; ?>"><button type="button" class="btn waves-effect waves-light adder_button"><i class="far fa-file-excel"></i> Export Excel</button></a>

                <button type="button" class="btn waves-effect waves-light adder_button"><i class="fa fa-filter"></i> Filter</button>

                <div class="dropdown dashboard-date-range-dropdown">
                  <button class="btn dropdown-toggle waves-effect waves-light dashboard-date-range-toggle" type="button" id="dashboardDateRangeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="dashboard-date-range-dates"><?php echo $dashboard_date_range_display; ?></span>
                    <span class="dashboard-date-range-label"><?php echo $dashboard_date_range_label; ?></span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right dashboard-date-range-menu" aria-labelledby="dashboardDateRangeDropdown">
                    <?php
                    foreach($dashboard_date_range_options as $dashboard_date_range_key => $dashboard_date_range_option){
                      if($dashboard_date_range_key!='custom'){
                        ?>
                        <a class="dropdown-item <?php echo $dashboard_date_range==$dashboard_date_range_key ? 'active' : ''; ?>" href="dashboard_transactions.php?date_range=<?php echo $dashboard_date_range_key; ?>"><?php echo $dashboard_date_range_option; ?></a>
                        <?php
                      }
                    }
                    ?>
                    <a class="dropdown-item <?php echo $dashboard_date_range=='custom' ? 'active' : ''; ?>" href="#" onclick="event.preventDefault(); document.getElementById('dashboard_custom_date_range').classList.toggle('d-none');">Custom</a>
                    <div id="dashboard_custom_date_range" class="dashboard-custom-date-range <?php echo $dashboard_date_range=='custom' ? '' : 'd-none'; ?>" onclick="event.stopPropagation();">
                      <label for="dashboard_from_date">Start date</label>
                      <input type="date" class="form-control form-control-sm" id="dashboard_from_date" value="<?php echo $dashboard_custom_from_date; ?>">
                      <label class="mt-1" for="dashboard_to_date">End date</label>
                      <input type="date" class="form-control form-control-sm" id="dashboard_to_date" value="<?php echo $dashboard_custom_to_date; ?>">
                      <button type="button" class="btn btn-sm dashboard-custom-date-apply" onclick="apply_dashboard_custom_date_range();">Apply</button>
                    </div>
                  </div>
                </div>
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
    function apply_dashboard_custom_date_range(){
      var fromDate = document.getElementById('dashboard_from_date').value;
      var toDate = document.getElementById('dashboard_to_date').value;
      var queryString = 'date_range=custom';

      if(fromDate){
        queryString += '&from_date=' + encodeURIComponent(fromDate);
      }
      if(toDate){
        queryString += '&to_date=' + encodeURIComponent(toDate);
      }

      window.location.href = 'dashboard_transactions.php?' + queryString;
    }
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
