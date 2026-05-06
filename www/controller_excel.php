<?php

  require '../lib/lib/Excel_Generator_Library/autoload.php';
  include("../lib/_configuration.php");

  ini_set('max_execution_time', 6000);

  use Spatie\SimpleExcel\SimpleExcelWriter;
  use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
  use Box\Spout\Common\Entity\Style\Color;

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

  if(isset($_GET['excel_type']) AND !empty($_GET['excel_type'])){

    $excel_type = holu_escape($_GET['excel_type']);

    switch ($excel_type) {
      case 'transaction_report':{

        $writer = SimpleExcelWriter::streamDownload($excel_type.'.xlsx');
 
        $income_filtering_data = "";
        $expense_filtering_data = "";
        $exchange_filtering_data = "";
        $purchase_filtering_data = "";
        $transfer_filtering_data = "";

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
        $users_id = "0";
        $amount = "0";
        $sib_number = "";
        $check_number = "";

        if(isset($_GET['province']) AND !empty($_GET['province'])){
          $province = $_GET['province'];
          $income_filtering_data .= " AND incomes.province='".$province."' ";
          $expense_filtering_data .= " AND expenses.province='".$province."' ";
          $exchange_filtering_data .= " AND exchanges.province='".$province."' ";
          $purchase_filtering_data .= " AND purchases.province='".$province."' ";
        }
        if(isset($_GET['branch']) AND !empty($_GET['branch'])){
          $branch = $_GET['branch'];
          $income_filtering_data .= " AND incomes.branch='".$branch."' ";
          $expense_filtering_data .= " AND expenses.branch='".$branch."' ";
          $exchange_filtering_data .= " AND exchanges.branch='".$branch."' ";
          $purchase_filtering_data .= " AND 0 ";
          $transfer_filtering_data .= " AND (transfers.from_branch='".$branch."' OR transfers.to_branch='".$branch."') ";
        }
        if(isset($_GET['from_date']) AND !empty($_GET['from_date'])){
          $from_date = $_GET['from_date'];
          $income_filtering_data .= " AND incomes.income_date>='".$from_date."' ";
          $expense_filtering_data .= " AND expenses.expense_date>='".$from_date."' ";
          $exchange_filtering_data .= " AND exchanges.exchange_date>='".$from_date."' ";
          $purchase_filtering_data .= " AND purchases.purchase_date>='".$from_date."' ";
          $transfer_filtering_data .= " AND transfers.transfer_date>='".$from_date."' ";
    
        }
        if(isset($_GET['to_date']) AND !empty($_GET['to_date'])){
          $to_date = $_GET['to_date'];
          $income_filtering_data .= " AND incomes.income_date<='".$to_date."' ";
          $expense_filtering_data .= " AND expenses.expense_date<='".$to_date."' ";
          $exchange_filtering_data .= " AND exchanges.exchange_date<='".$to_date."' ";
          $purchase_filtering_data .= " AND purchases.purchase_date<='".$to_date."' ";
          $transfer_filtering_data .= " AND transfers.transfer_date<='".$to_date."' ";
        }
        if(isset($_GET['customer_name']) AND !empty($_GET['customer_name'])){
          $customer_name = $_GET['customer_name'];
          $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
          $expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
          $exchange_filtering_data .= " AND 0 ";
          $purchase_filtering_data .= " AND 0 ";
          $transfer_filtering_data .= " AND 0 ";
        }

        if(isset($_GET['customer_id']) AND !empty($_GET['customer_id'])){
          $customer_id = $_GET['customer_id'];
          $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer ID' AND value_info='$customer_id' AND deleted='0') ";
          $expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer ID' AND value_info='$customer_id' AND deleted='0') ";
          $exchange_filtering_data .= " AND 0 ";
          $purchase_filtering_data .= " AND 0 ";
          $transfer_filtering_data .= " AND 0 ";
        }
        if(isset($_GET['description']) AND !empty($_GET['description'])){
          $description = $_GET['description'];
          $income_filtering_data .= " AND incomes.description LIKE '%".$description."%' ";
          $expense_filtering_data .= " AND expenses.description LIKE '%".$description."%' ";
          $exchange_filtering_data .= " AND exchanges.description LIKE '%".$description."%' ";
          $purchase_filtering_data .= " AND purchases.description LIKE '%".$description."%' ";
          $transfer_filtering_data .= " AND transfers.description LIKE '%".$description."%' ";
        }
        if(isset($_GET['markup']) AND !empty($_GET['markup'])){
          $markup = $_GET['markup'];
          $income_filtering_data .= "AND incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$markup."' AND deleted = '0')";
          $expense_filtering_data .= "AND expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$markup."' AND deleted = '0')";
          $exchange_filtering_data .= "AND exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$markup."' AND deleted = '0')";
          $purchase_filtering_data .= "AND purchases.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Purchase' AND markup_type = '".$markup."' AND deleted = '0')";
          $transfer_filtering_data .= "AND transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$markup."' AND deleted = '0')";
        }
        if(isset($_GET['unmark']) AND !empty($_GET['unmark'])){
          $unmark = $_GET['unmark'];
          $income_filtering_data .= " AND ( incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."' AND deleted = '1') OR incomes.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."')) ";
          $expense_filtering_data .= " AND ( expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."' AND deleted = '1') OR expenses.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."')) ";
          $exchange_filtering_data .= " AND ( exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$unmark."' AND deleted = '1') OR exchanges.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$unmark."')) ";
          $purchase_filtering_data .= " AND ( purchases.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Purchase' AND markup_type = '".$unmark."' AND deleted = '1') OR purchases.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Purchase' AND markup_type = '".$unmark."')) ";
          $transfer_filtering_data .= " AND ( transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."' AND deleted = '1') OR transfers.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."')) ";    
        }

        if(isset($_GET['currency']) AND !empty($_GET['currency'])){
          $currency = $_GET['currency'];
          $income_filtering_data .= " AND incomes.currency='".$currency."' ";
          $expense_filtering_data .= " AND expenses.currency='".$currency."' ";
          $exchange_filtering_data .= "";
          $purchase_filtering_data .= " AND purchases.currency='".$currency."' ";
          $transfer_filtering_data .= " AND transfers.currency='".$currency."' ";    
        }

        if(isset($_GET['transaction_components']) AND !empty($_GET['transaction_components'])){
          $transaction_components = $_GET['transaction_components'];
    
          $transaction_components = explode(",", $transaction_components);
          $income_sub_categories_id_array = '';
          $expense_sub_categories_id_array = '';
          $exchange_sub_categories_id_counter = 0;
          $purchase_sub_categories_id_array = '';
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

              case 'purchase':{
                if(count(explode("/", $transaction_component))>3){
                  $purchase_sub_categories_id_array .= "'".explode("/", $transaction_component)[3]."',";
                }
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
          $purchase_sub_categories_id_array = rtrim($purchase_sub_categories_id_array, ",");
    
    
          $loaded_transaction_components = rtrim($loaded_transaction_components, ",");
    
    
    
          if($income_sub_categories_id_array!=""){
            $income_filtering_data .= " AND sub_categories_id IN (".$income_sub_categories_id_array.") ";
            // $closing_income_filtering_data .= " AND sub_categories_id IN (".$income_sub_categories_id_array.") ";
    
          }else{
            $income_filtering_data .= " AND 0 ";
            // $closing_income_filtering_data .= " AND 0 ";
          }
    
          if($expense_sub_categories_id_array!=""){
            $expense_filtering_data .= " AND sub_categories_id IN (".$expense_sub_categories_id_array.") ";
            // $closing_expense_filtering_data .= " AND sub_categories_id IN (".$expense_sub_categories_id_array.") ";
          }else{
            $expense_filtering_data .= " AND 0 ";
            // $closing_expense_filtering_data .= " AND 0 ";
          }
    
          if($purchase_sub_categories_id_array!=""){
            $purchase_filtering_data .= " AND sub_categories_id IN (".$purchase_sub_categories_id_array.") ";
            // $closing_purchase_filtering_data .= " AND sub_categories_id IN (".$purchase_sub_categories_id_array.") ";
          }else{
            $purchase_filtering_data .= " AND 0 ";
            // $closing_purchase_filtering_data .= " AND 0 ";
          }
    
          
          
          
          
          
    
          if($exchange_sub_categories_id_counter>0){
            $exchange_filtering_data .= "";
            // $closing_exchange_filtering_data .= "";
          }else{
            $exchange_filtering_data .= " AND 0 ";
            // $closing_exchange_filtering_data .= " AND 0 ";
          }
    
          if($transfer_sub_categories_id_counter>0){
            $transfer_filtering_data .= "";
            // $closing_transfer_filtering_data .= "";
          }else{
            $transfer_filtering_data .= " AND 0 ";
            // $closing_transfer_filtering_data .= " AND 0 ";
          }
        }

        if(isset($_GET['transaction_type']) AND !empty($_GET['transaction_type'])){
          $transaction_type = $_GET['transaction_type'];
          if($transaction_type=="Income"){
            $income_filtering_data .= "";
            $expense_filtering_data .= " AND 0 ";
            $exchange_filtering_data .= " AND 0 ";
            $purchase_filtering_data .= " AND 0 ";
            $transfer_filtering_data .= " AND 0 ";
          }else if($transaction_type=="Expense"){
            $income_filtering_data .= " AND 0 ";
            $expense_filtering_data .= "";
            $exchange_filtering_data .= " AND 0 ";
            $purchase_filtering_data .= " AND 0 ";
            $transfer_filtering_data .= " AND 0 ";
          }else if($transaction_type=="Exchange"){
            $income_filtering_data .= " AND 0 ";
            $expense_filtering_data .= " AND 0 ";
            $exchange_filtering_data .= "";
            $purchase_filtering_data .= " AND 0 ";
            $transfer_filtering_data .= " AND 0 ";
          }else if($transaction_type=="Purchase"){
            $income_filtering_data .= " AND 0 ";
            $expense_filtering_data .= " AND 0 ";
            $exchange_filtering_data .= " AND 0 ";
            $purchase_filtering_data .= "";
            $transfer_filtering_data .= " AND 0 ";
          }else if($transaction_type=="Transfer"){
            $income_filtering_data .= " AND 0 ";
            $expense_filtering_data .= " AND 0 ";
            $exchange_filtering_data .= " AND 0 ";
            $purchase_filtering_data .= " AND 0 ";
            $transfer_filtering_data .= "";
          }
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
          $purchase_filtering_data .= " AND 0 ";
          $transfer_filtering_data .= " AND 0 ";
        }

        if(isset($_GET['sub_categories_id']) AND !empty($_GET['sub_categories_id'])){
          $sub_categories_id = $_GET['sub_categories_id'];
          $income_filtering_data .= " AND incomes.sub_categories_id='".$sub_categories_id."' ";
          $expense_filtering_data .= " AND expenses.sub_categories_id='".$sub_categories_id."' ";
          $exchange_filtering_data .= " AND 0 ";
          $purchase_filtering_data .= " AND 0 ";
          $transfer_filtering_data .= " AND 0 ";
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
            $purchase_filtering_data .= " AND purchases.users_id IN (".$users_id_items.") ";
            $transfer_filtering_data .= " AND (transfers.users_id IN (".$users_id_items.") OR transfers.approved_by IN (".$users_id_items.")) ";
          }

        }

        if(isset($_GET['amount']) AND !empty($_GET['amount'])){
          $amount = $_GET['amount'];
          $income_filtering_data .= " AND incomes.income_amount='".$amount."' ";
          $expense_filtering_data .= " AND expenses.expense_amount='".$amount."' ";
          $exchange_filtering_data .= " AND (exchanges.from_amount='".$amount."' OR exchanges.to_amount='".$amount."') ";
          $purchase_filtering_data .= " AND purchases.purchase_amount='".$amount."' ";
          $transfer_filtering_data .= " AND transfers.transfer_amount='".$amount."' ";    
        }

        if(isset($_GET['sib_number']) AND !empty($_GET['sib_number'])){
          $sib_number = $_GET['sib_number'];
          $income_filtering_data .= " AND incomes.sib_number='$sib_number' ";
          $expense_filtering_data .= " AND 0 ";
          $exchange_filtering_data .= " AND 0 ";
          $purchase_filtering_data .= " AND 0 ";
          $transfer_filtering_data .= " AND 0 ";
        }

        if(isset($_GET['check_number']) AND !empty($_GET['check_number'])){
          $check_number = $_GET['check_number'];
          $income_filtering_data .= " AND incomes.check_number='$check_number' ";
          $expense_filtering_data .= " AND expenses.check_number='$check_number' ";
          $exchange_filtering_data .= " AND 0 ";
          $purchase_filtering_data .= " AND 0 ";
          $transfer_filtering_data .= " AND 0 ";
        }
       

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
        exchanges.tms_markup As transaction_tms_markup,
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
        purchases.id AS transaction_id,
        'Purchase' AS transaction_type,
        purchases.sub_categories_id AS transaction_sub_categories_id,
        purchases.province AS transaction_province,
        '' AS transaction_branch,
        purchases.purchase_date AS transaction_date,
        purchases.purchase_amount AS transaction_amount,
        purchases.currency AS transaction_currency,
        purchases.description AS transaction_description,
        purchases.users_id AS transaction_users_id,
        '' AS transaction_check_number,
        '' AS transaction_sib_number,
        purchases.tms_markup AS transaction_tms_markup,
        purchases.qb_markup AS transaction_qb_markup,
        purchases.sib_markup AS transaction_sib_markup,
        purchases.ad_markup AS transaction_ad_markup,
        '' AS transaction_additional_informations,
        '' AS transaction_approve_description,
        '' AS transaction_from_province,
        '' AS transaction_to_province,
        '' AS transaction_from_branch,
        '' AS transaction_to_branch
        FROM `purchases`
        WHERE purchases.deleted='0' 
        AND purchases.is_approved='1'
        AND purchases.is_included='1'
        AND purchases.province IN ($accessed_provinces) 
        AND purchases.logistic_cashes_id IN ($accessed_logistic_cashes)
        $purchase_filtering_data
        AND purchases.sub_categories_id IN ($accessed_sub_categories_purchase)
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
        transfers.qb_markup AS transaction_qb_markup,
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
        ORDER BY transaction_date ASC"
        );

        
        $style = (new StyleBuilder())
        ->setFontSize(14)
        ->setFontColor(Color::WHITE)
        ->setBackgroundColor(Color::rgb(0, 184, 165))
        ->build();

        $header = [
          '#',
          'Description',
          'Amount',
          'Currency',
          'Type',
          'Sub Category',
          'Date',
          'Province',
          'Branch',
          'Check Number',
          'Additional Information',
          'SIB Number',
        ];

        $writer->addRow($header, $style);

        if($transaction_sq->rowCount()>0){

          $count = 1;
                            
          while($transaction_row = $transaction_sq->fetch()){
            
            

            

            $additional_informations = '';
            if($transaction_row['transaction_type']=='Transfer'){
              $additional_informations = $transaction_row['transaction_approve_description'];
            }else{
              $ai = json_decode($transaction_row['transaction_additional_informations'] ?? '');
              if(!empty($ai)){
                foreach ($ai as $key => $value) {
                  $additional_informations .= 
                    $key.': '.$value;
                }
              }
            }

            $transaction_row['transaction__sub_categories'] = $transaction_row['transaction_sub_categories_id']!="" ? get_col('sub_categories', 'sub_category_name', 'id', $transaction_row['transaction_sub_categories_id']) : '';
            $transaction_sib_number = $transaction_row['transaction_sib_number'];
            $transaction_description = $transaction_row['transaction_description'];
            if($transaction_row['transaction_type']=='Transfer' && $province!="0" && !empty($branch)){
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
            }elseif($transaction_row['transaction_type']=='Transfer' && $province!="0"){
              if($province==$transaction_row['transaction_to_province'] && $transaction_row['transaction_approve_description']!=""){
                $transaction_description = $transaction_row['transaction_approve_description'];
              }elseif($province==$transaction_row['transaction_from_province']){
                $transaction_description = $transaction_row['transaction_description'];
              }
            }elseif($transaction_row['transaction_type']=='Transfer' && !empty($branch)){
              if($branch==$transaction_row['transaction_to_branch'] && $transaction_row['transaction_approve_description']!=""){
                $transaction_description = $transaction_row['transaction_approve_description'];
              }elseif($branch==$transaction_row['transaction_from_branch']){
                $transaction_description = $transaction_row['transaction_description'];
              }
            }
            if(
              $transaction_row['transaction_type'] === 'Income' &&
              !has_customer_reference_for_sib($transaction_row['transaction_additional_informations'] ?? '')
            ){
              $transaction_sib_number = '';
            }

            $writer->addRow([
              $count++,
              $transaction_description,
              $transaction_row['transaction_amount'],
              $transaction_row['transaction_currency'],
              $transaction_row['transaction_type'],
              $transaction_row['transaction__sub_categories'],
              $transaction_row['transaction_date'],
              $transaction_row['transaction_province'],
              $transaction_row['transaction_branch'],
              $transaction_row['transaction_check_number'],
              $additional_informations,
              $transaction_sib_number,
            ]);
          }
        }

        $writer->toBrowser();

      }break;
      

      case 'dashboard_transactions':{

        $writer = SimpleExcelWriter::streamDownload($excel_type.'.xlsx');

        $dashboard_date_range_data = resolve_dashboard_transaction_date_range();
        $dashboard_date_filtering_data = $dashboard_date_range_data['sql_filter'];
        $dashboard_date_range_display = $dashboard_date_range_data['display_date_range'];
        $dashboard_date_range_label = $dashboard_date_range_data['label'];


        $income_access_condition = set_province_branch_portion('incomes.province', 'incomes.branch');
        $expense_access_condition = set_province_branch_portion('expenses.province', 'expenses.branch');
        $exchange_access_condition = set_province_branch_portion('exchanges.province', 'exchanges.branch');
        $transfer_from_access_condition = set_province_branch_portion('transfers.from_province', 'transfers.from_branch');
        $transfer_to_access_condition = set_province_branch_portion('transfers.to_province', 'transfers.to_branch');

        $transaction_sq = $db->query("SELECT * FROM (
          SELECT incomes.id AS transaction_id, 'Income' AS transaction_type, incomes.province AS transaction_province, incomes.branch AS transaction_branch, incomes.income_date AS transaction_date, incomes.income_amount AS transaction_amount, incomes.currency AS transaction_currency, incomes.description AS transaction_description, incomes.users_id AS transaction_users_id, incomes.sub_categories_id AS transaction_sub_categories_id, incomes.check_number AS transaction_check_number
          FROM `incomes`
          WHERE incomes.deleted='0'
          AND $income_access_condition
          AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
          UNION ALL
          SELECT expenses.id AS transaction_id, 'Expense' AS transaction_type, expenses.province AS transaction_province, expenses.branch AS transaction_branch, expenses.expense_date AS transaction_date, expenses.expense_amount AS transaction_amount, expenses.currency AS transaction_currency, expenses.description AS transaction_description, expenses.users_id AS transaction_users_id, expenses.sub_categories_id AS transaction_sub_categories_id, expenses.check_number AS transaction_check_number
          FROM `expenses`
          WHERE expenses.deleted='0'
          AND $expense_access_condition
          AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
          UNION ALL
          SELECT exchanges.id AS transaction_id, 'Exchange' AS transaction_type, exchanges.province AS transaction_province, exchanges.branch AS transaction_branch, exchanges.exchange_date AS transaction_date, CONCAT(exchanges.from_amount, ' to ', exchanges.to_amount) AS transaction_amount, CONCAT(exchanges.from_currency, ' to ', exchanges.to_currency) AS transaction_currency, exchanges.description AS transaction_description, exchanges.users_id AS transaction_users_id, 0 AS transaction_sub_categories_id, '' AS transaction_check_number
          FROM `exchanges`
          WHERE exchanges.deleted='0'
          AND $exchange_access_condition
          $accessed_sub_categories_exchange
          UNION ALL
          SELECT transfers.id AS transaction_id, 'Transfers' AS transaction_type, CONCAT(transfers.from_province, ' to ', transfers.to_province) AS transaction_province, CONCAT(transfers.from_branch, ' to ', transfers.to_branch) AS transaction_branch, transfers.transfer_date AS transaction_date, transfers.transfer_amount AS transaction_amount, transfers.currency AS transaction_currency, transfers.description AS transaction_description, transfers.users_id AS transaction_users_id, 0 AS transaction_sub_categories_id, transfers.check_number AS transaction_check_number
          FROM `transfers`
          WHERE transfers.deleted='0'
          AND ((($transfer_from_access_condition) OR ($transfer_to_access_condition)) OR transfers.users_id='$holu_users_id')
          $accessed_sub_categories_transfer
        ) AS dashboard_transactions
        WHERE 1 $dashboard_date_filtering_data
        ORDER BY transaction_date DESC, transaction_id DESC");

        $style = (new StyleBuilder())
        ->setFontSize(14)
        ->setFontColor(Color::WHITE)
        ->setBackgroundColor(Color::rgb(0, 184, 165))
        ->build();

        $writer->addRow([
          'Report of Transactions',
          $dashboard_date_range_display,
          $dashboard_date_range_label,
        ], $style);
        $writer->addRow([]);

        $writer->addRow([
          '#',
          'Type',
          'Province',
          'Branch',
          'Category',
          'Sub Category',
          'Date',
          'Amount',
          'Currency',
          'Check Number',
          'Description',
          'Created By',
        ], $style);

        if($transaction_sq->rowCount()>0){
          $count = 1;
          while($transaction_row = $transaction_sq->fetch()){
            $transaction_category = '';
            $transaction_sub_category = '';
            if($transaction_row['transaction_sub_categories_id']!=0){
              $transaction_category = get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $transaction_row['transaction_sub_categories_id']));
              $transaction_sub_category = get_col('sub_categories', 'sub_category_name', 'id', $transaction_row['transaction_sub_categories_id']);
            }

            $writer->addRow([
              $count++,
              $transaction_row['transaction_type'],
              $transaction_row['transaction_province'],
              $transaction_row['transaction_branch'],
              $transaction_category,
              $transaction_sub_category,
              $transaction_row['transaction_date'],
              $transaction_row['transaction_amount'],
              $transaction_row['transaction_currency'],
              $transaction_row['transaction_check_number'],
              $transaction_row['transaction_description'],
              get_col('users', 'username', 'id', $transaction_row['transaction_users_id']),
            ]);
          }
        }

        $writer->toBrowser();

      }break;

      default:{

      }break;
    }

    

  }

?>
