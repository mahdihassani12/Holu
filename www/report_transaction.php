<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Transactions"];

  // Reports Transactions now uses the former Dashboards Transactions implementation.
  // The previous Reports Transactions implementation is commented out in report_transaction_legacy_commented.php for reversible restoration.

  set_pagination();


  function has_customer_reference_for_sib($additional_informations_raw){
    if(empty($additional_informations_raw)){
      return false;
    }

    $decoded_additional_informations = json_decode($additional_informations_raw, true);
    if(!is_array($decoded_additional_informations)){
      return false;
    }

    $customer_name = isset($decoded_additional_informations['Customer Name']) ? trim((string)$decoded_additional_informations['Customer Name']) : '';
    $customer_id = isset($decoded_additional_informations['Customer ID']) ? trim((string)$decoded_additional_informations['Customer ID']) : '';

    return ($customer_name !== '' || $customer_id !== '');
  }


  $dashboard_date_range_data = resolve_dashboard_transaction_date_range();
  $dashboard_date_range_options = $dashboard_date_range_data['options'];
  $dashboard_date_range = $dashboard_date_range_data['selected'];
  $dashboard_custom_from_date = $dashboard_date_range_data['custom_from_date'];
  $dashboard_custom_to_date = $dashboard_date_range_data['custom_to_date'];
  $dashboard_date_filtering_data = $dashboard_date_range_data['sql_filter'];
  $dashboard_excel_data = $dashboard_date_range_data['query_string'];
  $dashboard_date_range_display = $dashboard_date_range_data['display_date_range'];
  $dashboard_date_range_label = $dashboard_date_range_data['label'];
  $dashboard_from_date = $dashboard_date_range_data['from_date'];
  $dashboard_to_date = $dashboard_date_range_data['to_date'];
  $holu_filtering_array[] = $dashboard_date_range_data['filter_label'];

  $dashboard_filtering_data = [
    'income' => '',
    'expense' => '',
    'exchange' => '',
    'transfer' => '',
  ];
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
  $dashboard_advanced_filter_query = '';

  function dashboard_filter_input($key, $default=''){
    return isset($_GET[$key]) ? holu_escape($_GET[$key]) : $default;
  }

  function dashboard_filter_sql_value($value){
    global $db;
    return $db->quote((string)$value);
  }

  function dashboard_filter_like_value($value){
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], (string)$value);
  }

  function dashboard_filter_date_value($value){
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

  function dashboard_add_table_filter($condition_by_type){
    global $dashboard_filtering_data;
    foreach($dashboard_filtering_data as $transaction_key => $existing_filter){
      $dashboard_filtering_data[$transaction_key] .= isset($condition_by_type[$transaction_key]) ? $condition_by_type[$transaction_key] : ' AND 0 ';
    }
  }

  function dashboard_add_filter_query($key, $value){
    global $dashboard_advanced_filter_query;
    if(is_array($value)){
      foreach($value as $item){
        $dashboard_advanced_filter_query .= '&'.urlencode($key).'[]='.urlencode((string)$item);
      }
    }else{
      $dashboard_advanced_filter_query .= '&'.urlencode($key).'='.urlencode((string)$value);
    }
  }

  function dashboard_add_filter_label($label, $value){
    global $holu_filtering_array;
    $holu_filtering_array[] = $label.': '.htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
  }


  function dashboard_filter_component_labels($component_ids){
    $labels = [];
    foreach($component_ids as $component_id){
      $parts = explode('/', trim((string)$component_id, '/'));
      if(count($parts)<2 || $parts[0]!='sub_category_accessibility'){
        continue;
      }

      $transaction_type = ucfirst($parts[1]);
      if(count($parts)>=4 && $parts[3]!=='' && is_numeric($parts[3])){
        $sub_category_name = get_col('sub_categories', 'sub_category_name', 'id', $parts[3]);
        if($sub_category_name!=''){
          $labels[] = $transaction_type.' / '.$sub_category_name;
          continue;
        }
      }

      if(count($parts)>=3 && $parts[2]!=='' && is_numeric($parts[2])){
        $category_name = get_col('categories', 'category_name', 'id', $parts[2]);
        if($category_name!=''){
          $labels[] = $transaction_type.' / '.$category_name;
          continue;
        }
      }

      $labels[] = $transaction_type;
    }

    return implode(', ', array_unique($labels));
  }

  function dashboard_transaction_description($transaction_row){
    if($transaction_row['transaction_type']!='Transfer'){
      return $transaction_row['transaction_description'];
    }

    return ($transaction_row['transaction_transfer_side']=='Transfer To')
      ? $transaction_row['transaction_approve_description']
      : $transaction_row['transaction_description'];
  }

  function dashboard_report_header_label($date_range_display, $province, $branch){
    $header_parts = [
      'Report of Transactions',
      (string)$date_range_display,
    ];

    $province = trim((string)$province);
    if($province!=='' && $province!=='0'){
      $header_parts[] = $province;
    }

    $branch = trim((string)$branch);
    if($branch!=='' && $branch!=='0'){
      $header_parts[] = $branch;
    }

    return htmlspecialchars(implode(' • ', $header_parts), ENT_QUOTES, 'UTF-8');
  }


  $income_access_condition = set_province_branch_portion('incomes.province', 'incomes.branch');
  $expense_access_condition = set_province_branch_portion('expenses.province', 'expenses.branch');
  $exchange_access_condition = set_province_branch_portion('exchanges.province', 'exchanges.branch');
  $transfer_from_access_condition = set_province_branch_portion('transfers.from_province', 'transfers.from_branch');
  $transfer_to_access_condition = set_province_branch_portion('transfers.to_province', 'transfers.to_branch');

  $dashboard_filter_values['province'] = dashboard_filter_input('dashboard_filter_province');
  if($dashboard_filter_values['province']!='' && $dashboard_filter_values['province']!='0'){
    $province_sql = dashboard_filter_sql_value($dashboard_filter_values['province']);
    dashboard_add_table_filter([
      'income' => " AND incomes.province=$province_sql ",
      'expense' => " AND expenses.province=$province_sql ",
      'exchange' => " AND exchanges.province=$province_sql ",
      'transfer' => " AND (transfers.from_province=$province_sql OR transfers.to_province=$province_sql) ",
    ]);
    dashboard_add_filter_query('dashboard_filter_province', $dashboard_filter_values['province']);
    dashboard_add_filter_label('Province', $dashboard_filter_values['province']);
  }

  $dashboard_filter_values['branch'] = dashboard_filter_input('dashboard_filter_branch');
  if($dashboard_filter_values['branch']!='' && $dashboard_filter_values['branch']!='0'){
    $branch_sql = dashboard_filter_sql_value($dashboard_filter_values['branch']);
    dashboard_add_table_filter([
      'income' => " AND incomes.branch=$branch_sql ",
      'expense' => " AND expenses.branch=$branch_sql ",
      'exchange' => " AND exchanges.branch=$branch_sql ",
      'transfer' => " AND (transfers.from_branch=$branch_sql OR transfers.to_branch=$branch_sql) ",
    ]);
    dashboard_add_filter_query('dashboard_filter_branch', $dashboard_filter_values['branch']);
    dashboard_add_filter_label('Branch', $dashboard_filter_values['branch']);
  }

  $dashboard_report_header = dashboard_report_header_label(
    $dashboard_date_range_display,
    $dashboard_filter_values['province'],
    $dashboard_filter_values['branch']
  );

  $dashboard_filter_values['from_date'] = $dashboard_from_date;
  $dashboard_filter_values['to_date'] = $dashboard_to_date;

  $dashboard_filter_values['customer_name'] = dashboard_filter_input('dashboard_filter_customer_name');
  if($dashboard_filter_values['customer_name']!=''){
    $customer_name_sql = dashboard_filter_sql_value('%'.dashboard_filter_like_value($dashboard_filter_values['customer_name']).'%');
    dashboard_add_table_filter([
      'income' => " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info LIKE $customer_name_sql ESCAPE '\\\\' AND deleted='0') ",
      'expense' => " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer Name' AND value_info LIKE $customer_name_sql ESCAPE '\\\\' AND deleted='0') ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    dashboard_add_filter_query('dashboard_filter_customer_name', $dashboard_filter_values['customer_name']);
    dashboard_add_filter_label('Customer Name', $dashboard_filter_values['customer_name']);
  }

  $dashboard_filter_values['customer_id'] = dashboard_filter_input('dashboard_filter_customer_id');
  if($dashboard_filter_values['customer_id']!=''){
    $customer_id_sql = dashboard_filter_sql_value('%'.dashboard_filter_like_value($dashboard_filter_values['customer_id']).'%');
    dashboard_add_table_filter([
      'income' => " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer ID' AND value_info LIKE $customer_id_sql ESCAPE '\\\\' AND deleted='0') ",
      'expense' => " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer ID' AND value_info LIKE $customer_id_sql ESCAPE '\\\\' AND deleted='0') ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    dashboard_add_filter_query('dashboard_filter_customer_id', $dashboard_filter_values['customer_id']);
    dashboard_add_filter_label('Customer ID', $dashboard_filter_values['customer_id']);
  }

  $dashboard_filter_values['description'] = dashboard_filter_input('dashboard_filter_description');
  if($dashboard_filter_values['description']!=''){
    $description_sql = dashboard_filter_sql_value('%'.dashboard_filter_like_value($dashboard_filter_values['description']).'%');
    dashboard_add_table_filter([
      'income' => " AND incomes.description LIKE $description_sql ESCAPE '\\\\' ",
      'expense' => " AND expenses.description LIKE $description_sql ESCAPE '\\\\' ",
      'exchange' => " AND exchanges.description LIKE $description_sql ESCAPE '\\\\' ",
      'transfer' => " AND transfers.description LIKE $description_sql ESCAPE '\\\\' ",
    ]);
    dashboard_add_filter_query('dashboard_filter_description', $dashboard_filter_values['description']);
    dashboard_add_filter_label('Description', $dashboard_filter_values['description']);
  }

  $dashboard_filter_values['markup'] = dashboard_filter_input('dashboard_filter_markup');
  if($dashboard_filter_values['markup']!=''){
    $markup_sql = dashboard_filter_sql_value($dashboard_filter_values['markup']);
    dashboard_add_table_filter([
      'income' => " AND incomes.id IN (SELECT reference_id FROM markups WHERE reference_type='Income' AND markup_type=$markup_sql AND deleted='0') ",
      'expense' => " AND expenses.id IN (SELECT reference_id FROM markups WHERE reference_type='Expense' AND markup_type=$markup_sql AND deleted='0') ",
      'exchange' => " AND exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type='Exchange' AND markup_type=$markup_sql AND deleted='0') ",
      'transfer' => " AND transfers.id IN (SELECT reference_id FROM markups WHERE reference_type='Transfer' AND markup_type=$markup_sql AND deleted='0') ",
    ]);
    dashboard_add_filter_query('dashboard_filter_markup', $dashboard_filter_values['markup']);
    dashboard_add_filter_label('Markup', $dashboard_filter_values['markup']);
  }

  $dashboard_filter_values['unmark'] = dashboard_filter_input('dashboard_filter_unmark');
  if($dashboard_filter_values['unmark']!=''){
    $unmark_sql = dashboard_filter_sql_value($dashboard_filter_values['unmark']);
    dashboard_add_table_filter([
      'income' => " AND (incomes.id IN (SELECT reference_id FROM markups WHERE reference_type='Income' AND markup_type=$unmark_sql AND deleted='1') OR incomes.id NOT IN (SELECT reference_id FROM markups WHERE reference_type='Income' AND markup_type=$unmark_sql)) ",
      'expense' => " AND (expenses.id IN (SELECT reference_id FROM markups WHERE reference_type='Expense' AND markup_type=$unmark_sql AND deleted='1') OR expenses.id NOT IN (SELECT reference_id FROM markups WHERE reference_type='Expense' AND markup_type=$unmark_sql)) ",
      'exchange' => " AND (exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type='Exchange' AND markup_type=$unmark_sql AND deleted='1') OR exchanges.id NOT IN (SELECT reference_id FROM markups WHERE reference_type='Exchange' AND markup_type=$unmark_sql)) ",
      'transfer' => " AND (transfers.id IN (SELECT reference_id FROM markups WHERE reference_type='Transfer' AND markup_type=$unmark_sql AND deleted='1') OR transfers.id NOT IN (SELECT reference_id FROM markups WHERE reference_type='Transfer' AND markup_type=$unmark_sql)) ",
    ]);
    dashboard_add_filter_query('dashboard_filter_unmark', $dashboard_filter_values['unmark']);
    dashboard_add_filter_label('Unmark', $dashboard_filter_values['unmark']);
  }

  $dashboard_filter_values['currency'] = dashboard_filter_input('dashboard_filter_currency');
  if($dashboard_filter_values['currency']!=''){
    $currency_sql = dashboard_filter_sql_value($dashboard_filter_values['currency']);
    dashboard_add_table_filter([
      'income' => " AND incomes.currency=$currency_sql ",
      'expense' => " AND expenses.currency=$currency_sql ",
      'exchange' => " AND (exchanges.from_currency=$currency_sql OR exchanges.to_currency=$currency_sql) ",
      'transfer' => " AND transfers.currency=$currency_sql ",
    ]);
    dashboard_add_filter_query('dashboard_filter_currency', $dashboard_filter_values['currency']);
    dashboard_add_filter_label('Currency', $dashboard_filter_values['currency']);
  }

  $dashboard_filter_values['transaction_components'] = dashboard_filter_input('dashboard_filter_transaction_components');
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
            $income_sub_categories_id_array[] = dashboard_filter_sql_value($transaction_component_parts[3]);
          }
        break;

        case 'expense':
          if(count($transaction_component_parts)>3 && $transaction_component_parts[3]!=''){
            $expense_sub_categories_id_array[] = dashboard_filter_sql_value($transaction_component_parts[3]);
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
      $dashboard_filtering_data['income'] .= " AND incomes.sub_categories_id IN (".implode(',', array_unique($income_sub_categories_id_array)).") ";
    }else{
      $dashboard_filtering_data['income'] .= " AND 0 ";
    }

    if(count($expense_sub_categories_id_array)>0){
      $dashboard_filtering_data['expense'] .= " AND expenses.sub_categories_id IN (".implode(',', array_unique($expense_sub_categories_id_array)).") ";
    }else{
      $dashboard_filtering_data['expense'] .= " AND 0 ";
    }

    if($exchange_sub_categories_id_counter<=0){
      $dashboard_filtering_data['exchange'] .= " AND 0 ";
    }

    if($transfer_sub_categories_id_counter<=0){
      $dashboard_filtering_data['transfer'] .= " AND 0 ";
    }

    dashboard_add_filter_query('dashboard_filter_transaction_components', $dashboard_filter_values['transaction_components']);
    dashboard_add_filter_label('Transaction Components', dashboard_filter_component_labels($transaction_components));
  }else{
    $dashboard_filter_values['transaction_type'] = dashboard_filter_input('dashboard_filter_transaction_type');
    if($dashboard_filter_values['transaction_type']!=''){
      $selected_transaction_type = strtolower($dashboard_filter_values['transaction_type']);
      foreach($dashboard_filtering_data as $transaction_key => $existing_filter){
        if($transaction_key!=$selected_transaction_type){
          $dashboard_filtering_data[$transaction_key] .= " AND 0 ";
        }
      }
      dashboard_add_filter_query('dashboard_filter_transaction_type', $dashboard_filter_values['transaction_type']);
      dashboard_add_filter_label('Transaction Type', $dashboard_filter_values['transaction_type']);
    }
  }

  $dashboard_filter_values['amount'] = dashboard_filter_input('dashboard_filter_amount');
  if($dashboard_filter_values['amount']!=''){
    $amount_sql = dashboard_filter_sql_value($dashboard_filter_values['amount']);
    dashboard_add_table_filter([
      'income' => " AND incomes.income_amount=$amount_sql ",
      'expense' => " AND expenses.expense_amount=$amount_sql ",
      'exchange' => " AND (exchanges.from_amount=$amount_sql OR exchanges.to_amount=$amount_sql) ",
      'transfer' => " AND transfers.transfer_amount=$amount_sql ",
    ]);
    dashboard_add_filter_query('dashboard_filter_amount', $dashboard_filter_values['amount']);
    dashboard_add_filter_label('Amount', $dashboard_filter_values['amount']);
  }

  $dashboard_filter_values['sib_number'] = dashboard_filter_input('dashboard_filter_sib_number');
  if($dashboard_filter_values['sib_number']!=''){
    $sib_number_sql = dashboard_filter_sql_value($dashboard_filter_values['sib_number']);
    dashboard_add_table_filter([
      'income' => " AND incomes.sib_number=$sib_number_sql ",
      'expense' => " AND 0 ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    dashboard_add_filter_query('dashboard_filter_sib_number', $dashboard_filter_values['sib_number']);
    dashboard_add_filter_label('SIB Number', $dashboard_filter_values['sib_number']);
  }

  $dashboard_filter_values['check_number'] = dashboard_filter_input('dashboard_filter_check_number');
  if($dashboard_filter_values['check_number']!=''){
    $check_number_sql = dashboard_filter_sql_value($dashboard_filter_values['check_number']);
    dashboard_add_table_filter([
      'income' => " AND incomes.check_number=$check_number_sql ",
      'expense' => " AND expenses.check_number=$check_number_sql ",
      'exchange' => " AND 0 ",
      'transfer' => " AND 0 ",
    ]);
    dashboard_add_filter_query('dashboard_filter_check_number', $dashboard_filter_values['check_number']);
    dashboard_add_filter_label('Check Number', $dashboard_filter_values['check_number']);
  }

  if(isset($_GET['dashboard_filter_users_id']) && is_array($_GET['dashboard_filter_users_id']) && count($_GET['dashboard_filter_users_id'])>0){
    $users_id_items = [];
    foreach($_GET['dashboard_filter_users_id'] as $users_id_item){
      $users_id_item = holu_escape($users_id_item);
      if($users_id_item!==''){
        $dashboard_filter_values['users_id'][] = $users_id_item;
        $users_id_items[] = dashboard_filter_sql_value($users_id_item);
      }
    }
    if(count($users_id_items)>0){
      $users_id_sql = implode(',', $users_id_items);
      dashboard_add_table_filter([
        'income' => " AND incomes.users_id IN ($users_id_sql) ",
        'expense' => " AND expenses.users_id IN ($users_id_sql) ",
        'exchange' => " AND exchanges.users_id IN ($users_id_sql) ",
        'transfer' => " AND (transfers.users_id IN ($users_id_sql) OR transfers.approved_by IN ($users_id_sql)) ",
      ]);
      dashboard_add_filter_query('dashboard_filter_users_id', $dashboard_filter_values['users_id']);
      dashboard_add_filter_label('Added By', implode(', ', $dashboard_filter_values['users_id']));
    }
  }

  $dashboard_excel_data .= $dashboard_advanced_filter_query;
  // Keep applied filters active while rendering the advanced filter panel closed after form submission.
  $dashboard_filter_panel_is_open = false;

  $dashboard_transfer_out_scope = "(($transfer_from_access_condition) OR (transfers.users_id='$holu_users_id' AND NOT ($transfer_to_access_condition)))";
  $dashboard_transfer_in_scope = "($transfer_to_access_condition)";

  if($dashboard_filter_values['branch']!='' && $dashboard_filter_values['branch']!='0'){
    $dashboard_transfer_branch_sql = dashboard_filter_sql_value($dashboard_filter_values['branch']);
    $dashboard_transfer_out_scope = "transfers.from_branch=$dashboard_transfer_branch_sql";
    $dashboard_transfer_in_scope = "transfers.to_branch=$dashboard_transfer_branch_sql";
  }elseif($dashboard_filter_values['province']!='' && $dashboard_filter_values['province']!='0'){
    $dashboard_transfer_province_sql = dashboard_filter_sql_value($dashboard_filter_values['province']);
    $dashboard_transfer_out_scope = "transfers.from_province=$dashboard_transfer_province_sql";
    $dashboard_transfer_in_scope = "transfers.to_province=$dashboard_transfer_province_sql";
  }

  $transactions_query = "
    SELECT * FROM (
      SELECT
        incomes.id AS transaction_id,
        'Income' AS transaction_type,
        incomes.sub_categories_id AS transaction_sub_categories_id,
        incomes.province AS transaction_province,
        incomes.branch AS transaction_branch,
        incomes.income_date AS transaction_date,
        incomes.income_amount AS transaction_amount,
        incomes.currency AS transaction_currency,
        incomes.description AS transaction_description,
        incomes.users_id AS transaction_users_id,
        incomes.check_number AS transaction_check_number,
        incomes.sib_number AS transaction_sib_number,
        incomes.tms_markup AS transaction_tms_markup,
        incomes.qb_markup AS transaction_qb_markup,
        incomes.sib_markup AS transaction_sib_markup,
        incomes.ad_markup AS transaction_ad_markup,
        incomes.additional_informations AS transaction_additional_informations,
        '' AS transaction_approve_description,
        '' AS transaction_from_province,
        '' AS transaction_to_province,
        '' AS transaction_from_branch,
        '' AS transaction_to_branch,
        '' AS transaction_transfer_side
      FROM `incomes`
      WHERE incomes.deleted='0'
      AND $income_access_condition
      AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
      {$dashboard_filtering_data['income']}
      UNION ALL
      SELECT
        expenses.id AS transaction_id,
        'Expense' AS transaction_type,
        expenses.sub_categories_id AS transaction_sub_categories_id,
        expenses.province AS transaction_province,
        expenses.branch AS transaction_branch,
        expenses.expense_date AS transaction_date,
        expenses.expense_amount AS transaction_amount,
        expenses.currency AS transaction_currency,
        expenses.description AS transaction_description,
        expenses.users_id AS transaction_users_id,
        expenses.check_number AS transaction_check_number,
        '' AS transaction_sib_number,
        expenses.tms_markup AS transaction_tms_markup,
        expenses.qb_markup AS transaction_qb_markup,
        expenses.sib_markup AS transaction_sib_markup,
        expenses.ad_markup AS transaction_ad_markup,
        expenses.additional_informations AS transaction_additional_informations,
        '' AS transaction_approve_description,
        '' AS transaction_from_province,
        '' AS transaction_to_province,
        '' AS transaction_from_branch,
        '' AS transaction_to_branch,
        '' AS transaction_transfer_side
      FROM `expenses`
      WHERE expenses.deleted='0'
      AND $expense_access_condition
      AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
      {$dashboard_filtering_data['expense']}
      UNION ALL
      SELECT
        exchanges.id AS transaction_id,
        'Exchange' AS transaction_type,
        0 AS transaction_sub_categories_id,
        exchanges.province AS transaction_province,
        exchanges.branch AS transaction_branch,
        exchanges.exchange_date AS transaction_date,
        CONCAT(exchanges.from_amount, ' to ', exchanges.to_amount) AS transaction_amount,
        CONCAT(exchanges.from_currency, ' to ', exchanges.to_currency) AS transaction_currency,
        exchanges.description AS transaction_description,
        exchanges.users_id AS transaction_users_id,
        '' AS transaction_check_number,
        '' AS transaction_sib_number,
        exchanges.tms_markup AS transaction_tms_markup,
        exchanges.qb_markup AS transaction_qb_markup,
        exchanges.sib_markup AS transaction_sib_markup,
        exchanges.ad_markup AS transaction_ad_markup,
        '' AS transaction_additional_informations,
        '' AS transaction_approve_description,
        '' AS transaction_from_province,
        '' AS transaction_to_province,
        '' AS transaction_from_branch,
        '' AS transaction_to_branch,
        '' AS transaction_transfer_side
      FROM `exchanges`
      WHERE exchanges.deleted='0'
      AND $exchange_access_condition
      $accessed_sub_categories_exchange
      {$dashboard_filtering_data['exchange']}
      UNION ALL
      SELECT
        transfers.id AS transaction_id,
        'Transfer' AS transaction_type,
        0 AS transaction_sub_categories_id,
        CONCAT(transfers.from_province, ' to ', transfers.to_province) AS transaction_province,
        CONCAT(transfers.from_branch, ' to ', transfers.to_branch) AS transaction_branch,
        transfers.transfer_date AS transaction_date,
        transfers.transfer_amount AS transaction_amount,
        transfers.currency AS transaction_currency,
        transfers.description AS transaction_description,
        transfers.users_id AS transaction_users_id,
        '' AS transaction_check_number,
        '' AS transaction_sib_number,
        transfers.tms_markup AS transaction_tms_markup,
        CONCAT(transfers.qb_markup, ',', transfers.rqb_markup) AS transaction_qb_markup,
        transfers.sib_markup AS transaction_sib_markup,
        transfers.ad_markup AS transaction_ad_markup,
        '' AS transaction_additional_informations,
        transfers.approve_description AS transaction_approve_description,
        transfers.from_province AS transaction_from_province,
        transfers.to_province AS transaction_to_province,
        transfers.from_branch AS transaction_from_branch,
        transfers.to_branch AS transaction_to_branch,
        CASE
          WHEN $dashboard_transfer_in_scope THEN 'Transfer To'
          WHEN $dashboard_transfer_out_scope THEN 'Transfer From'
          ELSE 'Transfer From'
        END AS transaction_transfer_side
      FROM `transfers`
      WHERE transfers.deleted='0'
      AND transfers.is_approved='1'
      AND ((($transfer_from_access_condition) OR ($transfer_to_access_condition)) OR transfers.users_id='$holu_users_id')
      $accessed_sub_categories_transfer
      {$dashboard_filtering_data['transfer']}
    ) AS dashboard_transactions
  ";

  $transaction_sq = $db->query("$transactions_query WHERE 1 $dashboard_date_filtering_data ORDER BY transaction_date DESC, transaction_id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(transaction_id) as record FROM ($transactions_query WHERE 1 $dashboard_date_filtering_data) AS counted_dashboard_transactions");
  extract($Pagenation->fetch());

  $dashboard_income_date_filter = "";
  $dashboard_expense_date_filter = "";
  $dashboard_exchange_date_filter = "";
  $dashboard_transfer_date_filter = "";

  if($dashboard_from_date!=''){
    $dashboard_income_date_filter .= " AND incomes.income_date>='".$dashboard_from_date."' ";
    $dashboard_expense_date_filter .= " AND expenses.expense_date>='".$dashboard_from_date."' ";
    $dashboard_exchange_date_filter .= " AND exchanges.exchange_date>='".$dashboard_from_date."' ";
    $dashboard_transfer_date_filter .= " AND transfers.transfer_date>='".$dashboard_from_date."' ";
  }
  if($dashboard_to_date!=''){
    $dashboard_income_date_filter .= " AND incomes.income_date<='".$dashboard_to_date."' ";
    $dashboard_expense_date_filter .= " AND expenses.expense_date<='".$dashboard_to_date."' ";
    $dashboard_exchange_date_filter .= " AND exchanges.exchange_date<='".$dashboard_to_date."' ";
    $dashboard_transfer_date_filter .= " AND transfers.transfer_date<='".$dashboard_to_date."' ";
  }

  $dashboard_closing_income_date_filter = $dashboard_income_date_filter;
  $dashboard_closing_expense_date_filter = $dashboard_expense_date_filter;
  $dashboard_closing_exchange_date_filter = $dashboard_exchange_date_filter;
  $dashboard_closing_transfer_date_filter = $dashboard_transfer_date_filter;

  if($dashboard_to_date!=''){
    $dashboard_closing_income_date_filter = " AND incomes.income_date<='".$dashboard_to_date."' ";
    $dashboard_closing_expense_date_filter = " AND expenses.expense_date<='".$dashboard_to_date."' ";
    $dashboard_closing_exchange_date_filter = " AND exchanges.exchange_date<='".$dashboard_to_date."' ";
    $dashboard_closing_transfer_date_filter = " AND transfers.transfer_date<='".$dashboard_to_date."' ";
  }

  $dashboard_total_income_sq = $db->query(
    "SELECT
      SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) AS total_income_afn,
      SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) AS total_income_usd,
      SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) AS total_income_irt
    FROM `incomes`
    WHERE incomes.deleted='0'
    AND $income_access_condition
    AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
    $dashboard_income_date_filter
    {$dashboard_filtering_data['income']}"
  );
  $dashboard_total_income_row = $dashboard_total_income_sq->fetch();

  $dashboard_total_expense_sq = $db->query(
    "SELECT
      SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) AS total_expense_afn,
      SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) AS total_expense_usd,
      SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) AS total_expense_irt
    FROM `expenses`
    WHERE expenses.deleted='0'
    AND $expense_access_condition
    AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
    $dashboard_expense_date_filter
    {$dashboard_filtering_data['expense']}"
  );
  $dashboard_total_expense_row = $dashboard_total_expense_sq->fetch();

  $dashboard_total_exchange_sq = $db->query(
    "SELECT
      SUM(CASE WHEN from_currency='AFN' AND to_currency='USD' THEN from_amount ELSE 0 END) AS total_from_afn,
      SUM(CASE WHEN from_currency='AFN' AND to_currency='IRT' THEN from_amount ELSE 0 END) AS total_from_afn2,
      SUM(CASE WHEN from_currency='USD' AND to_currency='AFN' THEN from_amount ELSE 0 END) AS total_from_usd,
      SUM(CASE WHEN from_currency='IRT' AND to_currency='AFN' THEN from_amount ELSE 0 END) AS total_from_irt,
      SUM(CASE WHEN to_currency='AFN' AND from_currency='USD' THEN to_amount ELSE 0 END) AS total_to_afn,
      SUM(CASE WHEN to_currency='AFN' AND from_currency='IRT' THEN to_amount ELSE 0 END) AS total_to_afn2,
      SUM(CASE WHEN to_currency='USD' AND from_currency='AFN' THEN to_amount ELSE 0 END) AS total_to_usd,
      SUM(CASE WHEN to_currency='IRT' AND from_currency='AFN' THEN to_amount ELSE 0 END) AS total_to_irt
    FROM `exchanges`
    WHERE exchanges.deleted='0'
    AND $exchange_access_condition
    $dashboard_exchange_date_filter
    $accessed_sub_categories_exchange
    {$dashboard_filtering_data['exchange']}"
  );
  $dashboard_total_exchange_row = $dashboard_total_exchange_sq->fetch();

  $dashboard_total_transfer_sq = $db->query(
    "SELECT
      SUM(CASE WHEN currency='AFN' AND $dashboard_transfer_out_scope THEN transfer_amount ELSE 0 END) AS total_transfer_out_afn,
      SUM(CASE WHEN currency='AFN' AND $dashboard_transfer_in_scope THEN transfer_amount ELSE 0 END) AS total_transfer_in_afn,
      SUM(CASE WHEN currency='USD' AND $dashboard_transfer_out_scope THEN transfer_amount ELSE 0 END) AS total_transfer_out_usd,
      SUM(CASE WHEN currency='USD' AND $dashboard_transfer_in_scope THEN transfer_amount ELSE 0 END) AS total_transfer_in_usd,
      SUM(CASE WHEN currency='IRT' AND $dashboard_transfer_out_scope THEN transfer_amount ELSE 0 END) AS total_transfer_out_irt,
      SUM(CASE WHEN currency='IRT' AND $dashboard_transfer_in_scope THEN transfer_amount ELSE 0 END) AS total_transfer_in_irt
    FROM `transfers`
    WHERE transfers.deleted='0'
    AND transfers.is_approved='1'
    AND ((($transfer_from_access_condition) OR ($transfer_to_access_condition)) OR transfers.users_id='$holu_users_id')
    $dashboard_transfer_date_filter
    $accessed_sub_categories_transfer
    {$dashboard_filtering_data['transfer']}"
  );
  $dashboard_total_transfer_row = $dashboard_total_transfer_sq->fetch();

  $dashboard_total_afn = ($dashboard_total_income_row['total_income_afn'] ?? 0)
    - ($dashboard_total_expense_row['total_expense_afn'] ?? 0)
    + ($dashboard_total_exchange_row['total_to_afn'] ?? 0)
    + ($dashboard_total_exchange_row['total_to_afn2'] ?? 0)
    - ($dashboard_total_exchange_row['total_from_afn'] ?? 0)
    - ($dashboard_total_exchange_row['total_from_afn2'] ?? 0)
    - ($dashboard_total_transfer_row['total_transfer_out_afn'] ?? 0)
    + ($dashboard_total_transfer_row['total_transfer_in_afn'] ?? 0);
  $dashboard_total_usd = ($dashboard_total_income_row['total_income_usd'] ?? 0)
    - ($dashboard_total_expense_row['total_expense_usd'] ?? 0)
    + ($dashboard_total_exchange_row['total_to_usd'] ?? 0)
    - ($dashboard_total_exchange_row['total_from_usd'] ?? 0)
    - ($dashboard_total_transfer_row['total_transfer_out_usd'] ?? 0)
    + ($dashboard_total_transfer_row['total_transfer_in_usd'] ?? 0);
  $dashboard_total_irt = ($dashboard_total_income_row['total_income_irt'] ?? 0)
    - ($dashboard_total_expense_row['total_expense_irt'] ?? 0)
    + ($dashboard_total_exchange_row['total_to_irt'] ?? 0)
    - ($dashboard_total_exchange_row['total_from_irt'] ?? 0)
    - ($dashboard_total_transfer_row['total_transfer_out_irt'] ?? 0)
    + ($dashboard_total_transfer_row['total_transfer_in_irt'] ?? 0);

  $dashboard_closing_income_sq = $db->query(
    "SELECT
      SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) AS closing_income_afn,
      SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) AS closing_income_usd,
      SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) AS closing_income_irt
    FROM `incomes`
    WHERE incomes.deleted='0'
    AND $income_access_condition
    AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
    $dashboard_closing_income_date_filter
    {$dashboard_filtering_data['income']}"
  );
  $dashboard_closing_income_row = $dashboard_closing_income_sq->fetch();

  $dashboard_closing_expense_sq = $db->query(
    "SELECT
      SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) AS closing_expense_afn,
      SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) AS closing_expense_usd,
      SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) AS closing_expense_irt
    FROM `expenses`
    WHERE expenses.deleted='0'
    AND $expense_access_condition
    AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
    $dashboard_closing_expense_date_filter
    {$dashboard_filtering_data['expense']}"
  );
  $dashboard_closing_expense_row = $dashboard_closing_expense_sq->fetch();

  $dashboard_closing_exchange_sq = $db->query(
    "SELECT
      SUM(CASE WHEN from_currency='AFN' AND to_currency='USD' THEN from_amount ELSE 0 END) AS closing_from_afn,
      SUM(CASE WHEN from_currency='AFN' AND to_currency='IRT' THEN from_amount ELSE 0 END) AS closing_from_afn2,
      SUM(CASE WHEN from_currency='USD' AND to_currency='AFN' THEN from_amount ELSE 0 END) AS closing_from_usd,
      SUM(CASE WHEN from_currency='IRT' AND to_currency='AFN' THEN from_amount ELSE 0 END) AS closing_from_irt,
      SUM(CASE WHEN to_currency='AFN' AND from_currency='USD' THEN to_amount ELSE 0 END) AS closing_to_afn,
      SUM(CASE WHEN to_currency='AFN' AND from_currency='IRT' THEN to_amount ELSE 0 END) AS closing_to_afn2,
      SUM(CASE WHEN to_currency='USD' AND from_currency='AFN' THEN to_amount ELSE 0 END) AS closing_to_usd,
      SUM(CASE WHEN to_currency='IRT' AND from_currency='AFN' THEN to_amount ELSE 0 END) AS closing_to_irt
    FROM `exchanges`
    WHERE exchanges.deleted='0'
    AND $exchange_access_condition
    $dashboard_closing_exchange_date_filter
    $accessed_sub_categories_exchange
    {$dashboard_filtering_data['exchange']}"
  );
  $dashboard_closing_exchange_row = $dashboard_closing_exchange_sq->fetch();

  $dashboard_closing_transfer_sq = $db->query(
    "SELECT
      SUM(CASE WHEN currency='AFN' AND $dashboard_transfer_out_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_out_afn,
      SUM(CASE WHEN currency='AFN' AND $dashboard_transfer_in_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_in_afn,
      SUM(CASE WHEN currency='USD' AND $dashboard_transfer_out_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_out_usd,
      SUM(CASE WHEN currency='USD' AND $dashboard_transfer_in_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_in_usd,
      SUM(CASE WHEN currency='IRT' AND $dashboard_transfer_out_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_out_irt,
      SUM(CASE WHEN currency='IRT' AND $dashboard_transfer_in_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_in_irt
    FROM `transfers`
    WHERE transfers.deleted='0'
    AND transfers.is_approved='1'
    AND ((($transfer_from_access_condition) OR ($transfer_to_access_condition)) OR transfers.users_id='$holu_users_id')
    $dashboard_closing_transfer_date_filter
    $accessed_sub_categories_transfer
    {$dashboard_filtering_data['transfer']}"
  );
  $dashboard_closing_transfer_row = $dashboard_closing_transfer_sq->fetch();

  $dashboard_closing_afn = ($dashboard_closing_income_row['closing_income_afn'] ?? 0)
    - ($dashboard_closing_expense_row['closing_expense_afn'] ?? 0)
    + ($dashboard_closing_exchange_row['closing_to_afn'] ?? 0)
    + ($dashboard_closing_exchange_row['closing_to_afn2'] ?? 0)
    - ($dashboard_closing_exchange_row['closing_from_afn'] ?? 0)
    - ($dashboard_closing_exchange_row['closing_from_afn2'] ?? 0)
    - ($dashboard_closing_transfer_row['closing_transfer_out_afn'] ?? 0)
    + ($dashboard_closing_transfer_row['closing_transfer_in_afn'] ?? 0);
  $dashboard_closing_usd = ($dashboard_closing_income_row['closing_income_usd'] ?? 0)
    - ($dashboard_closing_expense_row['closing_expense_usd'] ?? 0)
    + ($dashboard_closing_exchange_row['closing_to_usd'] ?? 0)
    - ($dashboard_closing_exchange_row['closing_from_usd'] ?? 0)
    - ($dashboard_closing_transfer_row['closing_transfer_out_usd'] ?? 0)
    + ($dashboard_closing_transfer_row['closing_transfer_in_usd'] ?? 0);
  $dashboard_closing_irt = ($dashboard_closing_income_row['closing_income_irt'] ?? 0)
    - ($dashboard_closing_expense_row['closing_expense_irt'] ?? 0)
    + ($dashboard_closing_exchange_row['closing_to_irt'] ?? 0)
    - ($dashboard_closing_exchange_row['closing_from_irt'] ?? 0)
    - ($dashboard_closing_transfer_row['closing_transfer_out_irt'] ?? 0)
    + ($dashboard_closing_transfer_row['closing_transfer_in_irt'] ?? 0);
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
          if(check_access("system_accessibility/report/report_transaction/view_transaction/")==1){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="dashboard-transaction-summary" aria-label="Transaction totals for selected date range">
                <div class="dashboard-summary-card dashboard-summary-income">
                  <div class="dashboard-summary-title"><i class="fa fa-arrow-down"></i> Income</div>
                  <div class="dashboard-summary-values">
                    <span><?php echo number_format($dashboard_total_income_row['total_income_afn'] ?? 0, 2); ?> AFN</span>
                    <span><?php echo number_format($dashboard_total_income_row['total_income_usd'] ?? 0, 2); ?> USD</span>
                    <span><?php echo number_format($dashboard_total_income_row['total_income_irt'] ?? 0, 2); ?> IRT</span>
                  </div>
                </div>
                <div class="dashboard-summary-card dashboard-summary-expense">
                  <div class="dashboard-summary-title"><i class="fa fa-arrow-up"></i> Expense</div>
                  <div class="dashboard-summary-values">
                    <span><?php echo number_format($dashboard_total_expense_row['total_expense_afn'] ?? 0, 2); ?> AFN</span>
                    <span><?php echo number_format($dashboard_total_expense_row['total_expense_usd'] ?? 0, 2); ?> USD</span>
                    <span><?php echo number_format($dashboard_total_expense_row['total_expense_irt'] ?? 0, 2); ?> IRT</span>
                  </div>
                </div>
                <div class="dashboard-summary-card dashboard-summary-exchange">
                  <div class="dashboard-summary-title"><i class="fa fa-exchange-alt"></i> Exchange</div>
                  <div class="dashboard-summary-values dashboard-summary-routes">
                    <span><?php echo number_format($dashboard_total_exchange_row['total_from_afn'] ?? 0, 2); ?> AFN to <?php echo number_format($dashboard_total_exchange_row['total_to_usd'] ?? 0, 2); ?> USD</span>
                    <span><?php echo number_format($dashboard_total_exchange_row['total_from_usd'] ?? 0, 2); ?> USD to <?php echo number_format($dashboard_total_exchange_row['total_to_afn'] ?? 0, 2); ?> AFN</span>
                    <span><?php echo number_format($dashboard_total_exchange_row['total_from_afn2'] ?? 0, 2); ?> AFN to <?php echo number_format($dashboard_total_exchange_row['total_to_irt'] ?? 0, 2); ?> IRT</span>
                    <span><?php echo number_format($dashboard_total_exchange_row['total_from_irt'] ?? 0, 2); ?> IRT to <?php echo number_format($dashboard_total_exchange_row['total_to_afn2'] ?? 0, 2); ?> AFN</span>
                  </div>
                </div>
                <div class="dashboard-summary-card dashboard-summary-transfer">
                  <div class="dashboard-summary-title"><i class="fa fa-random"></i> Transfer</div>
                  <div class="dashboard-summary-values">
                    <span><?php echo number_format(($dashboard_total_transfer_row['total_transfer_in_afn'] ?? 0) - ($dashboard_total_transfer_row['total_transfer_out_afn'] ?? 0), 2); ?> AFN</span>
                    <span><?php echo number_format(($dashboard_total_transfer_row['total_transfer_in_usd'] ?? 0) - ($dashboard_total_transfer_row['total_transfer_out_usd'] ?? 0), 2); ?> USD</span>
                    <span><?php echo number_format(($dashboard_total_transfer_row['total_transfer_in_irt'] ?? 0) - ($dashboard_total_transfer_row['total_transfer_out_irt'] ?? 0), 2); ?> IRT</span>
                  </div>
                </div>
                <div class="dashboard-summary-card dashboard-summary-total">
                  <div class="dashboard-summary-title"><i class="fa fa-balance-scale"></i> Total</div>
                  <div class="dashboard-summary-values">
                    <span><?php echo number_format($dashboard_total_afn, 2); ?> AFN</span>
                    <span><?php echo number_format($dashboard_total_usd, 2); ?> USD</span>
                    <span><?php echo number_format($dashboard_total_irt, 2); ?> IRT</span>
                  </div>
                </div>
                <div class="dashboard-summary-card dashboard-summary-closing">
                  <div class="dashboard-summary-title"><i class="fa fa-wallet"></i> Total with Closing</div>
                  <div class="dashboard-summary-values">
                    <span><?php echo number_format($dashboard_closing_afn, 2); ?> AFN</span>
                    <span><?php echo number_format($dashboard_closing_usd, 2); ?> USD</span>
                    <span><?php echo number_format($dashboard_closing_irt, 2); ?> IRT</span>
                  </div>
                </div>
              </div>
              <div class="card-box card-box-header dashboard-transactions-header">
                <h4 class="header-title" id="dashboard_transactions_report_header" data-report-base="Report of Transactions" data-report-date-range="<?php echo htmlspecialchars($dashboard_date_range_display, ENT_QUOTES, 'UTF-8'); ?>">
                  <?php echo get_table_header('fa fa-list', $dashboard_report_header, $transaction_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php if(check_access("system_accessibility/report/report_transaction/export_excel/")==1){ ?>
                <a id="dashboard_export_excel_btn" href="controller_excel.php?excel_type=dashboard_transactions<?php echo $dashboard_excel_data; ?>"><button type="button" class="btn waves-effect waves-light adder_button"><i class="far fa-file-excel"></i> Export Excel</button></a>
                <?php } ?>

                <button type="button" class="btn waves-effect waves-light adder_button dashboard-filter-toggle" id="dashboard_transaction_filter_toggle" aria-expanded="<?php echo $dashboard_filter_panel_is_open ? 'true' : 'false'; ?>" aria-controls="dashboard_transaction_filter_panel"><i class="fa fa-filter"></i> Filter</button>

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
                      <span>Transactions shown in this report</span>
                    </div>
                    <div class="dashboard-date-range-quick-list">
                      <?php
                      foreach($dashboard_date_range_options as $dashboard_date_range_key => $dashboard_date_range_option){
                        if($dashboard_date_range_key!='custom'){
                          ?>
                          <a class="dashboard-date-range-option <?php echo $dashboard_date_range==$dashboard_date_range_key ? 'active' : ''; ?>" href="report_transaction.php?date_range=<?php echo $dashboard_date_range_key; ?>">
                            <i class="far fa-clock"></i>
                            <span><?php echo $dashboard_date_range_option; ?></span>
                          </a>
                          <?php
                        }
                      }
                      ?>
                    </div>
                    <form id="dashboard_custom_date_range" class="dashboard-custom-date-range" action="report_transaction.php" method="get">
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
              </div>

              <div class="dashboard-filter-panel <?php echo $dashboard_filter_panel_is_open ? 'is-open' : ''; ?>" id="dashboard_transaction_filter_panel" aria-hidden="<?php echo $dashboard_filter_panel_is_open ? 'false' : 'true'; ?>">
                <form class="dashboard-filter-form" id="dashboard_transaction_filter_form" role="form" action="report_transaction.php" method="GET">
                  <div class="dashboard-filter-panel-topline"></div>
                  <input type="hidden" name="date_range" value="<?php echo htmlspecialchars($dashboard_date_range, ENT_QUOTES, 'UTF-8'); ?>">
                  <?php if($dashboard_date_range=='custom'){ ?>
                    <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($dashboard_custom_from_date, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($dashboard_custom_to_date, ENT_QUOTES, 'UTF-8'); ?>">
                  <?php } ?>
                  <div class="dashboard-filter-form-header">
                    <div>
                      <span class="dashboard-filter-eyebrow"><i class="fa fa-sliders-h"></i> Advanced filters</span>
                      <h5>Refine report transactions</h5>
                      <p>Choose filters and apply them to the transaction list and balance summary.</p>
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

                    <div class="dashboard-filter-field dashboard-filter-field-half">
                      <label for="dashboard_filter_from_date">Date from</label>
                      <div class="dashboard-filter-input-icon">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" id="dashboard_filter_from_date" name="dashboard_filter_from_date" class="form-control date_picker" placeholder="From" value="<?php echo htmlspecialchars($dashboard_filter_values['from_date'], ENT_QUOTES, 'UTF-8'); ?>">
                      </div>
                    </div>

                    <div class="dashboard-filter-field dashboard-filter-field-half">
                      <label for="dashboard_filter_to_date">Date to</label>
                      <div class="dashboard-filter-input-icon">
                        <i class="far fa-calendar-check"></i>
                        <input type="text" id="dashboard_filter_to_date" name="dashboard_filter_to_date" class="form-control date_picker" placeholder="To" value="<?php echo htmlspecialchars($dashboard_filter_values['to_date'], ENT_QUOTES, 'UTF-8'); ?>">
                      </div>
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
                    <small><i class="fa fa-info-circle"></i> Filters are applied to both the table and the summary totals above.</small>
                    <div>
                      <a class="btn dashboard-filter-reset" href="report_transaction.php?date_range=<?php echo urlencode($dashboard_date_range); ?><?php echo $dashboard_date_range=='custom' ? '&from_date='.urlencode($dashboard_custom_from_date).'&to_date='.urlencode($dashboard_custom_to_date) : ''; ?>"><i class="fa fa-undo"></i> Clear</a>
                      <button type="button" class="btn dashboard-filter-close" id="dashboard_transaction_filter_close"><i class="fa fa-times"></i> Close</button>
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
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Type</th>
                        <th>Sub Category</th>
                        <th>Date</th>
                        <th>Province</th>
                        <th>Branch</th>
                        <th>Check Number</th>
                        <th>Additional Information</th>
                        <th>Markups</th>
                        <th>SIB Number</th>
                        <th>Added By</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($transaction_sq->rowCount()>0){
                        while($transaction_row = $transaction_sq->fetch()){
                          $transaction_type = $transaction_row['transaction_type'];
                          $sub_category = '';
                          $check_number_container = '';
                          $sib_number_container = '';

                          if($transaction_row['transaction_sub_categories_id']!=0){
                            $sub_category = get_col('sub_categories', 'sub_category_name', 'id', $transaction_row['transaction_sub_categories_id']);
                          }

                          if(!empty($transaction_row['transaction_check_number'])){
                            $check_number_container = htmlspecialchars($transaction_row['transaction_check_number'], ENT_QUOTES, 'UTF-8');
                          }

                          if($transaction_type=='Income' && has_customer_reference_for_sib($transaction_row['transaction_additional_informations'] ?? '')){
                            $sib_number_container = '<p>'.$transaction_row['transaction_sib_number'].'</p>';
                          }

                          $transaction_description = dashboard_transaction_description($transaction_row);
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $transaction_description; ?></p></td>
                            <td><?php echo $transaction_row['transaction_amount']; ?></td>
                            <td><?php echo $transaction_row['transaction_currency']; ?></td>
                            <td><?php echo $transaction_row['transaction_type']; ?></td>
                            <td><?php echo $sub_category; ?></td>
                            <td><?php echo $transaction_row['transaction_date']; ?></td>
                            <td><?php echo $transaction_row['transaction_province']; ?></td>
                            <td><?php echo $transaction_row['transaction_branch']; ?></td>
                            <td class="text-center"><?php echo $check_number_container; ?></td>
                            <td class="text-center">
                              <?php
                                if ($transaction_type != 'Transfer') {
                                  $json = $transaction_row['transaction_additional_informations'] ?? '';

                                  if (!empty($json)) {
                                    $decoded = json_decode($json);

                                    if (json_last_error() === JSON_ERROR_NONE) {
                                      echo print_ai_labels($decoded);
                                    }
                                  }
                                }
                              ?>
                            </td>
                            <td class="text-center" id="markups<?php echo $transaction_type.$transaction_row['transaction_id']; ?>">
                              <?php echo get_markups(
                                'system_accessibility/report/report_transaction/',
                                $transaction_type,
                                $transaction_row['transaction_id'],
                                $transaction_row['transaction_tms_markup'],
                                $transaction_row['transaction_qb_markup'],
                                $transaction_row['transaction_sib_markup'],
                                $transaction_row['transaction_ad_markup']
                              ); ?>
                            </td>
                            <td class="text-center"><?php echo $sib_number_container; ?></td>
                            <td class="text-center"><?php echo get_col('users', 'username', 'id', $transaction_row['transaction_users_id']); ?></td>
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
    $('#dashboard_transaction_filter_toggle').on('click', function(){
      var $panel = $('#dashboard_transaction_filter_panel');
      var isOpen = $panel.toggleClass('is-open').hasClass('is-open');
      $(this).attr('aria-expanded', isOpen ? 'true' : 'false');
      $panel.attr('aria-hidden', isOpen ? 'false' : 'true');
    });

    function updateDashboardTransactionsReportHeader(){
      var $header = $('#dashboard_transactions_report_header');
      if(!$header.length){
        return;
      }

      var parts = [
        $header.data('report-base') || 'Report of Transactions',
        $header.data('report-date-range') || ''
      ];

      var province = $('#dashboard_filter_province').val();
      var branch = $('#dashboard_filter_branch').val();

      if(province && province !== '0'){
        parts.push($('#dashboard_filter_province option:selected').text());
      }

      if(branch && branch !== '0'){
        parts.push($('#dashboard_filter_branch option:selected').text());
      }

      var headerText = $.map(parts, function(part){
        return $.trim(part);
      }).filter(function(part){
        return part.length > 0;
      }).join(' • ');

      var iconNode = $header.children('i').first().get(0);
      if(iconNode && iconNode.nextSibling && iconNode.nextSibling.nodeType === 3){
        iconNode.nextSibling.nodeValue = ' ' + headerText + ' ';
      }
    }

    $('#dashboard_filter_province, #dashboard_filter_branch').on('change', updateDashboardTransactionsReportHeader);
    $(document).ajaxComplete(function(event, xhr, settings){
      var requestData = settings && settings.data ? settings.data : '';
      var branchOptionsReloaded = false;

      if(typeof requestData === 'string'){
        branchOptionsReloaded = requestData.indexOf('operation=get_branch_option') !== -1;
      }else if(requestData.operation){
        branchOptionsReloaded = requestData.operation === 'get_branch_option';
      }

      if(branchOptionsReloaded){
        updateDashboardTransactionsReportHeader();
      }
    });


    var dashboardTransactionComponents = <?php echo print_access_sub_categories(); ?>;
    var dashboardSelectedTransactionComponents = <?php echo json_encode(array_values(array_filter(explode(',', $dashboard_filter_values['transaction_components'])))); ?>;
    var dashboardTransactionComponentsTree = new Tree('#dashboard_filter_transaction_components_container', {
      data: [{ id: 'dashboard_filter_transaction_components_root', text: 'Transaction Components', children: dashboardTransactionComponents }],
      closeDepth: 2,
      loaded: function(){
        this.values = dashboardSelectedTransactionComponents;
        $('#dashboard_filter_transaction_components').val(this.values.join(','));
        updateDashboardTransactionComponentSummary(this.selectedNodes || []);
      },
      onChange: function(){
        $('#dashboard_filter_transaction_components').val(this.values.join(','));
        updateDashboardTransactionComponentSummary(this.selectedNodes || []);
      }
    });

    function updateDashboardTransactionComponentSummary(selectedNodes){
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

    $('#dashboard_transaction_filter_close').on('click', function(){
      $('#dashboard_transaction_filter_panel').removeClass('is-open').attr('aria-hidden', 'true');
      $('#dashboard_transaction_filter_toggle').attr('aria-expanded', 'false');
    });


    $('#dashboard_custom_date_range').on('submit', function(){
      var fromDate = $('#dashboard_from_date').val();
      var toDate = $('#dashboard_to_date').val();

      if(fromDate && toDate && fromDate > toDate){
        $('#dashboard_from_date').val(toDate);
        $('#dashboard_to_date').val(fromDate);
      }
    });
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
