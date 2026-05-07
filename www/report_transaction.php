<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Transactions"];

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

  $income_filtering_data = "";
  $expense_filtering_data = "";
  $exchange_filtering_data = "";
  $transfer_filtering_data = "";
  $closing_income_filtering_data = "";
  $closing_expense_filtering_data = "";
  $closing_exchange_filtering_data = "";
  $closing_transfer_filtering_data = "";

  $loaded_transaction_components = '';

  $province = "0";
  $branch = "";
  $from_date = "";
  $to_date = "";
  $customer_name = "";
  $customer_id = "";
  $description = "";
  $markup = "";
  $unmark = "";
  $currency = "";
  $transaction_components = "";
  $transaction_type = "";
  $categories_id = "0";
  $sub_categories_id = "0";
  $users_id = [];
  $amount = "0";
  $sib_number = "";
  $check_number = "";

  $excel_data = "";
  $transfer_out_scope = "0";
  $transfer_in_scope = "0";

  if(isset($_GET['flag_operation']) AND $_GET['flag_operation']=="report_transaction"){

    if(isset($_GET['province']) AND !empty($_GET['province'])){
      $province = $_GET['province'];
      $income_filtering_data .= " AND incomes.province='".$province."' ";
      $expense_filtering_data .= " AND expenses.province='".$province."' ";
      $exchange_filtering_data .= " AND exchanges.province='".$province."' ";
      $transfer_filtering_data .= " AND (transfers.from_province='".$province."' OR to_province='".$province."') ";
      $closing_income_filtering_data .= " AND incomes.province='".$province."' ";
      $closing_expense_filtering_data .= " AND expenses.province='".$province."' ";
      $closing_exchange_filtering_data .= " AND exchanges.province='".$province."' ";
      $closing_transfer_filtering_data .= " AND (transfers.from_province='".$province."' OR to_province='".$province."') ";

      $excel_data .= "&province=".$province;
    }
    if(isset($_GET['branch']) AND !empty($_GET['branch'])){
      $branch = $_GET['branch'];
      $income_filtering_data .= " AND incomes.branch='".$branch."' ";
      $expense_filtering_data .= " AND expenses.branch='".$branch."' ";
      $exchange_filtering_data .= " AND exchanges.branch='".$branch."' ";
      $transfer_filtering_data .= " AND (transfers.from_branch='".$branch."' OR transfers.to_branch='".$branch."') ";
      $closing_income_filtering_data .= " AND incomes.branch='".$branch."' ";
      $closing_expense_filtering_data .= " AND expenses.branch='".$branch."' ";
      $closing_exchange_filtering_data .= " AND exchanges.branch='".$branch."' ";
      $closing_transfer_filtering_data .= " AND (transfers.from_branch='".$branch."' OR transfers.to_branch='".$branch."') ";

      $excel_data .= "&branch=".$branch;
    }
    if(isset($_GET['from_date']) AND !empty($_GET['from_date'])){
      $from_date = $_GET['from_date'];
      $income_filtering_data .= " AND incomes.income_date>='".$from_date."' ";
      $expense_filtering_data .= " AND expenses.expense_date>='".$from_date."' ";
      $exchange_filtering_data .= " AND exchanges.exchange_date>='".$from_date."' ";
      $transfer_filtering_data .= " AND transfers.transfer_date>='".$from_date."' ";

      $excel_data .= "&from_date=".$from_date;
    }
    if(isset($_GET['to_date']) AND !empty($_GET['to_date'])){
      $to_date = $_GET['to_date'];
      $income_filtering_data .= " AND incomes.income_date<='".$to_date."' ";
      $expense_filtering_data .= " AND expenses.expense_date<='".$to_date."' ";
      $exchange_filtering_data .= " AND exchanges.exchange_date<='".$to_date."' ";
      $transfer_filtering_data .= " AND transfers.transfer_date<='".$to_date."' ";
      $closing_income_filtering_data .= " AND incomes.income_date<='".$to_date."' ";
      $closing_expense_filtering_data .= " AND expenses.expense_date<='".$to_date."' ";
      $closing_exchange_filtering_data .= " AND exchanges.exchange_date<='".$to_date."' ";
      $closing_transfer_filtering_data .= " AND transfers.transfer_date<='".$to_date."' ";

      $excel_data .= "&to_date=".$to_date;
    }
    if(isset($_GET['customer_name']) AND !empty($_GET['customer_name'])){
      $customer_name = $_GET['customer_name'];
      $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
      $expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
      $closing_income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
      $closing_expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
      $closing_exchange_filtering_data .= " AND 0 ";
      $closing_transfer_filtering_data .= " AND 0 ";

      $excel_data .= "&customer_name=".$customer_name;
    }

    if(isset($_GET['customer_id']) AND !empty($_GET['customer_id'])){
      $customer_id = $_GET['customer_id'];
      $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
      $expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
      $closing_income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
      $closing_expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
      $closing_exchange_filtering_data .= " AND 0 ";
      $closing_transfer_filtering_data .= " AND 0 ";

      $excel_data .= "&customer_id=".$customer_id;
    }
    if(isset($_GET['description']) AND !empty($_GET['description'])){
      $description = $_GET['description'];
      $income_filtering_data .= " AND incomes.description LIKE '%".$description."%' ";
      $expense_filtering_data .= " AND expenses.description LIKE '%".$description."%' ";
      $exchange_filtering_data .= " AND exchanges.description LIKE '%".$description."%' ";
      $transfer_filtering_data .= " AND transfers.description LIKE '%".$description."%' ";
      $closing_income_filtering_data .= " AND incomes.description LIKE '%".$description."%' ";
      $closing_expense_filtering_data .= " AND expenses.description LIKE '%".$description."%' ";
      $closing_exchange_filtering_data .= " AND exchanges.description LIKE '%".$description."%' ";
      $closing_transfer_filtering_data .= " AND transfers.description LIKE '%".$description."%' ";

      $excel_data .= "&description=".$description;
    }
    if(isset($_GET['markup']) AND !empty($_GET['markup'])){
      $markup = $_GET['markup'];
      $income_filtering_data .= "AND incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$markup."' AND deleted = '0')";
      $expense_filtering_data .= "AND expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$markup."' AND deleted = '0')";
      $exchange_filtering_data .= "AND exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$markup."' AND deleted = '0')";
      $transfer_filtering_data .= "AND transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$markup."' AND deleted = '0')";
      $closing_income_filtering_data .= "AND incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$markup."' AND deleted = '0')";
      $closing_expense_filtering_data .= "AND expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$markup."' AND deleted = '0')";
      $closing_exchange_filtering_data .= "AND exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$markup."' AND deleted = '0')";
      $closing_transfer_filtering_data .= "AND transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$markup."' AND deleted = '0')";

      $excel_data .= "&markup=".$markup;
    }
    if(isset($_GET['unmark']) AND !empty($_GET['unmark'])){
      $unmark = $_GET['unmark'];
      $income_filtering_data .= " AND ( incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."' AND deleted = '1') OR incomes.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."')) ";
      $expense_filtering_data .= " AND ( expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."' AND deleted = '1') OR expenses.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."')) ";
      $transfer_filtering_data .= " AND ( transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."' AND deleted = '1') OR transfers.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."')) ";
      $closing_income_filtering_data .= " AND ( incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."' AND deleted = '1') OR incomes.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."')) ";
      $closing_expense_filtering_data .= " AND ( expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."' AND deleted = '1') OR expenses.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."')) ";
      $closing_transfer_filtering_data .= " AND ( transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."' AND deleted = '1') OR transfers.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."')) ";

      $excel_data .= "&unmark=".$unmark;
    }

    if(isset($_GET['currency']) AND !empty($_GET['currency'])){
      $currency = $_GET['currency'];
      $income_filtering_data .= " AND incomes.currency='".$currency."' ";
      $expense_filtering_data .= " AND expenses.currency='".$currency."' ";
      $exchange_filtering_data .= " AND (exchanges.from_currency='".$currency."' OR exchanges.to_currency='".$currency."') ";
      $transfer_filtering_data .= " AND transfers.currency='".$currency."' ";
      $closing_income_filtering_data .= " AND incomes.currency='".$currency."' ";
      $closing_expense_filtering_data .= " AND expenses.currency='".$currency."' ";
      $closing_exchange_filtering_data .= " AND (exchanges.from_currency='".$currency."' OR exchanges.to_currency='".$currency."') ";
      $closing_transfer_filtering_data .= " AND transfers.currency='".$currency."' ";

      $excel_data .= "&currency=".$currency;
    }

    if(isset($_GET['transaction_components']) AND !empty($_GET['transaction_components'])){
      $transaction_components = $_GET['transaction_components'];
      $excel_data .= "&transaction_components=".$transaction_components;

      $transaction_components = explode(",", $transaction_components);
      $income_sub_categories_id_array = '';
      $expense_sub_categories_id_array = '';
      $exchange_sub_categories_id_counter = 0;
      $transfer_sub_categories_id_counter = 0;

      
      foreach($transaction_components as $transaction_component){
        $loaded_transaction_components .= '"'.$transaction_component.'",';
        $transaction_component_type = explode("/", $transaction_component)[1];

        

        switch ($transaction_component_type) {
          case 'income':{
            if(count(explode("/", $transaction_component))>3){
              $income_sub_categories_id_array .= "'".explode("/", $transaction_component)[3]."',";
            }
          }break;

          case 'expense':{
            if(count(explode("/", $transaction_component))>3){
              $expense_sub_categories_id_array .= "'".explode("/", $transaction_component)[3]."',";
            }
          }break;

          case 'exchange':{
            $exchange_sub_categories_id_counter ++;
          }break;

          case 'transfer':{
            $transfer_sub_categories_id_counter ++;
          }break;
          
          default:{

          }break;
        }
          
        
        
      }
      $income_sub_categories_id_array = rtrim($income_sub_categories_id_array, ",");
      $expense_sub_categories_id_array = rtrim($expense_sub_categories_id_array, ",");


      $loaded_transaction_components = rtrim($loaded_transaction_components, ",");



      if($income_sub_categories_id_array!=""){
        $income_filtering_data .= " AND sub_categories_id IN (".$income_sub_categories_id_array.") ";
        $closing_income_filtering_data .= " AND sub_categories_id IN (".$income_sub_categories_id_array.") ";

      }else{
        $income_filtering_data .= " AND 0 ";
        $closing_income_filtering_data .= " AND 0 ";
      }

      if($expense_sub_categories_id_array!=""){
        $expense_filtering_data .= " AND sub_categories_id IN (".$expense_sub_categories_id_array.") ";
        $closing_expense_filtering_data .= " AND sub_categories_id IN (".$expense_sub_categories_id_array.") ";
      }else{
        $expense_filtering_data .= " AND 0 ";
        $closing_expense_filtering_data .= " AND 0 ";
      }


      if($exchange_sub_categories_id_counter>0){
        $exchange_filtering_data .= "";
        $closing_exchange_filtering_data .= "";
      }else{
        $exchange_filtering_data .= " AND 0 ";
        $closing_exchange_filtering_data .= " AND 0 ";
      }

      if($transfer_sub_categories_id_counter>0){
        $transfer_filtering_data .= "";
        $closing_transfer_filtering_data .= "";
      }else{
        $transfer_filtering_data .= " AND 0 ";
        $closing_transfer_filtering_data .= " AND 0 ";
      }

    }

    if(isset($_GET['transaction_type']) AND !empty($_GET['transaction_type'])){
      $transaction_type = $_GET['transaction_type'];
      if($transaction_type=="Income"){
        $income_filtering_data .= "";
        $expense_filtering_data .= " AND 0 ";
        $exchange_filtering_data .= " AND 0 ";
        $transfer_filtering_data .= " AND 0 ";
        $closing_income_filtering_data .= "";
        $closing_expense_filtering_data .= " AND 0 ";
        $closing_exchange_filtering_data .= " AND 0 ";
        $closing_transfer_filtering_data .= " AND 0 ";
      }else if($transaction_type=="Expense"){
        $income_filtering_data .= " AND 0 ";
        $expense_filtering_data .= "";
        $exchange_filtering_data .= " AND 0 ";
        $transfer_filtering_data .= " AND 0 ";
        $closing_income_filtering_data .= " AND 0 ";
        $closing_expense_filtering_data .= "";
        $closing_exchange_filtering_data .= " AND 0 ";
        $closing_transfer_filtering_data .= " AND 0 ";
      }else if($transaction_type=="Exchange"){
        $income_filtering_data .= " AND 0 ";
        $expense_filtering_data .= " AND 0 ";
        $exchange_filtering_data .= "";
        $transfer_filtering_data .= " AND 0 ";
        $closing_income_filtering_data .= " AND 0 ";
        $closing_expense_filtering_data .= " AND 0 ";
        $closing_exchange_filtering_data .= "";
        $closing_transfer_filtering_data .= " AND 0 ";
      }else if($transaction_type=="Transfer"){
        $income_filtering_data .= " AND 0 ";
        $expense_filtering_data .= " AND 0 ";
        $exchange_filtering_data .= " AND 0 ";
        $transfer_filtering_data .= "";
        $closing_income_filtering_data .= " AND 0 ";
        $closing_expense_filtering_data .= " AND 0 ";
        $closing_exchange_filtering_data .= " AND 0 ";
        $closing_transfer_filtering_data .= "";
      }

      $excel_data .= "&transaction_type=".$transaction_type;
    }

    if(isset($_GET['categories_id']) AND !empty($_GET['categories_id'])){

      $categories_id = $_GET['categories_id'];

      $sub_categories = '';
      $sub_category_sq = $db->prepare(
        "SELECT id 
        FROM `sub_categories` 
        WHERE categories_id=:categories_id 
        AND deleted='0'"
      );
      $sub_category_sqx = $sub_category_sq->execute([
        'categories_id'=>$categories_id
      ]);
      if($sub_category_sq->rowCount()>0){
        while($sub_category_row = $sub_category_sq->fetch()){
          $sub_categories .= '\''.$sub_category_row['id'].'\',';
        }
        $sub_categories = rtrim($sub_categories, ',');
      }
      
      $income_filtering_data .= " AND incomes.sub_categories_id IN (".$sub_categories.") ";
      $expense_filtering_data .= " AND expenses.sub_categories_id IN (".$sub_categories.") ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
      $closing_income_filtering_data .= " AND incomes.sub_categories_id IN (".$sub_categories.") ";
      $closing_expense_filtering_data .= " AND expenses.sub_categories_id IN (".$sub_categories.") ";
      $closing_exchange_filtering_data .= " AND 0 ";
      $closing_transfer_filtering_data .= " AND 0 ";

      $excel_data .= "&categories_id=".$categories_id;
    }

    if(isset($_GET['sub_categories_id']) AND !empty($_GET['sub_categories_id'])){
      $sub_categories_id = $_GET['sub_categories_id'];
      $income_filtering_data .= " AND incomes.sub_categories_id='".$sub_categories_id."' ";
      $expense_filtering_data .= " AND expenses.sub_categories_id='".$sub_categories_id."' ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
      $closing_income_filtering_data .= " AND incomes.sub_categories_id='".$sub_categories_id."' ";
      $closing_expense_filtering_data .= " AND expenses.sub_categories_id='".$sub_categories_id."' ";
      $closing_exchange_filtering_data .= " AND 0 ";
      $closing_transfer_filtering_data .= " AND 0 ";

      $excel_data .= "&sub_categories_id=".$sub_categories_id;
    }

    if(isset($_GET['users_id']) AND !empty($_GET['users_id'])){
      $users_id = $_GET['users_id'];
      $users_id_items = '';
      if(count($users_id)>0){
        foreach ($users_id as $users_id_item) {
          $users_id_items .= "'".$users_id_item."',";
        }
        $users_id_items = rtrim($users_id_items, ',');

        $income_filtering_data .= " AND incomes.users_id IN (".$users_id_items.") ";
        $expense_filtering_data .= " AND expenses.users_id IN (".$users_id_items.") ";
        $exchange_filtering_data .= " AND exchanges.users_id IN (".$users_id_items.") ";
        $transfer_filtering_data .= " AND (transfers.users_id IN (".$users_id_items.") OR transfers.approved_by IN (".$users_id_items.")) ";
        $closing_income_filtering_data .= " AND incomes.users_id IN (".$users_id_items.") ";
        $closing_expense_filtering_data .= " AND expenses.users_id IN (".$users_id_items.") ";
        $closing_exchange_filtering_data .= " AND exchanges.users_id IN (".$users_id_items.") ";
        $closing_transfer_filtering_data .= " AND (transfers.users_id IN (".$users_id_items.") OR transfers.approved_by IN (".$users_id_items.")) ";

        $excel_data .= "&users_id=".http_build_query($users_id);
      }

    }

    if(isset($_GET['amount']) AND !empty($_GET['amount'])){
      $amount = $_GET['amount'];
      $income_filtering_data .= " AND incomes.income_amount='".$amount."' ";
      $expense_filtering_data .= " AND expenses.expense_amount='".$amount."' ";
      $exchange_filtering_data .= " AND (exchanges.from_amount='".$amount."' OR exchanges.to_amount='".$amount."') ";
      $transfer_filtering_data .= " AND transfers.transfer_amount='".$amount."' ";
      $closing_income_filtering_data .= " AND incomes.income_amount='".$amount."' ";
      $closing_expense_filtering_data .= " AND expenses.expense_amount='".$amount."' ";
      $closing_exchange_filtering_data .= " AND (exchanges.from_amount='".$amount."' OR exchanges.to_amount='".$amount."') ";
      $closing_transfer_filtering_data .= " AND transfers.transfer_amount='".$amount."' ";

      $excel_data .= "&amount=".$amount;
    }

    if(isset($_GET['sib_number']) AND !empty($_GET['sib_number'])){
      $sib_number = $_GET['sib_number'];
      $income_filtering_data .= " AND incomes.sib_number='$sib_number' ";
      $expense_filtering_data .= " AND 0 ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
      $closing_income_filtering_data .= " AND incomes.sib_number='$sib_number' ";
      $closing_expense_filtering_data .= " AND 0 ";
      $closing_exchange_filtering_data .= " AND 0 ";
      $closing_transfer_filtering_data .= " AND 0 ";

      $excel_data .= "&sib_number=".$sib_number;
    }

    if(isset($_GET['check_number']) AND !empty($_GET['check_number'])){
      $check_number = $_GET['check_number'];
      $income_filtering_data .= " AND incomes.check_number='$check_number' ";
      $expense_filtering_data .= " AND expenses.check_number='$check_number' ";
      $exchange_filtering_data .= " AND 0 ";
      $transfer_filtering_data .= " AND 0 ";
      $closing_income_filtering_data .= " AND incomes.check_number='$check_number' ";
      $closing_expense_filtering_data .= " AND expenses.check_number='$check_number' ";
      $closing_exchange_filtering_data .= " AND 0 ";
      $closing_transfer_filtering_data .= " AND 0 ";

      $excel_data .= "&check_number=".$check_number;
    }

    if(!empty($branch)){
      $transfer_out_scope = "from_branch='".$branch."'";
      $transfer_in_scope = "to_branch='".$branch."'";
    }else if(!empty($province) AND $province!="0"){
      $transfer_out_scope = "from_province='".$province."'";
      $transfer_in_scope = "to_province='".$province."'";
    }else{
      $transfer_out_scope = "1";
      $transfer_in_scope = "0";
    }

    
   
    set_pagination();

    $transaction_sq = $db->query( 
    "SELECT 
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
    '' AS transaction_to_branch
    FROM `incomes`
    WHERE incomes.deleted='0' 
    AND incomes.province IN ($accessed_provinces) 
    AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
    $income_filtering_data
    UNION
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
    '' AS transaction_to_branch
    FROM `expenses`
    WHERE expenses.deleted='0' 
    AND expenses.province IN ($accessed_provinces) 
    AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
    $expense_filtering_data
    UNION
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
    '' AS transaction_to_branch
    FROM `exchanges`
    WHERE exchanges.deleted='0' 
    AND exchanges.province IN ($accessed_provinces) 
    $exchange_filtering_data
    $accessed_sub_categories_exchange
    UNION
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
    transfers.to_branch AS transaction_to_branch
    FROM `transfers`
    WHERE transfers.deleted='0' 
    AND transfers.is_approved='1'
    AND (from_province IN ($accessed_provinces) OR to_province IN ($accessed_provinces))
    $transfer_filtering_data
    $accessed_sub_categories_transfer
    ORDER BY transaction_date ASC
    limit $holu_to OFFSET $holu_from"
    );

    

    $Pagenation = $db->query(
      "SELECT 
      count(transaction_id) as record 
      FROM (
        SELECT 
          incomes.id AS transaction_id
        FROM `incomes`
        WHERE incomes.deleted='0' 
        AND incomes.province IN ($accessed_provinces) 
        AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
        $income_filtering_data
        UNION
        SELECT 
          expenses.id AS transaction_id
        FROM `expenses`
        WHERE expenses.deleted='0' 
        AND expenses.province IN ($accessed_provinces) 
        AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
        $expense_filtering_data
        UNION
        SELECT 
          exchanges.id AS transaction_id
        FROM `exchanges`
        WHERE exchanges.deleted='0' 
        AND exchanges.province IN ($accessed_provinces) 
        $exchange_filtering_data
        $accessed_sub_categories_exchange
        UNION
        SELECT 
          transfers.id AS transaction_id
        FROM `transfers`
        WHERE transfers.deleted='0' 
        AND transfers.is_approved='1'
        AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
        $transfer_filtering_data
        $accessed_sub_categories_transfer
      ) AS `all_transactions`"
    );
    extract($Pagenation->fetch());

    $total_income_sq = $db->query(
      "SELECT 
        SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) AS total_income_afn,
        SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) AS total_income_usd,
        SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) AS total_income_irt
      FROM `incomes`
      WHERE incomes.deleted='0' 
      AND incomes.province IN ($accessed_provinces) 
      AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
      $income_filtering_data"
    );
    $total_income_row = $total_income_sq->fetch();

    $total_expense_sq = $db->query(
      "SELECT 
        SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) AS total_expense_afn,
        SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) AS total_expense_usd,
        SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) AS total_expense_irt
      FROM `expenses`
      WHERE expenses.deleted='0' 
      AND expenses.province IN ($accessed_provinces) 
      AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
      $expense_filtering_data"
    );
    $total_expense_row = $total_expense_sq->fetch();

    $total_exchange_sq = $db->query(
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
      AND exchanges.province IN ($accessed_provinces) 
      $exchange_filtering_data
      $accessed_sub_categories_exchange"
    );
    $total_exchange_row = $total_exchange_sq->fetch();

    $total_transfer_sq = $db->query(
      "SELECT 
        SUM(CASE WHEN currency='AFN' AND $transfer_out_scope THEN transfer_amount ELSE 0 END) AS total_transfer_out_afn,
        SUM(CASE WHEN currency='AFN' AND $transfer_in_scope THEN transfer_amount ELSE 0 END) AS total_transfer_in_afn,
        SUM(CASE WHEN currency='USD' AND $transfer_out_scope THEN transfer_amount ELSE 0 END) AS total_transfer_out_usd,
        SUM(CASE WHEN currency='USD' AND $transfer_in_scope THEN transfer_amount ELSE 0 END) AS total_transfer_in_usd,
        SUM(CASE WHEN currency='IRT' AND $transfer_out_scope THEN transfer_amount ELSE 0 END) AS total_transfer_out_irt,
        SUM(CASE WHEN currency='IRT' AND $transfer_in_scope THEN transfer_amount ELSE 0 END) AS total_transfer_in_irt
      FROM `transfers`
      WHERE transfers.deleted='0' 
      AND transfers.is_approved='1'
      AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
      $transfer_filtering_data
      $accessed_sub_categories_transfer"
    );
    $total_transfer_row = $total_transfer_sq->fetch();

    $total_afn = $total_income_row['total_income_afn']
    -$total_expense_row['total_expense_afn']
    +$total_exchange_row['total_to_afn']
    +$total_exchange_row['total_to_afn2']
    -$total_exchange_row['total_from_afn']
    -$total_exchange_row['total_from_afn2']
    -$total_transfer_row['total_transfer_out_afn']
    +$total_transfer_row['total_transfer_in_afn'];

    $total_usd = $total_income_row['total_income_usd']
    -$total_expense_row['total_expense_usd']
    +$total_exchange_row['total_to_usd']
    -$total_exchange_row['total_from_usd']
    -$total_transfer_row['total_transfer_out_usd']
    +$total_transfer_row['total_transfer_in_usd'];

    $total_irt = $total_income_row['total_income_irt']
    -$total_expense_row['total_expense_irt']
    +$total_exchange_row['total_to_irt']
    -$total_exchange_row['total_from_irt']
    -$total_transfer_row['total_transfer_out_irt']
    +$total_transfer_row['total_transfer_in_irt'];

    $closing_income_sq = $db->query(
    "SELECT 
    SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) AS closing_income_afn,
    SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) AS closing_income_usd,
    SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) AS closing_income_irt
    FROM `incomes`
    WHERE incomes.deleted='0' 
    AND incomes.province IN ($accessed_provinces) 
    AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
    $closing_income_filtering_data"
    );
    $closing_income_row = $closing_income_sq->fetch();

    $closing_expense_sq = $db->query(
    "SELECT 
    SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) AS closing_expense_afn,
    SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) AS closing_expense_usd,
    SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) AS closing_expense_irt
    FROM `expenses`
    WHERE expenses.deleted='0' 
    AND expenses.province IN ($accessed_provinces) 
    AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
    $closing_expense_filtering_data"
    );
    $closing_expense_row = $closing_expense_sq->fetch();

    $closing_exchange_sq = $db->query( 
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
    AND exchanges.province IN ($accessed_provinces) 
    $closing_exchange_filtering_data
    $accessed_sub_categories_exchange"
    );
    $closing_exchange_row = $closing_exchange_sq->fetch();

    $closing_transfer_sq = $db->query(
      "SELECT 
        SUM(CASE WHEN currency='AFN' AND $transfer_out_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_out_afn,
        SUM(CASE WHEN currency='AFN' AND $transfer_in_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_in_afn,
        SUM(CASE WHEN currency='USD' AND $transfer_out_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_out_usd,
        SUM(CASE WHEN currency='USD' AND $transfer_in_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_in_usd,
        SUM(CASE WHEN currency='IRT' AND $transfer_out_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_out_irt,
        SUM(CASE WHEN currency='IRT' AND $transfer_in_scope THEN transfer_amount ELSE 0 END) AS closing_transfer_in_irt
      FROM `transfers`
      WHERE transfers.deleted='0' 
      AND transfers.is_approved='1'
      AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
      $closing_transfer_filtering_data
      $accessed_sub_categories_transfer"
    );
    $closing_transfer_row = $closing_transfer_sq->fetch();

    $closing_afn = $closing_income_row['closing_income_afn']
    -$closing_expense_row['closing_expense_afn']
    +$closing_exchange_row['closing_to_afn']
    +$closing_exchange_row['closing_to_afn2']
    -$closing_exchange_row['closing_from_afn']
    -$closing_exchange_row['closing_from_afn2']
    -$closing_transfer_row['closing_transfer_out_afn']
    +$closing_transfer_row['closing_transfer_in_afn'];

    $closing_usd = $closing_income_row['closing_income_usd']
    -$closing_expense_row['closing_expense_usd']
    +$closing_exchange_row['closing_to_usd']
    -$closing_exchange_row['closing_from_usd']
    -$closing_transfer_row['closing_transfer_out_usd']
    +$closing_transfer_row['closing_transfer_in_usd'];

    $closing_irt = $closing_income_row['closing_income_irt']
    -$closing_expense_row['closing_expense_irt']
    +$closing_exchange_row['closing_to_irt']
    -$closing_exchange_row['closing_from_irt']
    -$closing_transfer_row['closing_transfer_out_irt']
    +$closing_transfer_row['closing_transfer_in_irt'];

  }
  
  
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
                <form class="form-horizontal" id="form_report_transaction" role="form" action="report_transaction.php" method="GET" enctype="multipart/form-data">

                  <input type="hidden" name="flag_request" id="flag_request" value="operation"/>

                  <input type="hidden" name="flag_operation" id="flag_operation" value="report_transaction"/>

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
                    <label class="col-sm-3 col-form-label" for="from_date">Date</label>
                    <div class="col-sm-3">
                      <input type="text" id="from_date" name="from_date" class="form-control date_picker" placeholder="From" value="<?php echo $from_date; ?>" >
                    </div>
                    <div class="col-sm-3">
                      <input type="text" id="to_date" name="to_date" class="form-control date_picker" placeholder="To" value="<?php echo $to_date; ?>" >
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
                    <label class="col-sm-3 col-form-label" for="description">Description</label>
                    <div class="col-sm-6">
                      <input type="text" id="description" name="description" class="form-control" placeholder="Type here..." value="<?php echo $description; ?>">
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
                    <label class="col-sm-3 col-form-label" for="currency">Currency</label>
                    <div class="col-sm-6">
                      <select id="currency" name="currency" class="form-control">
                        <option selected value="">Select an option</option>
                        <?php echo get_currency_option($currency); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="transaction_components">Transaction Components</label>
                    <input type="hidden" id="transaction_components" name="transaction_components" value="">
                    <div class="col-sm-6">
                      <div style="border: 1px solid #ced4da !important; height: 250px !important; overflow-y: scroll !important;" id="transaction_components_container">
                        
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="users_id">Added By</label>
                    <div class="col-sm-6">
                      <select id="users_id" name="users_id[]" class="form-control select2" multiple>
                        <?php echo get_user_option($users_id); ?>
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
                    <label class="col-sm-3 col-form-label" for="sib_number">SIB Number</label>
                    <div class="col-sm-6">
                      <input type="text" id="sib_number" name="sib_number" class="form-control" placeholder="Type here..." value="<?php echo $sib_number; ?>">
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

          <?php
          if(isset($_GET['flag_operation']) AND $_GET['flag_operation']=="report_transaction"){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">
                  <i class="fa fa-list"></i>
                  Report of Transactions
                </h4>

                <?php
                if(check_access("system_accessibility/report/report_transaction/export_excel/")){
                ?>
                <a id="export_excel_btn" href="controller_excel.php?excel_type=transaction_report<?php echo $excel_data; ?>"><button type="button" class="btn waves-effect waves-light adder_button"><i class="far fa-file-excel"></i> Export Excel</button></a>
                <?php
                }
                ?>

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
                        <th>Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($transaction_sq->rowCount()>0){
                        
                        while($transaction_row = $transaction_sq->fetch()){

                          $transaction_type = $transaction_row['transaction_type'];
                          $transaction_currency = $transaction_row['transaction_currency'];

                          $check_number = $transaction_row['transaction_check_number'];
                          $sib_number = $transaction_row['transaction_sib_number'];
                          $has_customer_reference = has_customer_reference_for_sib($transaction_row['transaction_additional_informations'] ?? '');

                          $sib_number_container = "";
                          $check_number_container = "";
                          $additional_informations = '';

                          $operations = "";
                          $edit_transaction = "";

                          switch ($transaction_type) {
                            case 'Income':{

                              if($check_number!=""){
                                $check_number_container = htmlspecialchars($check_number, ENT_QUOTES, 'UTF-8');
                              }
                              
                              if($has_customer_reference){
                                $sib_number_container = '
                                <p>'.$sib_number.'</p>
                                ';

                                if(check_access("system_accessibility/report/report_transaction/edit_sib_number/")){
                                  $sib_number_container .= '
                                    <span onclick="edit_sib_number(\''.$transaction_row['transaction_id'].'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
                                  ';
                                }
                              }
                              $operation_destination = 'controller_income.php';

                              $edit_transaction = 'edit_income_form';

                              
                              
                            }break;

                            case 'Expense':{

                              if($check_number!=""){
                                $check_number_container = htmlspecialchars($check_number, ENT_QUOTES, 'UTF-8');
                              }
                              $operation_destination = 'controller_expense.php';

                              $edit_transaction = 'edit_expense_form';

                              
                              
                            }break;

                            case 'Exchange':{
                              
                              $operation_destination = 'controller_exchange.php';

                              $edit_transaction = 'edit_exchange_form';
                            }break;

                            case 'Transfer':{
                              
                              $operation_destination = 'controller_transfer.php';

                              $edit_transaction = 'edit_transfer_form';
                            }break;
                            
                            default:{
                              $check_number = '';
                              $sib_number = '';
                              $operation_destination = '';
                            }break;
                          }



                          if(check_access("system_accessibility/report/report_transaction/edit_transaction/")){
                            $operations .= '
                            <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \''.$operation_destination.'\', \''.$edit_transaction.'\', \'general_lg\', \''.holu_encode($transaction_row['transaction_id']).'\');"><i class="fas fa-edit"></i> Edit Transaction</a>
                            ';
                          }

                          if($transaction_row['transaction_sub_categories_id']!=""){
                            $sub_category = get_col('sub_categories', 'sub_category_name', 'id', $transaction_row['transaction_sub_categories_id']);
                          }

                          $transaction_description = $transaction_row['transaction_description'];
                          if($transaction_type=='Transfer' && $province!="0" && !empty($branch)){
                            if(
                              $province==$transaction_row['transaction_to_province'] &&
                              $branch==$transaction_row['transaction_to_branch'] &&
                              $transaction_row['transaction_approve_description']!=""
                            ){
                              $transaction_description = $transaction_row['transaction_approve_description'];
                            }elseif(
                              $province==$transaction_row['transaction_from_province'] &&
                              $branch==$transaction_row['transaction_from_branch']
                            ){
                              $transaction_description = $transaction_row['transaction_description'];
                            }
                          }elseif($transaction_type=='Transfer' && $province!="0"){
                            if($province==$transaction_row['transaction_to_province'] && $transaction_row['transaction_approve_description']!=""){
                              $transaction_description = $transaction_row['transaction_approve_description'];
                            }elseif($province==$transaction_row['transaction_from_province']){
                              $transaction_description = $transaction_row['transaction_description'];
                            }
                          }elseif($transaction_type=='Transfer' && !empty($branch)){
                            if($branch==$transaction_row['transaction_to_branch'] && $transaction_row['transaction_approve_description']!=""){
                              $transaction_description = $transaction_row['transaction_approve_description'];
                            }elseif($branch==$transaction_row['transaction_from_branch']){
                              $transaction_description = $transaction_row['transaction_description'];
                            }
                          }

                          if(check_access("system_accessibility/report/report_transaction/print_receipt/") AND $transaction_type=="Income"){
                            $operations .= '<a class="dropdown-item" href="print_receipt.php?incomes_id='.holu_encode($transaction_row['transaction_id']).'" target=" _ "><i class="fas fa-print"></i> Print Receipt</a>';
                          }

                          if(check_access("system_accessibility/report/report_transaction/print_voucher/") AND $transaction_type=="Expense"){
                            $operations .= '<a class="dropdown-item" href="print_voucher.php?expenses_id='.holu_encode($transaction_row['transaction_id']).'" target=" _ "><i class="fas fa-print"></i> Print Voucher</a>';
                          }

                          if(check_access("system_accessibility/report/report_transaction/view_attachment/") AND ($transaction_type=="Income" OR $transaction_type=="Expense")){
                            $operations .= '
                            <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \''.$operation_destination.'\', \'view_attachment\', \'general_lg\', \''.holu_encode($transaction_row['transaction_id']).'\');"><i class="far fa-file-image"></i> View Attachment</a>
                            ';
                          }

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
                            <td class="text-center" id="check_number_container<?php echo $transaction_row['transaction_type'].$transaction_row['transaction_id']; ?>"><?php echo $check_number_container; ?></td>
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
                            <td class="text-center" id="sib_number_container<?php echo $transaction_row['transaction_type'].$transaction_row['transaction_id']; ?>">
                              <?php echo $sib_number_container; ?>
                            </td>
                            <td class="text-center">
                              <?php echo get_col('users', 'username', 'id', $transaction_row['transaction_users_id']); ?>
                            </td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  
                                  <?php echo $operations; ?>

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
                  <br/>

                  <div class="alert alert-success text-center">
                    Income: <span class="badge badge-success">
                      <?php echo number_format($total_income_row['total_income_afn'] ?? 0, 2); ?> AFN</span>
                      - 
                      <span class="badge badge-success">
                      <?php echo number_format($total_income_row['total_income_usd'] ?? 0, 2); ?> USD</span> - <span class="badge badge-success">
                      <?php echo number_format($total_income_row['total_income_irt'] ?? 0, 2); ?> IRT</span>
                  </div>

                  <div class="alert alert-danger text-center">Expense: <span class="badge badge-danger"><?php echo number_format($total_expense_row['total_expense_afn'] ?? 0, 2); ?> AFN</span> - <span class="badge badge-danger"><?php echo number_format($total_expense_row['total_expense_usd'] ?? 0, 2); ?> USD</span> - <span class="badge badge-danger"><?php echo number_format($total_expense_row['total_expense_irt'] ?? 0, 2); ?> IRT</span>
                  </div>

                  <div class="alert alert-warning text-center">Exchange: <span class="badge badge-warning"><?php echo number_format($total_exchange_row['total_from_afn'] ?? 0, 2); ?> AFN to <?php echo number_format($total_exchange_row['total_to_usd'] ?? 0, 2); ?> USD</span> - <span class="badge badge-warning"><?php echo number_format($total_exchange_row['total_from_usd'] ?? 0, 2); ?> USD to <?php echo number_format($total_exchange_row['total_to_afn'] ?? 0, 2); ?> AFN</span> - <span class="badge badge-warning"><?php echo number_format($total_exchange_row['total_from_afn2'] ?? 0, 2); ?> AFN to <?php echo number_format($total_exchange_row['total_to_irt'] ?? 0, 2); ?> IRT</span> - <span class="badge badge-warning"><?php echo number_format($total_exchange_row['total_from_irt'] ?? 0, 2); ?> IRT to <?php echo number_format($total_exchange_row['total_to_afn2'] ?? 0, 2); ?> AFN</span>
                  </div>

                  <div class="alert alert-secondary text-center">
                    Transfer:
                    <span class="badge badge-secondary"><?php echo number_format(($total_transfer_row['total_transfer_in_afn'] ?? 0) - ($total_transfer_row['total_transfer_out_afn'] ?? 0), 2); ?> AFN</span>
                    -
                    <span class="badge badge-secondary"><?php echo number_format(($total_transfer_row['total_transfer_in_usd'] ?? 0) - ($total_transfer_row['total_transfer_out_usd'] ?? 0), 2); ?> USD</span>
                    -
                    <span class="badge badge-secondary"><?php echo number_format(($total_transfer_row['total_transfer_in_irt'] ?? 0) - ($total_transfer_row['total_transfer_out_irt'] ?? 0), 2); ?> IRT</span>
                  </div>

                  <div class="alert alert-info text-center">Total: <span class="badge badge-info">
                      <?php echo number_format($total_afn, 2); ?> AFN</span> - 
                      <span class="badge badge-info">
                        <?php echo number_format($total_usd, 2); ?> USD
                      </span> 
                      - 
                      <span class="badge badge-info">
                        <?php echo number_format($total_irt, 2); ?> IRT
                      </span>
                  </div>

                  <div class="alert alert-info text-center">Total with Closing: <span class="badge badge-info"><?php echo number_format($closing_afn, 2); ?> AFN</span> - <span class="badge badge-info"><?php echo number_format($closing_usd, 2); ?> USD</span> - <span class="badge badge-info"><?php echo number_format($closing_irt, 2); ?> IRT</span></div>
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
  <script type="text/javascript">

  function edit_sib_number(incomes_id, operation_type){
  
    if(operation_type=="edit"){
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_sib_number',
          operation_type:operation_type,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#sib_number_containerIncome"+incomes_id).html(result);
        }
      });
    }else if(operation_type=="save"){
      var sib_number = $("#sib_number"+incomes_id).val();
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_sib_number',
          operation_type:operation_type,
          sib_number:sib_number,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#sib_number_containerIncome"+incomes_id).html(result);
        }
      });
    }
  }

  function edit_accounting_note(incomes_id, operation_type){
  
    if(operation_type=="edit"){
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_accounting_note',
          operation_type:operation_type,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#accounting_note_containerIncome"+incomes_id).html(result);
        }
      });
    }else if(operation_type=="save"){
      var accounting_note = $("#accounting_note"+incomes_id).val();
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_accounting_note',
          operation_type:operation_type,
          accounting_note:accounting_note,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#accounting_note_containerIncome"+incomes_id).html(result);
        }
      });
    }
  }

  function set_currency(){
    var exchange_type = $("#exchange_type").val();
    switch(exchange_type){
      case "AFN to USD":{
        $("#from_currency").val("AFN");
        $("#to_currency").val("USD");
      }break;

      case "USD to AFN":{
        $("#from_currency").val("USD");
        $("#to_currency").val("AFN");
      }break;

      case "AFN to IRT":{
        $("#from_currency").val("AFN");
        $("#to_currency").val("IRT");
      }break;

      case "IRT to AFN":{
        $("#from_currency").val("IRT");
        $("#to_currency").val("AFN");
      }break;

      default:{
        $("#from_currency").val("");
        $("#to_currency").val("");
      }break;
    }
  }

  var transaction_components = <?php echo print_access_sub_categories(); ?>

    return components
      .filter(function(component){
      })
      .map(function(component){
        if(component.children && component.children.length){
        }
        return component;
      });
  }


  var transaction_components_tree = new Tree('#transaction_components_container', {
    data: [{ id: 'transaction_components', text: 'Transaction Components', children: transaction_components }],
    closeDepth: 2,
    loaded: function () {
      this.values = [<?php echo $loaded_transaction_components; ?>];
    },
    onChange: function () {
      var transaction_components = this.values;
      $('#transaction_components').val(transaction_components);
    }
  });
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
