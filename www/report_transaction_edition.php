<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Transaction Edition"];

  $general_filtering_data = '';
  $income_filtering_data = '';
  $expense_filtering_data = '';
  $exchange_filtering_data = '';
  $transfer_filtering_data = '';

  $dashboard_filter_values = [
    'province' => '',
    'branch' => '',
    'from_date' => '',
    'to_date' => '',
    'customer_name' => '',
    'customer_id' => '',
    'description' => '',
    'markup' => '',
    'unmark' => '',
    'currency' => '',
    'transaction_components' => '',
    'transaction_type' => '',
    'amount' => '',
    'sib_number' => '',
    'check_number' => '',
    'users_id' => [],
  ];

  $dashboard_date_range_data = resolve_dashboard_transaction_date_range('transaction_editions.insertion_date', 'start_edition_date', 'end_edition_date');
  $dashboard_date_range_options = $dashboard_date_range_data['options'];
  $dashboard_date_range = $dashboard_date_range_data['selected'];
  $dashboard_custom_from_date = $dashboard_date_range_data['custom_from_date'];
  $dashboard_custom_to_date = $dashboard_date_range_data['custom_to_date'];
  $dashboard_date_filtering_data = $dashboard_date_range_data['sql_filter'];
  $dashboard_date_range_display = $dashboard_date_range_data['display_date_range'];
  $dashboard_date_range_label = $dashboard_date_range_data['label'];
  $dashboard_from_date = $dashboard_date_range_data['from_date'];
  $dashboard_to_date = $dashboard_date_range_data['to_date'];
  $holu_filtering_array[] = $dashboard_date_range_data['filter_label'];

  function transaction_edition_filter_input($key, $legacy_key='', $default=''){
    if(isset($_GET[$key])){
      return holu_escape($_GET[$key]);
    }
    if($legacy_key!='' && isset($_GET[$legacy_key])){
      return holu_escape($_GET[$legacy_key]);
    }
    return $default;
  }

  function transaction_edition_filter_sql_value($value){
    global $db;
    return $db->quote((string)$value);
  }

  function transaction_edition_filter_like_value($value){
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], (string)$value);
  }

  function transaction_edition_filter_date_value($value){
    $value = trim((string)$value);
    if($value===''){
      return '';
    }
    if(is_holu_date_value($value)){
      return $value;
    }
    $timestamp = strtotime($value);
    if($timestamp===false){
      return '';
    }
    return date('Y-m-d', $timestamp);
  }

  function transaction_edition_add_table_filter($condition_by_type){
    global $income_filtering_data, $expense_filtering_data, $exchange_filtering_data, $transfer_filtering_data;

    $income_filtering_data .= isset($condition_by_type['income']) ? $condition_by_type['income'] : ' AND 0 ';
    $expense_filtering_data .= isset($condition_by_type['expense']) ? $condition_by_type['expense'] : ' AND 0 ';
    $exchange_filtering_data .= isset($condition_by_type['exchange']) ? $condition_by_type['exchange'] : ' AND 0 ';
    $transfer_filtering_data .= isset($condition_by_type['transfer']) ? $condition_by_type['transfer'] : ' AND 0 ';
  }

  function transaction_edition_add_filter_label($label, $value){
    global $holu_filtering_array;
    $value = trim((string)$value);
    if($value!==''){
      $holu_filtering_array[] = $label.': '.htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
  }

  function transaction_edition_filter_component_labels($component_ids){
    $labels = [];
    foreach($component_ids as $component_id){
      $parts = explode('/', trim((string)$component_id, '/'));
      if(count($parts)<2 || $parts[0]!='sub_category_accessibility'){
        continue;
      }

      $transaction_type_label = ucfirst($parts[1]);
      if(count($parts)>=4 && $parts[3]!=='' && is_numeric($parts[3])){
        $sub_category_name = get_col('sub_categories', 'sub_category_name', 'id', $parts[3]);
        if($sub_category_name!=''){
          $labels[] = $transaction_type_label.' / '.$sub_category_name;
          continue;
        }
      }

      if(count($parts)>=3 && $parts[2]!=='' && is_numeric($parts[2])){
        $category_name = get_col('categories', 'category_name', 'id', $parts[2]);
        if($category_name!=''){
          $labels[] = $transaction_type_label.' / '.$category_name;
          continue;
        }
      }

      $labels[] = $transaction_type_label;
    }

    return implode(', ', array_unique($labels));
  }

  $dashboard_filter_values['province'] = transaction_edition_filter_input('dashboard_filter_province', 'province');
  if($dashboard_filter_values['province']!='' && $dashboard_filter_values['province']!='0'){
    $province_sql = transaction_edition_filter_sql_value($dashboard_filter_values['province']);
    transaction_edition_add_table_filter([
      'income' => " AND incomes.province=$province_sql ",
      'expense' => " AND expenses.province=$province_sql ",
      'exchange' => " AND exchanges.province=$province_sql ",
      'transfer' => " AND (transfers.from_province=$province_sql OR transfers.to_province=$province_sql) ",
    ]);
    transaction_edition_add_filter_label('Province', $dashboard_filter_values['province']);
  }

  $dashboard_filter_values['branch'] = transaction_edition_filter_input('dashboard_filter_branch', 'branch');
  if($dashboard_filter_values['branch']!='' && $dashboard_filter_values['branch']!='0'){
    $branch_sql = transaction_edition_filter_sql_value($dashboard_filter_values['branch']);
    transaction_edition_add_table_filter([
      'income' => " AND incomes.branch=$branch_sql ",
      'expense' => " AND expenses.branch=$branch_sql ",
      'exchange' => " AND exchanges.branch=$branch_sql ",
      'transfer' => " AND (transfers.from_branch=$branch_sql OR transfers.to_branch=$branch_sql) ",
    ]);
    transaction_edition_add_filter_label('Branch', $dashboard_filter_values['branch']);
  }

  $dashboard_filter_values['from_date'] = $dashboard_from_date;
  $dashboard_filter_values['to_date'] = $dashboard_to_date;
  $general_filtering_data .= $dashboard_date_filtering_data;

  $dashboard_filter_values['customer_name'] = transaction_edition_filter_input('dashboard_filter_customer_name', 'customer_name');
  if($dashboard_filter_values['customer_name']!=''){
    $customer_name_sql = transaction_edition_filter_sql_value('%'.transaction_edition_filter_like_value($dashboard_filter_values['customer_name']).'%');
    transaction_edition_add_table_filter([
      'income' => " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info LIKE $customer_name_sql ESCAPE '\\\\' AND deleted='0') ",
      'expense' => " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer Name' AND value_info LIKE $customer_name_sql ESCAPE '\\\\' AND deleted='0') ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    transaction_edition_add_filter_label('Customer Name', $dashboard_filter_values['customer_name']);
  }

  $dashboard_filter_values['customer_id'] = transaction_edition_filter_input('dashboard_filter_customer_id', 'customer_id');
  if($dashboard_filter_values['customer_id']!=''){
    $customer_id_sql = transaction_edition_filter_sql_value('%'.transaction_edition_filter_like_value($dashboard_filter_values['customer_id']).'%');
    transaction_edition_add_table_filter([
      'income' => " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer ID' AND value_info LIKE $customer_id_sql ESCAPE '\\\\' AND deleted='0') ",
      'expense' => " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer ID' AND value_info LIKE $customer_id_sql ESCAPE '\\\\' AND deleted='0') ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    transaction_edition_add_filter_label('Customer ID', $dashboard_filter_values['customer_id']);
  }

  $dashboard_filter_values['description'] = transaction_edition_filter_input('dashboard_filter_description', 'description');
  if($dashboard_filter_values['description']!=''){
    $description_sql = transaction_edition_filter_sql_value('%'.transaction_edition_filter_like_value($dashboard_filter_values['description']).'%');
    transaction_edition_add_table_filter([
      'income' => " AND incomes.description LIKE $description_sql ESCAPE '\\\\' ",
      'expense' => " AND expenses.description LIKE $description_sql ESCAPE '\\\\' ",
      'exchange' => " AND exchanges.description LIKE $description_sql ESCAPE '\\\\' ",
      'transfer' => " AND transfers.description LIKE $description_sql ESCAPE '\\\\' ",
    ]);
    transaction_edition_add_filter_label('Description', $dashboard_filter_values['description']);
  }

  $dashboard_filter_values['markup'] = transaction_edition_filter_input('dashboard_filter_markup', 'markup');
  if($dashboard_filter_values['markup']!=''){
    $markup_sql = transaction_edition_filter_sql_value($dashboard_filter_values['markup']);
    $general_filtering_data .= " AND transaction_editions.id IN (SELECT reference_id FROM markups WHERE reference_type='Transaction_Edition' AND markup_type=$markup_sql AND deleted='0') ";
    transaction_edition_add_filter_label('Markup', $dashboard_filter_values['markup']);
  }

  $dashboard_filter_values['unmark'] = transaction_edition_filter_input('dashboard_filter_unmark', 'unmark');
  if($dashboard_filter_values['unmark']!=''){
    $unmark_sql = transaction_edition_filter_sql_value($dashboard_filter_values['unmark']);
    $general_filtering_data .= " AND (transaction_editions.id IN (SELECT reference_id FROM markups WHERE reference_type='Transaction_Edition' AND markup_type=$unmark_sql AND deleted='1') OR transaction_editions.id NOT IN (SELECT reference_id FROM markups WHERE reference_type='Transaction_Edition' AND markup_type=$unmark_sql)) ";
    transaction_edition_add_filter_label('Unmark', $dashboard_filter_values['unmark']);
  }

  $dashboard_filter_values['currency'] = transaction_edition_filter_input('dashboard_filter_currency', 'currency');
  if($dashboard_filter_values['currency']!=''){
    $currency_sql = transaction_edition_filter_sql_value($dashboard_filter_values['currency']);
    transaction_edition_add_table_filter([
      'income' => " AND incomes.currency=$currency_sql ",
      'expense' => " AND expenses.currency=$currency_sql ",
      'exchange' => " AND (exchanges.from_currency=$currency_sql OR exchanges.to_currency=$currency_sql) ",
      'transfer' => " AND transfers.currency=$currency_sql ",
    ]);
    transaction_edition_add_filter_label('Currency', $dashboard_filter_values['currency']);
  }

  $dashboard_filter_values['transaction_components'] = transaction_edition_filter_input('dashboard_filter_transaction_components');
  if($dashboard_filter_values['transaction_components']!=''){
    $transaction_components = array_filter(explode(',', $dashboard_filter_values['transaction_components']));
    $income_sub_categories_id_array = [];
    $expense_sub_categories_id_array = [];
    $exchange_sub_categories_id_counter = 0;
    $transfer_sub_categories_id_counter = 0;

    foreach($transaction_components as $transaction_component){
      $transaction_component_parts = explode('/', $transaction_component);
      if(count($transaction_component_parts)<2){
        continue;
      }

      $transaction_component_type = $transaction_component_parts[1];
      switch($transaction_component_type){
        case 'income':
          if(count($transaction_component_parts)>3 && $transaction_component_parts[3]!=''){
            $income_sub_categories_id_array[] = transaction_edition_filter_sql_value($transaction_component_parts[3]);
          }
        break;

        case 'expense':
          if(count($transaction_component_parts)>3 && $transaction_component_parts[3]!=''){
            $expense_sub_categories_id_array[] = transaction_edition_filter_sql_value($transaction_component_parts[3]);
          }
        break;

        case 'exchange':
          $exchange_sub_categories_id_counter++;
        break;

        case 'transfer':
          $transfer_sub_categories_id_counter++;
        break;
      }
    }

    if(count($income_sub_categories_id_array)>0){
      $income_filtering_data .= " AND incomes.sub_categories_id IN (".implode(',', array_unique($income_sub_categories_id_array)).") ";
    }else{
      $income_filtering_data .= " AND 0 ";
    }

    if(count($expense_sub_categories_id_array)>0){
      $expense_filtering_data .= " AND expenses.sub_categories_id IN (".implode(',', array_unique($expense_sub_categories_id_array)).") ";
    }else{
      $expense_filtering_data .= " AND 0 ";
    }

    if($exchange_sub_categories_id_counter<=0){
      $exchange_filtering_data .= " AND 0 ";
    }

    if($transfer_sub_categories_id_counter<=0){
      $transfer_filtering_data .= " AND 0 ";
    }

    transaction_edition_add_filter_label('Transaction Components', transaction_edition_filter_component_labels($transaction_components));
  }else{
    $dashboard_filter_values['transaction_type'] = transaction_edition_filter_input('dashboard_filter_transaction_type', 'transaction_type');
    if($dashboard_filter_values['transaction_type']!=''){
      $selected_transaction_type = strtolower($dashboard_filter_values['transaction_type']);
      foreach(['income', 'expense', 'exchange', 'transfer'] as $transaction_key){
        if($transaction_key!=$selected_transaction_type){
          ${$transaction_key.'_filtering_data'} .= " AND 0 ";
        }
      }
      transaction_edition_add_filter_label('Transaction Type', $dashboard_filter_values['transaction_type']);
    }
  }

  $dashboard_filter_values['amount'] = transaction_edition_filter_input('dashboard_filter_amount', 'amount');
  if($dashboard_filter_values['amount']!=''){
    $amount_sql = transaction_edition_filter_sql_value($dashboard_filter_values['amount']);
    transaction_edition_add_table_filter([
      'income' => " AND incomes.income_amount=$amount_sql ",
      'expense' => " AND expenses.expense_amount=$amount_sql ",
      'exchange' => " AND (exchanges.from_amount=$amount_sql OR exchanges.to_amount=$amount_sql) ",
      'transfer' => " AND transfers.transfer_amount=$amount_sql ",
    ]);
    transaction_edition_add_filter_label('Amount', $dashboard_filter_values['amount']);
  }

  $dashboard_filter_values['sib_number'] = transaction_edition_filter_input('dashboard_filter_sib_number');
  if($dashboard_filter_values['sib_number']!=''){
    $sib_number_sql = transaction_edition_filter_sql_value($dashboard_filter_values['sib_number']);
    transaction_edition_add_table_filter([
      'income' => " AND incomes.sib_number=$sib_number_sql ",
      'expense' => " AND 0 ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    transaction_edition_add_filter_label('SIB Number', $dashboard_filter_values['sib_number']);
  }

  $dashboard_filter_values['check_number'] = transaction_edition_filter_input('dashboard_filter_check_number', 'check_number');
  if($dashboard_filter_values['check_number']!=''){
    $check_number_sql = transaction_edition_filter_sql_value($dashboard_filter_values['check_number']);
    transaction_edition_add_table_filter([
      'income' => " AND incomes.check_number=$check_number_sql ",
      'expense' => " AND expenses.check_number=$check_number_sql ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    transaction_edition_add_filter_label('Check Number', $dashboard_filter_values['check_number']);
  }

  if(isset($_GET['dashboard_filter_users_id']) && is_array($_GET['dashboard_filter_users_id']) && count($_GET['dashboard_filter_users_id'])>0){
    $users_id_items = [];
    foreach($_GET['dashboard_filter_users_id'] as $users_id_item){
      $users_id_item = holu_escape($users_id_item);
      if($users_id_item!==''){
        $dashboard_filter_values['users_id'][] = $users_id_item;
        $users_id_items[] = transaction_edition_filter_sql_value($users_id_item);
      }
    }
    if(count($users_id_items)>0){
      $users_id_sql = implode(',', $users_id_items);
      transaction_edition_add_table_filter([
        'income' => " AND incomes.users_id IN ($users_id_sql) ",
        'expense' => " AND expenses.users_id IN ($users_id_sql) ",
        'exchange' => " AND exchanges.users_id IN ($users_id_sql) ",
        'transfer' => " AND (transfers.users_id IN ($users_id_sql) OR transfers.approved_by IN ($users_id_sql)) ",
      ]);
      transaction_edition_add_filter_label('Added By', implode(', ', $dashboard_filter_values['users_id']));
    }
  }

  $dashboard_filter_panel_is_open = false;
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
                <h4 class="header-title">
                  <?php echo get_table_header('fa fa-list', 'Report of Transaction Edition', $transaction_edition_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>
                <div class="dropdown dashboard-date-range-dropdown">
                  <button class="btn dropdown-toggle waves-effect waves-light dashboard-date-range-toggle" type="button" id="dashboardDateRangeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-calendar-alt dashboard-date-range-icon"></i>
                    <span class="dashboard-date-range-copy">
                      <span class="dashboard-date-range-dates"><?php echo $dashboard_date_range_display; ?></span>
                      <span class="dashboard-date-range-label"><?php echo $dashboard_date_range_label; ?></span>
                    </span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right dashboard-date-range-menu" aria-labelledby="dashboardDateRangeDropdown" onclick="event.stopPropagation();">
                    <div class="dashboard-date-range-menu-heading">
                      <strong>Choose date range</strong>
                      <span>Transaction editions shown in this report</span>
                    </div>
                    <div class="dashboard-date-range-quick-list">
                      <?php
                      foreach($dashboard_date_range_options as $dashboard_date_range_key => $dashboard_date_range_option){
                        if($dashboard_date_range_key!='custom'){
                          ?>
                          <a class="dashboard-date-range-option <?php echo $dashboard_date_range==$dashboard_date_range_key ? 'active' : ''; ?>" href="report_transaction_edition.php?date_range=<?php echo $dashboard_date_range_key; ?>">
                            <i class="far fa-clock"></i>
                            <span><?php echo $dashboard_date_range_option; ?></span>
                          </a>
                          <?php
                        }
                      }
                      ?>
                    </div>
                    <form id="dashboard_custom_date_range" class="dashboard-custom-date-range" action="report_transaction_edition.php" method="get">
                      <input type="hidden" name="date_range" value="custom">
                      <div class="dashboard-custom-date-title">
                        <i class="far fa-calendar-check"></i>
                        <span>Custom range</span>
                      </div>
                      <div class="dashboard-custom-date-grid">
                        <div>
                          <label for="dashboard_from_date">Start date</label>
                          <input type="date" class="form-control form-control-sm" id="dashboard_from_date" name="from_date" value="<?php echo htmlspecialchars($dashboard_from_date, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div>
                          <label for="dashboard_to_date">End date</label>
                          <input type="date" class="form-control form-control-sm" id="dashboard_to_date" name="to_date" value="<?php echo htmlspecialchars($dashboard_to_date, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                      </div>
                      <small class="dashboard-date-range-help">Leave one side empty for an open-ended date filter.</small>
                      <button type="submit" class="btn btn-sm dashboard-custom-date-apply">
                        <i class="fa fa-check"></i> Apply date filter
                      </button>
                    </form>
                  </div>
                </div>
                <button type="button" class="btn waves-effect waves-light adder_button dashboard-filter-toggle" id="dashboard_transaction_edition_filter_toggle" aria-expanded="<?php echo $dashboard_filter_panel_is_open ? 'true' : 'false'; ?>" aria-controls="dashboard_transaction_edition_filter_panel"><i class="fa fa-filter"></i> Filter</button>
              </div>

              <div class="dashboard-filter-panel <?php echo $dashboard_filter_panel_is_open ? 'is-open' : ''; ?>" id="dashboard_transaction_edition_filter_panel" aria-hidden="<?php echo $dashboard_filter_panel_is_open ? 'false' : 'true'; ?>">
                <form class="dashboard-filter-form" id="dashboard_transaction_edition_filter_form" role="form" action="report_transaction_edition.php" method="GET">
                  <div class="dashboard-filter-panel-topline"></div>
                  <input type="hidden" name="date_range" value="<?php echo htmlspecialchars($dashboard_date_range, ENT_QUOTES, 'UTF-8'); ?>">
                  <?php if($dashboard_date_range=='custom'){ ?>
                    <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($dashboard_custom_from_date, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($dashboard_custom_to_date, ENT_QUOTES, 'UTF-8'); ?>">
                  <?php } ?>
                  <div class="dashboard-filter-form-header">
                    <div>
                      <span class="dashboard-filter-eyebrow"><i class="fa fa-sliders-h"></i> Advanced filters</span>
                      <h5>Refine transaction editions</h5>
                      <p>Choose filters and apply them to the transaction edition list.</p>
                    </div>
                    <span class="dashboard-filter-status"><i class="fa fa-check-circle"></i> Active filters</span>
                  </div>

                  <div class="dashboard-filter-grid">
                    <div class="dashboard-filter-field dashboard-filter-field-wide">
                      <label for="dashboard_filter_province">Province</label>
                      <select id="dashboard_filter_province" name="dashboard_filter_province" class="form-control" data-branch-target="dashboard_filter_branch" data-branch-value="<?php echo htmlspecialchars($dashboard_filter_values['branch'], ENT_QUOTES, 'UTF-8'); ?>">
                        <option <?php echo ($dashboard_filter_values['province']=='' || $dashboard_filter_values['province']=='0') ? 'selected' : ''; ?> hidden value="">Select an option</option>
                        <?php echo get_province_option($dashboard_filter_values['province']!='' ? $dashboard_filter_values['province'] : '0'); ?>
                      </select>
                    </div>

                    <div class="dashboard-filter-field dashboard-filter-field-wide">
                      <label for="dashboard_filter_branch">Branch</label>
                      <select id="dashboard_filter_branch" name="dashboard_filter_branch" class="form-control">
                        <?php echo get_branch_option($dashboard_filter_values['province']!='' ? $dashboard_filter_values['province'] : '0', $dashboard_filter_values['branch']!='' ? $dashboard_filter_values['branch'] : '0'); ?>
                      </select>
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_customer_name">Customer Name</label>
                      <input type="text" id="dashboard_filter_customer_name" name="dashboard_filter_customer_name" class="form-control" value="<?php echo htmlspecialchars($dashboard_filter_values['customer_name'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Type here...">
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_customer_id">Customer ID</label>
                      <input type="text" id="dashboard_filter_customer_id" name="dashboard_filter_customer_id" class="form-control" value="<?php echo htmlspecialchars($dashboard_filter_values['customer_id'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Type here...">
                    </div>

                    <div class="dashboard-filter-field dashboard-filter-field-wide">
                      <label for="dashboard_filter_description">Description</label>
                      <input type="text" id="dashboard_filter_description" name="dashboard_filter_description" class="form-control" value="<?php echo htmlspecialchars($dashboard_filter_values['description'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search the transaction description...">
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_markup">Markup</label>
                      <select id="dashboard_filter_markup" name="dashboard_filter_markup" class="form-control">
                        <?php echo get_markup_option('system_accessibility/report/report_transaction/', $dashboard_filter_values['markup']); ?>
                      </select>
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_unmark">Unmark</label>
                      <select id="dashboard_filter_unmark" name="dashboard_filter_unmark" class="form-control">
                        <?php echo get_markup_option('system_accessibility/report/report_transaction/', $dashboard_filter_values['unmark']); ?>
                      </select>
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_currency">Currency</label>
                      <select id="dashboard_filter_currency" name="dashboard_filter_currency" class="form-control">
                        <option <?php echo $dashboard_filter_values['currency']=='' ? 'selected' : ''; ?> value="">Select an option</option>
                        <?php echo get_currency_option($dashboard_filter_values['currency']); ?>
                      </select>
                    </div>

                    <div class="dashboard-filter-field dashboard-filter-field-wide">
                      <label for="dashboard_filter_transaction_component_search">Transaction Components</label>
                      <input type="hidden" id="dashboard_filter_transaction_components" name="dashboard_filter_transaction_components" value="<?php echo htmlspecialchars($dashboard_filter_values['transaction_components'], ENT_QUOTES, 'UTF-8'); ?>">
                      <div class="dashboard-component-selector">
                        <div class="dashboard-component-search">
                          <i class="fa fa-search"></i>
                          <input type="text" id="dashboard_filter_transaction_component_search" class="form-control" placeholder="Search income, expense, transfer, exchange, or child components..." autocomplete="off">
                        </div>
                        <div class="dashboard-component-tree" id="dashboard_filter_transaction_components_container"></div>
                        <small class="dashboard-component-help" id="dashboard_filter_transaction_components_summary">Select parent or child transaction components.</small>
                      </div>
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_amount">Amount</label>
                      <input type="text" id="dashboard_filter_amount" name="dashboard_filter_amount" class="form-control" value="<?php echo htmlspecialchars($dashboard_filter_values['amount'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Type amount...">
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_sib_number">SIB Number</label>
                      <input type="text" id="dashboard_filter_sib_number" name="dashboard_filter_sib_number" class="form-control" value="<?php echo htmlspecialchars($dashboard_filter_values['sib_number'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Type SIB number...">
                    </div>

                    <div class="dashboard-filter-field">
                      <label for="dashboard_filter_check_number">Check Number</label>
                      <input type="text" id="dashboard_filter_check_number" name="dashboard_filter_check_number" class="form-control" value="<?php echo htmlspecialchars($dashboard_filter_values['check_number'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Type check number...">
                    </div>

                    <div class="dashboard-filter-field dashboard-filter-field-wide">
                      <label for="dashboard_filter_users_id">Added By</label>
                      <select id="dashboard_filter_users_id" name="dashboard_filter_users_id[]" class="form-control select2" multiple data-placeholder="Select user(s)">
                        <?php echo get_user_option($dashboard_filter_values['users_id']); ?>
                      </select>
                    </div>
                  </div>

                  <div class="dashboard-filter-actions">
                    <small><i class="fa fa-info-circle"></i> Filters are applied to the transaction edition table.</small>
                    <div>
                      <a class="btn dashboard-filter-reset" href="report_transaction_edition.php"><i class="fa fa-undo"></i> Clear</a>
                      <button type="button" class="btn dashboard-filter-close" id="dashboard_transaction_edition_filter_close"><i class="fa fa-times"></i> Close</button>
                      <button type="submit" class="btn dashboard-filter-apply"><i class="fa fa-search"></i> Apply filters</button>
                    </div>
                  </div>
                </form>
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
    $('#dashboard_transaction_edition_filter_toggle').on('click', function(){
      var $panel = $('#dashboard_transaction_edition_filter_panel');
      var isOpen = $panel.toggleClass('is-open').hasClass('is-open');
      $(this).attr('aria-expanded', isOpen ? 'true' : 'false');
      $panel.attr('aria-hidden', isOpen ? 'false' : 'true');
    });

    var dashboardTransactionEditionComponents = <?php echo print_access_sub_categories(); ?>;
    var dashboardSelectedTransactionEditionComponents = <?php echo json_encode(array_values(array_filter(explode(',', $dashboard_filter_values['transaction_components'])))); ?>;
    var dashboardTransactionEditionComponentsTree = new Tree('#dashboard_filter_transaction_components_container', {
      data: [{ id: 'dashboard_filter_transaction_components_root', text: 'Transaction Components', children: dashboardTransactionEditionComponents }],
      closeDepth: 2,
      loaded: function(){
        this.values = dashboardSelectedTransactionEditionComponents;
        $('#dashboard_filter_transaction_components').val(this.values.join(','));
        updateDashboardTransactionEditionComponentSummary(this.selectedNodes || []);
      },
      onChange: function(){
        $('#dashboard_filter_transaction_components').val(this.values.join(','));
        updateDashboardTransactionEditionComponentSummary(this.selectedNodes || []);
      }
    });

    function updateDashboardTransactionEditionComponentSummary(selectedNodes){
      var $summary = $('#dashboard_filter_transaction_components_summary');
      var selectedLabels = $.map(selectedNodes, function(node){
        return node && node.text ? node.text : null;
      });

      if(selectedLabels.length === 0){
        $summary.text('Select parent or child transaction components.');
      }else if(selectedLabels.length <= 3){
        $summary.text('Selected: ' + selectedLabels.join(', '));
      }else{
        $summary.text(selectedLabels.length + ' transaction components selected.');
      }
    }

    $('#dashboard_filter_transaction_component_search').on('input', function(){
      var term = $.trim($(this).val()).toLowerCase();
      var $tree = $('#dashboard_filter_transaction_components_container');

      if(term === ''){
        $tree.find('.treejs-node').show();
        return;
      }

      $tree.find('.treejs-node').hide();
      $tree.find('.treejs-label').each(function(){
        var $label = $(this);
        var isMatch = $label.text().toLowerCase().indexOf(term) !== -1;
        if(isMatch){
          $label.closest('.treejs-node').show().parents('.treejs-node').show();
          $label.closest('.treejs-node').parents('.treejs-node__close').removeClass('treejs-node__close');
          $label.closest('.treejs-nodes').show();
        }
      });
    });

    $('#dashboard_transaction_edition_filter_close').on('click', function(){
      $('#dashboard_transaction_edition_filter_panel').removeClass('is-open').attr('aria-hidden', 'true');
      $('#dashboard_transaction_edition_filter_toggle').attr('aria-expanded', 'false');
    });

    $('#dashboard_custom_date_range').on('submit', function(){
      var fromDate = $('#dashboard_from_date').val();
      var toDate = $('#dashboard_to_date').val();

      if(fromDate && toDate && fromDate > toDate){
        $('#dashboard_from_date').val(toDate);
        $('#dashboard_to_date').val(fromDate);
      }
    });

    $(function(){
      $("[data-toggle=popover]").popover();
    });
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
