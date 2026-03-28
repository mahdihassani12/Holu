<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Transactions"];

  $income_filtering_data = "";
  $expense_filtering_data = "";
  $exchange_filtering_data = "";
  $purchase_filtering_data = "";
  $transfer_filtering_data = "";

  $province = "0";
  $from_date = "";
  $to_date = "";
  $customer_name = "";
  $customer_id = "";
  $description = "";
  $markup = "";
  $unmark = "";
  $currency = "";
  $transaction_type = "";
  $categories_id = "0";
  $sub_categories_id = "0";
  $users_id = [];
  $amount = "0";
  $sib_number = "";
  $check_number = "";

  $excel_data = "";

  if(isset($_GET['province']) AND !empty($_GET['province'])){
    $province = $_GET['province'];
    $income_filtering_data .= " AND incomes.province='".$province."' ";
    $expense_filtering_data .= " AND expenses.province='".$province."' ";
    $exchange_filtering_data .= " AND exchanges.province='".$province."' ";
    $purchase_filtering_data .= " AND purchases.province='".$province."' ";
    $transfer_filtering_data .= " AND (transfers.from_province='".$province."' OR to_province='".$province."') ";

    $excel_data .= "&province=".$province;
  }
  if(isset($_GET['from_date']) AND !empty($_GET['from_date'])){
    $from_date = $_GET['from_date'];
    $income_filtering_data .= " AND incomes.income_date>='".$from_date."' ";
    $expense_filtering_data .= " AND expenses.expense_date>='".$from_date."' ";
    $exchange_filtering_data .= " AND exchanges.exchange_date>='".$from_date."' ";
    $purchase_filtering_data .= " AND purchases.purchase_date>='".$from_date."' ";
    $transfer_filtering_data .= " AND transfers.transfer_date>='".$from_date."' ";

    $excel_data .= "&from_date=".$from_date;
  }
  if(isset($_GET['to_date']) AND !empty($_GET['to_date'])){
    $to_date = $_GET['to_date'];
    $income_filtering_data .= " AND incomes.income_date<='".$to_date."' ";
    $expense_filtering_data .= " AND expenses.expense_date<='".$to_date."' ";
    $exchange_filtering_data .= " AND exchanges.exchange_date<='".$to_date."' ";
    $purchase_filtering_data .= " AND purchases.purchase_date<='".$to_date."' ";
    $transfer_filtering_data .= " AND transfers.transfer_date<='".$to_date."' ";

    $excel_data .= "&to_date=".$to_date;
  }
  if(isset($_GET['customer_name']) AND !empty($_GET['customer_name'])){
    $customer_name = $_GET['customer_name'];
    $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
    $expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer Name' AND value_info LIKE '%$customer_name%' AND deleted='0') ";
    $exchange_filtering_data .= " AND 0 ";
    $purchase_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";

    $excel_data .= "&customer_name=".$customer_name;
  }

  if(isset($_GET['customer_id']) AND !empty($_GET['customer_id'])){
    $customer_id = $_GET['customer_id'];
    $income_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
    $expense_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Expense' AND key_info='Customer ID' AND value_info LIKE '%$customer_id' AND deleted='0') ";
    $exchange_filtering_data .= " AND 0 ";
    $purchase_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";

    $excel_data .= "&customer_id=".$customer_id;
  }
  if(isset($_GET['description']) AND !empty($_GET['description'])){
    $description = $_GET['description'];
    $income_filtering_data .= " AND incomes.description LIKE '%".$description."%' ";
    $expense_filtering_data .= " AND expenses.description LIKE '%".$description."%' ";
    $exchange_filtering_data .= " AND exchanges.description LIKE '%".$description."%' ";
    $purchase_filtering_data .= " AND purchases.description LIKE '%".$description."%' ";
    $transfer_filtering_data .= " AND transfers.description LIKE '%".$description."%' ";

    $excel_data .= "&description=".$description;
  }
  if(isset($_GET['markup']) AND !empty($_GET['markup'])){
    $markup = $_GET['markup'];
    $income_filtering_data .= "AND incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$markup."' AND deleted = '0')";
    $expense_filtering_data .= "AND expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$markup."' AND deleted = '0')";
    $exchange_filtering_data .= "AND exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$markup."' AND deleted = '0')";
    $purchase_filtering_data .= "AND purchases.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Purchase' AND markup_type = '".$markup."' AND deleted = '0')";
    $transfer_filtering_data .= "AND transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$markup."' AND deleted = '0')";

    $excel_data .= "&markup=".$markup;
  }
  if(isset($_GET['unmark']) AND !empty($_GET['unmark'])){
    $unmark = $_GET['unmark'];
    $income_filtering_data .= " AND ( incomes.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."' AND deleted = '1') OR incomes.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Income' AND markup_type = '".$unmark."')) ";
    $expense_filtering_data .= " AND ( expenses.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."' AND deleted = '1') OR expenses.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Expense' AND markup_type = '".$unmark."')) ";
    $exchange_filtering_data .= " AND ( exchanges.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$unmark."' AND deleted = '1') OR exchanges.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Exchange' AND markup_type = '".$unmark."')) ";
    $purchase_filtering_data .= " AND ( purchases.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Purchase' AND markup_type = '".$unmark."' AND deleted = '1') OR purchases.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Purchase' AND markup_type = '".$unmark."')) ";
    $transfer_filtering_data .= " AND ( transfers.id IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."' AND deleted = '1') OR transfers.id NOT IN (SELECT reference_id FROM markups WHERE reference_type = 'Transfer' AND markup_type = '".$unmark."')) ";

    $excel_data .= "&unmark=".$unmark;
  }

  if(isset($_GET['currency']) AND !empty($_GET['currency'])){
    $currency = $_GET['currency'];
    $income_filtering_data .= " AND incomes.currency='".$currency."' ";
    $expense_filtering_data .= " AND expenses.currency='".$currency."' ";
    $exchange_filtering_data .= " AND (exchanges.from_currency='".$currency."' OR exchanges.to_currency='".$currency."') ";
    $purchase_filtering_data .= " AND purchases.currency='".$currency."' ";
    $transfer_filtering_data .= " AND transfers.currency='".$currency."' ";

    $excel_data .= "&currency=".$currency;
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
    $purchase_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";

    $excel_data .= "&categories_id=".$categories_id;
  }

  if(isset($_GET['sub_categories_id']) AND !empty($_GET['sub_categories_id'])){
    $sub_categories_id = $_GET['sub_categories_id'];
    $income_filtering_data .= " AND incomes.sub_categories_id='".$sub_categories_id."' ";
    $expense_filtering_data .= " AND expenses.sub_categories_id='".$sub_categories_id."' ";
    $exchange_filtering_data .= " AND 0 ";
    $purchase_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";

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


    $excel_data .= "&amount=".$amount;
  }

  if(isset($_GET['sib_number']) AND !empty($_GET['sib_number'])){
    $sib_number = $_GET['sib_number'];
    $income_filtering_data .= " AND incomes.sib_number='$sib_number' ";
    $expense_filtering_data .= " AND 0 ";
    $exchange_filtering_data .= " AND 0 ";
    $purchase_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";

    $excel_data .= "&sib_number=".$sib_number;
  }

  if(isset($_GET['check_number']) AND !empty($_GET['check_number'])){
    $check_number = $_GET['check_number'];
    $income_filtering_data .= " AND incomes.check_number='$check_number' ";
    $expense_filtering_data .= " AND expenses.check_number='$check_number' ";
    $exchange_filtering_data .= " AND 0 ";
    $purchase_filtering_data .= " AND 0 ";
    $transfer_filtering_data .= " AND 0 ";

    $excel_data .= "&check_number=".$check_number;
  }
 
  set_pagination();

  $transaction_sq = $db->query( 
  "SELECT 
  incomes.id AS transaction_id,
  'Income' AS transaction_type,
  incomes.sub_categories_id AS transaction_sub_categories_id,
  incomes.province AS transaction_province,
  incomes.income_date AS transaction_date,
  incomes.income_amount AS transaction_amount,
  incomes.currency AS transaction_currency,
  incomes.description AS transaction_description,
  incomes.users_id AS transaction_users_id,
  incomes.insertion_date AS transaction_insertion_date,
  incomes.check_number AS transaction_check_number,
  incomes.sib_number AS transaction_sib_number,
  incomes.tms_markup AS transaction_tms_markup,
  incomes.qb_markup AS transaction_qb_markup,
  incomes.sib_markup AS transaction_sib_markup,
  incomes.ad_markup AS transaction_ad_markup,
  incomes.additional_informations AS transaction_additional_informations
  FROM `incomes`
  WHERE incomes.deleted='0' 
  AND incomes.insertion_date!=incomes.income_date
  AND incomes.province IN ($accessed_provinces) 
  AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
  $income_filtering_data
  UNION
  SELECT 
  expenses.id AS transaction_id,
  'Expense' AS transaction_type,
  expenses.sub_categories_id AS transaction_sub_categories_id,
  expenses.province AS transaction_province,
  expenses.expense_date AS transaction_date,
  expenses.expense_amount AS transaction_amount,
  expenses.currency AS transaction_currency,
  expenses.description AS transaction_description,
  expenses.users_id AS transaction_users_id,
  expenses.insertion_date AS transaction_insertion_date,
  expenses.check_number AS transaction_check_number,
  '' AS transaction_sib_number,
  expenses.tms_markup AS transaction_tms_markup,
  expenses.qb_markup AS transaction_qb_markup,
  expenses.sib_markup AS transaction_sib_markup,
  expenses.ad_markup AS transaction_ad_markup,
  expenses.additional_informations AS transaction_additional_informations
  FROM `expenses`
  WHERE expenses.deleted='0' 
  AND expenses.insertion_date!=expenses.expense_date
  AND expenses.province IN ($accessed_provinces) 
  AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
  $expense_filtering_data
  UNION
  SELECT 
  exchanges.id AS transaction_id,
  'Exchange' AS transaction_type,
  0 AS transaction_sub_categories_id,
  exchanges.province AS transaction_province,
  exchanges.exchange_date AS transaction_date,
  CONCAT(exchanges.from_amount, ' to ', exchanges.to_amount) AS transaction_amount,
  CONCAT(exchanges.from_currency, ' to ', exchanges.to_currency) AS transaction_currency,
  exchanges.description AS transaction_description,
  exchanges.users_id AS transaction_users_id,
  exchanges.insertion_date AS transaction_insertion_date,
  '' AS transaction_check_number,
  '' AS transaction_sib_number,
  exchanges.tms_markup AS transaction_tms_markup,
  exchanges.qb_markup AS transaction_qb_markup,
  exchanges.sib_markup AS transaction_sib_markup,
  exchanges.ad_markup AS transaction_ad_markup,
  '' AS transaction_additional_informations
  FROM `exchanges`
  WHERE exchanges.deleted='0' 
  AND exchanges.insertion_date!=exchanges.exchange_date
  AND exchanges.province IN ($accessed_provinces) 
  $exchange_filtering_data
  $accessed_sub_categories_exchange
  UNION
  SELECT 
  purchases.id AS transaction_id,
  'Purchase' AS transaction_type,
  0 AS transaction_sub_categories_id,
  purchases.province AS transaction_province,
  purchases.purchase_date AS transaction_date,
  purchases.purchase_amount AS transaction_amount,
  purchases.currency AS transaction_currency,
  purchases.description AS transaction_description,
  purchases.users_id AS transaction_users_id,
  purchases.insertion_date AS transaction_insertion_date,
  '' AS transaction_check_number,
  '' AS transaction_sib_number,
  purchases.tms_markup AS transaction_tms_markup,
  purchases.qb_markup AS transaction_qb_markup,
  purchases.sib_markup AS transaction_sib_markup,
  purchases.ad_markup AS transaction_ad_markup,
  '' AS transaction_additional_informations
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
  transfers.transfer_date AS transaction_date,
  transfers.transfer_amount AS transaction_amount,
  transfers.currency AS transaction_currency,
  transfers.description AS transaction_description,
  transfers.users_id AS transaction_users_id,
  transfers.insertion_date AS transaction_insertion_date,
  '' AS transaction_check_number,
  '' AS transaction_sib_number,
  transfers.tms_markup AS transaction_tms_markup,
  transfers.qb_markup AS transaction_qb_markup,
  transfers.sib_markup AS transaction_sib_markup,
  transfers.ad_markup AS transaction_ad_markup,
  '' AS transaction_additional_informations
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
    AND incomes.insertion_date!=incomes.income_date
    AND incomes.province IN ($accessed_provinces) 
    AND incomes.sub_categories_id IN ($accessed_sub_categories_income)
    $income_filtering_data
    UNION
    SELECT 
      expenses.id AS transaction_id
    FROM `expenses`
    WHERE expenses.deleted='0' 
    AND expenses.insertion_date!=expenses.expense_date
    AND expenses.province IN ($accessed_provinces) 
    AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)
    $expense_filtering_data
    UNION
    SELECT 
      exchanges.id AS transaction_id
    FROM `exchanges`
    WHERE exchanges.deleted='0' 
    AND exchanges.insertion_date!=exchanges.exchange_date
    AND exchanges.province IN ($accessed_provinces) 
    $exchange_filtering_data
    $accessed_sub_categories_exchange
    UNION
    SELECT 
      purchases.id AS transaction_id
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
      transfers.id AS transaction_id
    FROM `transfers`
    WHERE transfers.deleted='0' 
    AND transfers.is_approved='1'
    AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
    $transfer_filtering_data
    $accessed_sub_categories_transfer) AS `all_transactions`"
  );
  extract($Pagenation->fetch());

  $count = 1;
  
  
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
                <form class="form-horizontal" id="form_report_suspicious_transaction" role="form" action="report_suspicious_transaction.php" method="GET" enctype="multipart/form-data">

                  <input type="hidden" name="flag_request" id="flag_request" value="operation"/>

                  <input type="hidden" name="flag_operation" id="flag_operation" value="report_suspicious_transaction"/>

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
                    <label class="col-sm-3 col-form-label" for="transaction_type">Transaction Type</label>
                    <div class="col-sm-6">
                      <select id="transaction_type" name="transaction_type" class="form-control">
                        <option selected value="">Select an option</option>
                        <?php echo get_transaction_type_option($transaction_type); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
                    <div class="col-sm-6">
                      <select id="categories_id" name="categories_id" class="form-control">
                        <?php echo get_category_option('0', $categories_id); ?>
                      </select>
                    </div>
                  </div>

                  
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
                    <div class="col-sm-6">
                      <select id="sub_categories_id" name="sub_categories_id" class="form-control">
                        <?php echo get_sub_category_option($sub_categories_id, '0'); ?>
                      </select>
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
          if(isset($_GET['flag_operation']) AND $_GET['flag_operation']=="report_suspicious_transaction"){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">
                  <i class="fa fa-list"></i>
                  Report of Transactions
                </h4>

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
                        <th>Additional Information</th>
                        <th>Added By</th>
                        <th>Added On</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($transaction_sq->rowCount()>0){
                        
                        while($transaction_row = $transaction_sq->fetch()){

                          $transaction_type = $transaction_row['transaction_type'];
                          $transaction_currency = $transaction_row['transaction_currency'];

                          $sib_number_container = "";
                          $check_number_container = "";
                          $additional_informations = '';

                          $operations = "";
                          $edit_transaction = "";

                          switch ($transaction_type) {
                            case 'Income':{
                              
                              $check_number = $transaction_row['transaction_check_number'];

                              if($check_number!=""){
                                $check_number_container = '
                                <p>'.$check_number.'</p>
                                ';
                              }
                              
                              $sib_number = $transaction_row['transaction_sib_number'];
                              
                              $sib_number_container = '
                              <p>'.$sib_number.'</p>
                              ';

                              if(check_access("system_accessibility/report/report_transaction/edit_sib_number/")){
                                $sib_number_container .= '
                                  <span onclick="edit_sib_number(\''.$transaction_row['transaction_id'].'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
                                ';
                              }
                              $operation_destination = 'controller_income.php';

                              $edit_transaction = 'edit_income_form';
                              
                            }break;

                            case 'Expense':{
                              $sib_number = '';

                              $check_number = $transaction_row['transaction_check_number'];
                              if($check_number!=""){
                                $check_number_container = '
                                <p>'.$check_number.'</p>
                                ';
                              }
                              if(check_access("system_accessibility/report/report_transaction/edit_check_number/")){
                                $check_number_container .= '
                                  <span onclick="edit_check_number(\''.$transaction_row['transaction_id'].'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
                                ';
                              }
                              $operation_destination = 'controller_expense.php';

                              $edit_transaction = 'edit_expense_form';
                            }break;

                            case 'Exchange':{
                              $check_number = '';
                              $sib_number = '';
                              $operation_destination = 'controller_exchange.php';

                              $edit_transaction = 'edit_exchange_form';
                            }break;
                            
                            default:{
                              $check_number = '';
                              $sib_number = '';
                              $operation_destination = '';
                            }break;
                          }

                          if(check_access("system_accessibility/report/report_transaction/view_commnet/")){
                            $operations .= '
                            <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \''.$operation_destination.'\', \'load_comment_panel\', \'general_md\', \''.holu_encode($transaction_row['transaction_id']).'\');"><i class="fas fa-comment"></i> view Comment</a>
                            ';
                          }

                          if(check_access("system_accessibility/report/report_transaction/edit_transaction/")){
                            $operations .= '
                            <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \''.$operation_destination.'\', \''.$edit_transaction.'\', \'general_lg\', \''.holu_encode($transaction_row['transaction_id']).'\');"><i class="fas fa-edit"></i> Edit Transaction</a>
                            ';
                          }

                          if($transaction_row['transaction_sub_categories_id']!=""){
                            $sub_category = get_col('sub_categories', 'sub_category_name', 'id', $transaction_row['transaction_sub_categories_id']);
                          }

                          if(check_access("system_accessibility/report/report_transaction/print_receipt/") AND $transaction_type=="Income"){
                            $operations .= '<a class="dropdown-item" href="print_receipt.php?incomes_id='.holu_encode($transaction_row['transaction_id']).'" target=" _ "><i class="fas fa-print"></i> Print Receipt</a>';
                          }

                          if(check_access("system_accessibility/report/report_transaction/print_voucher/") AND $transaction_type=="Expense"){
                            $operations .= '<a class="dropdown-item" href="print_voucher.php?expenses_id='.holu_encode($transaction_row['transaction_id']).'" target=" _ "><i class="fas fa-print"></i> Print Voucher</a>';
                          }

                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td class="text-right"><p lang="fa" dir="rtl"><?php echo $transaction_row['transaction_description']; ?></p></td>
                            <td><?php echo $transaction_row['transaction_amount']; ?></td>
                            <td><?php echo $transaction_row['transaction_currency']; ?></td>
                            <td><?php echo $transaction_row['transaction_type']; ?></td>
                            <td><?php echo $sub_category; ?></td>
                            <td><?php echo $transaction_row['transaction_date']; ?></td>
                            <td><?php echo $transaction_row['transaction_province']; ?></td>
                            <td class="text-center"><?php echo print_ai_labels(json_decode($transaction_row['transaction_additional_informations'])); ?></td>
                            <td class="text-center">
                              <?php echo get_col('users', 'username', 'id', $transaction_row['transaction_users_id']); ?>
                            </td>
                            <td><?php echo $transaction_row['transaction_insertion_date']; ?></td>
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