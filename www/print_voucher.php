<?php 
include("../lib/_configuration.php");

if(!function_exists('get_additional_info_value')){
  function get_additional_info_value($db, $reference_type, $reference_id, $key_info){
    $sq = $db->prepare(
      "SELECT value_info       FROM `additional_informations`
      WHERE deleted='0'
      AND key_info=:key_info
      AND reference_type=:reference_type
      AND reference_id=:reference_id
      LIMIT 1"
    );

    $sq->execute([
      'key_info'=>$key_info,
      'reference_type'=>$reference_type,
      'reference_id'=>$reference_id
    ]);

    $row = $sq->fetch();
    return ($row !== false && isset($row['value_info']) && !empty($row['value_info'])) ? $row['value_info'] : null;
  }
}

if(isset($_GET['expenses_id']) AND !empty($_GET['expenses_id'])){

  $expenses_id = holu_escape(holu_decode($_GET['expenses_id']));

  $expense_sq = $db->prepare(
    "SELECT * 
    FROM `expenses`
    WHERE id=:expenses_id
    LIMIT 1"
  );

  $expense_sqx = $expense_sq->execute([
    'expenses_id'=>$expenses_id
  ]);

  if($expense_sq->rowCount()>0){

    $expense_row = $expense_sq->fetch();

    $invoice_iq = $db->prepare("INSERT INTO `invoices` (
      reference_type,
      reference_id,
      province,
      insertion_date, 
      insertion_time, 
      users_id
    ) VALUES (
      :reference_type,
      :reference_id,
      :province,
      :holu_date, 
      :holu_time, 
      :holu_users_id
    )");
    $invoice_iqx = $invoice_iq->execute([
      'reference_type'=>'Expense',
      'reference_id'=>$expenses_id,
      'province'=>$expense_row['province'],
      'holu_date'=>$holu_date,
      'holu_time'=>$holu_time,
      'holu_users_id'=>$holu_users_id
    ]);

    $invoices_id = $db->lastInsertId();
    $main_office_address = 'Gol-e-Sorkh Square, Parwan 2, Street 16, Alley opposite Salam University.';
    $branch_office_address = 'Mahtab Qala Bus Stop, opposite the new road, inside Rasul Akram Mosque Alley.';
    $finance_email = 'billing@benyaminhope.af';
    $sales_email = 'sales@benyaminhope.af';
    $support_email = 'support@benyaminhope.af';
    $website = 'www.benyaminhope.af';
    $phone = '0787506000';
    $bank_name = 'Azizi Bank';
    $account_name = 'Benyamin Hope IT Services';
    $account_no_afn = '000101115085020';
    $account_no_usd = '000101215333739';

    $customer_name = get_additional_info_value($db, 'Expense', $expenses_id, 'Customer Name');
    $customer_id = get_additional_info_value($db, 'Expense', $expenses_id, 'Customer ID');
    $customer_name_html = !empty($customer_name) ? '<div class="date">Customer Name: '.htmlspecialchars($customer_name).'</div>' : '';
    $customer_id_html = !empty($customer_id) ? '<div class="date">Customer ID: '.htmlspecialchars($customer_id).'</div>' : '';

    $doc_header = "Voucher Payment";
    $bill_number = $expense_row['check_number'];
    $bill_date = $expense_row['expense_date'];
    $to_date = $expense_row['expense_date'];

    $additional_info_html = print_ai_labels(json_decode($expense_row['additional_informations'] ?? ''));
    $has_additional_info = !empty(trim(strip_tags($additional_info_html)));

    $item_table = '
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="thead"><strong>#</strong></th>
            <th class="thead"><strong>Category</strong></th>
            <th class="thead"><strong>Description</strong></th>
            '.($has_additional_info ? '<th class="thead"><strong>Additional Info</strong></th>' : '').'
            <th class="thead"><strong>Currency</strong></th>
            <th class="thead"><strong>Amount</strong></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="tbody"><strong>1</strong></td>
            <td class="tbody">
              <strong>'.get_col('categories', 'category_name', 'id',
                       get_col('sub_categories', 'categories_id', 'id',
                                                 $expense_row['sub_categories_id'])).'<br/>'
                      .get_col('sub_categories', 'sub_category_name', 'id', 
                                                 $expense_row['sub_categories_id']).
              '</strong>
            </td>
            <td class="tbody" style="text-align:center;direction: rtl;">
                <strong lang="fa">'.$expense_row['description'].'</strong>
            </td>
            '.($has_additional_info ? '<td class="tbody"><small>'.$additional_info_html.'</small></td>' : '').'
            <td class="tbody"><strong>'.$expense_row['currency'].'</strong></td>
            <td class="tbody"><strong>'.$expense_row['expense_amount'].'</strong></td>
          </tr>

        </tbody>
      </table>
    ';
    
    ?>

    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <title><?php echo $holu_system_name; ?></title>
        <link rel="shortcut icon" href="assets/images/fav.png">
        <style type="text/css">
        @page {
          size: auto;
          margin-top: 0%;
          margin-bottom: 0%; 
        }
        html{
            background-color: #FFFFFF; 
            margin: 0px;  /* this affects the margin on the html before sending to printer */
        }
        body
        {
            margin: 10px 15px 10px 15px auto; /* margin you want for the content */
        }

        @font-face {
          font-family: SourceSansPro;
          src: url(SourceSansPro-Regular.ttf);
        }

        .clearfix:after {
          content: "";
          display: table;
          clear: both;
        }

        a {
          color: #00b8a5;
          text-decoration: none;
        }

        body {
          position: relative;
          width: 98%;
          color: #000;
          background: #FFFFFF; 
          font-family: Arial, sans-serif; 
          font-size: 14px; 
          font-family: SourceSansPro;
        }

        header {
          padding: 10px 0;
          margin-bottom: 10px;
          border-bottom: 4px solid #2c5f59;
        }

        #logo {
          float: right;
          text-align: right;
        }

        #logo img {
          height: 80px;
        }

        #company {
          float: left;
          border-left: 6px solid #2c5f59;
          padding-left: 10px;
        }


        #details {
          margin-bottom: 20px;
        }

        #bank_details {
          margin-top: 20px;
          margin-bottom: 20px;
        }

        #client {
          padding-left: 6px;
          border-left: 6px solid #00b8a5;
          float: left;
        }

        #client .to {
          color: #000;
        }

        h2.name {
          font-size: 1.4em;
          font-weight: normal;
          margin: 0;
        }

        #invoice {
          float: right;
          text-align: right;
          border-right: 6px solid #00b8a5;
          padding-right: 6px;
        }

        #invoice h1 {
          color: #00b8a5;
          font-size: 2.4em;
          line-height: 1em;
          font-weight: normal;
          margin: 0  0 10px 0;
        }

        #invoice .date {
          color: #000;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          border-spacing: 0;
          margin-bottom: 0px;
        }

        table th,
        table td {
          padding: 9px 7px;
          text-align: center;
          border-bottom: 1px solid #FFFFFF;
        }

        table th {
          white-space: nowrap;        
          font-weight: normal;
        }

        table td {
          text-align: center;
        }

        table td h3{
          color: #00b8a5;
          font-size: 1.2em;
          font-weight: normal;
          margin: 0 0 0.2em 0;
        }

        table .no {
          color: #000;
          font-size: 1.6em;
          background: #dddddd;
          text-align: center;
        }

        table .item {
          text-align: center;
        }

        table .additional_info {
          text-align: center;
        }

        table .end_date {
          background: #DDDDDD;
          text-align: left;
        }

        table .currency {
          text-align: center;
          background: #DDDDDD;
        }

        table .package {
          text-align: center;
          background: #DDDDDD;
        }

        table .start_date {
          text-align: left;
        }

        table .amount {
          background: #eeeeee;
          color: #000;
          text-align: center;
        }

        
        table td.amount,
        table td.amount {
          font-size: 1.2em;
        }

        table tbody tr:last-child td {
          border: none;
        }

        table tr.tfoot td {
          padding: 5px;
          background: #FFFFFF;
          border-bottom: none;
          font-size: 1.2em;
          white-space: nowrap; 
          border-top: 1px solid #AAAAAA; 
        }

        table tr.tfoot:first-child td {
          border-top: none; 
        }

        table tr.tfoot:last-child td {
          color: #00b8a5;
          font-size: 1.4em;
          border-top: 1px solid #00b8a5; 

        }

        table tr.tfoot td:first-child {
          border: none;
        }

        #thanks{
          font-size: 2em;
          margin-bottom: 20px;
        }

        #notices{
          padding-left: 6px;
          border-left: 6px solid #00b8a5;  
        }

        #notices .notice {
          font-size: 1.2em;
        }

        .centeral{
          text-align: center !important;
          color: #00b8a5;
        }

        .signature tbody tr td{
          background-color: #fff !important;
          height: 80px;
          vertical-align: middle !important;
        }

        @font-face {
          font-family: 'Avenir';
          src: url('assets/fonts/Avenir-LT-Std-45-Book_5171.ttf');
          font-weight: normal;
          font-style: normal;
        }

        body{
          font-family: 'Avenir';
        }
        h1,h2,h3,h4,h5,h6{
          font-family: 'Avenir';
        }
        a{
          font-family: 'Avenir';
        }
        p{
          font-family: 'Avenir';
        }
        .reciept_head{
            color: #2c5f59;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bolder;
        }
        .thead{
          background: #2c5f59 !important;
          border-right: 1px solid #fff;
          border-top: 1px solid #2c5f59;
          color: #fff;
        }
        .thead:first-child{
          border-left: 1px solid #2c5f59;
        }
        .thead:last-child{
          border-right: 1px solid #2c5f59;
        }
        .tbody{
          border: 1px solid #2c5f59 !important;
          border-top: unset !important;
          text-align: center;
        }

        .header_img{
          position: absolute;
          top: -7px;
          width: 100%;
          height: 180px;
          object-fit: fill;
        }
        .header_details{
          
        }

        .header_details #company h1{
          margin-bottom: 5px;
        }

        .header_details #company h3{
          margin: 0px;
        }

        .footer{
          font-size: 12px;
          margin: 0 10px;
        }

        .footer-row{
          display: flex;
          width: 100%;
          gap: 16px;
          align-items: stretch;
        }

        .footer-col{
          width: 50%;
          vertical-align: top;
          padding: 0 10px;
          border-left: 2px solid #2c5f59;
          line-height: 1.6;
        }

        .footer-title{
          font-size: 30px;
          line-height: 1.1;
          margin-bottom: 8px;
          font-weight: bold;
          color: #2c5f59;
        }

        .footer-item{
          margin-bottom: 4px;
          position: relative;
        }

        .footer-item::before {
          content: "";
          position: absolute;
          left: -15px;
          top: 6px;
          width: 8px;
          height: 8px;
          background: #2c5f59;
          transform: rotate(45deg);
          border-radius: 1px;
          print-color-adjust: exact;
          -webkit-print-color-adjust: exact;
        }

        .footer-signature{
          color: #2c5f59;
          text-align: right;
          margin-top: 14px;
          font-size: 18px;
          font-weight: bold;
        }

        .footer i{
          display: inline-block;
          font-size: 17px;
        }
        @media print{
          table {
            -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59;
           }
           .thead{
              -webkit-print-color-adjust: exact;
              background: #2c5f59 !important;
              border-right: 1px solid #fff;
              border-top: 1px solid #2c5f59;
            }
            .thead:first-child{
              -webkit-print-color-adjust: exact;
              border-left: 1px solid #2c5f59;
            }
            .thead:last-child{
              -webkit-print-color-adjust: exact;
              border-right: 1px solid #2c5f59;
            }
            .tbody{
              -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59 !important;
              border-top: unset !important;
            }
            .reciept_head{
              -webkit-print-color-adjust: exact;
              color: #2c5f59;
            }
            * {
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
            }
        }
        </style>
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      </head>
      <body>
        <input type="hidden" id="invoices_id" value="<?php echo $invoices_id; ?>">
        <header class="clearfix">
          <section class="header_details">

            <div id="logo">
                <img src="assets/images/blue-logo.png">
            </div>

            <div id="company">
                <h1>
                  <strong>Benyamin Hope Information</strong>
                </h1>
                <h3>
                  <strong><?php echo $doc_header; ?></strong>
                </h3>
                <div class="date">Number: <?php echo $bill_number; ?></div>
                <div class="date">Date: <?php echo $bill_date; ?></div>
                <?php echo $customer_name_html; ?>
                <?php echo $customer_id_html; ?>
            </div>

          </section>
        </header>
        <main>

          <?php echo $item_table; ?>
          <br/>

          <table border="0" cellspacing="0" cellpadding="0" class="signature text-center">
            <tbody>
              <tr>
                <td>
                  <strong class="reciept_head">Receipt Signature</strong>
                </td>
                <td>
                  <strong class="reciept_head">Payment Signature</strong>
                </td>
                <td>
                  <strong class="reciept_head">Approval Signature</strong>
                </td>
              </tr>
            </tbody>
          </table>

          <br/>
            
          <hr style="border: 2px solid #2c5f59;"/>
            <section class="footer">
              <div class="footer-row">
                <div class="footer-col">
                  <div class="footer-title">Contact Details</div>
                  <div class="footer-item"><strong>Main Office:</strong> <?php echo $main_office_address; ?></div>
                  <div class="footer-item"><strong>Branch Office Dasht-e-Barchi:</strong> <?php echo $branch_office_address; ?></div>
                  <div class="footer-item"><strong>Finance Email:</strong> <?php echo $finance_email; ?></div>
                  <div class="footer-item"><strong>Sales Email:</strong> <?php echo $sales_email; ?></div>
                  <div class="footer-item"><strong>Support Email:</strong> <?php echo $support_email; ?></div>
                  <div class="footer-item"><strong>Phone:</strong> <?php echo $phone; ?></div>
                  <div class="footer-item"><strong>Website:</strong> <?php echo $website; ?></div>
                </div>
                <div class="footer-col">
                  <div class="footer-title">Bank Account Details</div>
                  <div class="footer-item"><strong>Bank Name:</strong> <?php echo $bank_name; ?></div>
                  <div class="footer-item"><strong>Account Name:</strong> <?php echo $account_name; ?></div>
                  <div class="footer-item"><strong>Account No-AFN:</strong> <?php echo $account_no_afn; ?></div>
                  <div class="footer-item"><strong>Account No-USD:</strong> <?php echo $account_no_usd; ?></div>
                  <div class="footer-signature">Receipt Signature</div>
                </div>
              </div>
            </section>
          <hr style="border: 2px solid #2c5f59;"/>
        </main>
      </body>
      <!-- Vendor js -->
      <script src="assets/js/vendor.min.js"></script>
      <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
      <script src="assets/libs/peity/jquery.peity.min.js"></script>
      <script type="text/javascript">
      
        setTimeout(function () { 
          window.print(); 
        }, 300);

        window.onafterprint = function(){
          invoices_id = $("#invoices_id").val();
          $.ajax({
            url:'controller_ajax.php',
            method:'post',
            data:{
              operation:'update_invoice',
              invoices_id:invoices_id
            },
            success:function(result){
              window.close();
            }
          });
        }
        
      </script>
    </html>

  <?php
  }
}else if(isset($_GET['purchases_id']) AND !empty($_GET['purchases_id'])){

  $purchases_id = holu_escape(holu_decode($_GET['purchases_id']));

  $purchase_sq = $db->prepare(
    "SELECT * 
    FROM `purchases`
    WHERE id=:purchases_id
    LIMIT 1"
  );

  $purchase_sqx = $purchase_sq->execute([
    'purchases_id'=>$purchases_id
  ]);

  if($purchase_sq->rowCount()>0){

    $purchase_row = $purchase_sq->fetch();

    $invoice_iq = $db->prepare("INSERT INTO `invoices` (
      reference_type,
      reference_id,
      province,
      insertion_date, 
      insertion_time, 
      users_id
    ) VALUES (
      :reference_type,
      :reference_id,
      :province,
      :holu_date, 
      :holu_time, 
      :holu_users_id
    )");
    $invoice_iqx = $invoice_iq->execute([
      'reference_type'=>'Purchase',
      'reference_id'=>$purchases_id,
      'province'=>$purchase_row['province'],
      'holu_date'=>$holu_date,
      'holu_time'=>$holu_time,
      'holu_users_id'=>$holu_users_id
    ]);

    $invoices_id = $db->lastInsertId();
    $main_office_address = 'Gol-e-Sorkh Square, Parwan 2, Street 16, Alley opposite Salam University.';
    $branch_office_address = 'Mahtab Qala Bus Stop, opposite the new road, inside Rasul Akram Mosque Alley.';
    $finance_email = 'billing@benyaminhope.af';
    $sales_email = 'sales@benyaminhope.af';
    $support_email = 'support@benyaminhope.af';
    $website = 'www.benyaminhope.af';
    $phone = '0787506000';
    $bank_name = 'Azizi Bank';
    $account_name = 'Benyamin Hope IT Services';
    $account_no_afn = '000101115085020';
    $account_no_usd = '000101215333739';

    $customer_name = get_additional_info_value($db, 'Purchase', $purchases_id, 'Customer Name');
    $customer_id = get_additional_info_value($db, 'Purchase', $purchases_id, 'Customer ID');
    $customer_name_html = !empty($customer_name) ? '<div class="date">Customer Name: '.htmlspecialchars($customer_name).'</div>' : '';
    $customer_id_html = !empty($customer_id) ? '<div class="date">Customer ID: '.htmlspecialchars($customer_id).'</div>' : '';

    $doc_header = "Voucher Payment";
    $bill_number = $purchase_row['check_number'];
    $bill_date = $purchase_row['purchase_date'];
    $to_date = $purchase_row['purchase_date'];

    $additional_info_html = print_ai_labels(json_decode($purchase_row['additional_informations'] ?? ''));
    $has_additional_info = !empty(trim(strip_tags($additional_info_html)));

    $item_table = '
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="thead"><strong>#</strong></th>
            <th class="thead"><strong>Category</strong></th>
            <th class="thead"><strong>Description</strong></th>
            '.($has_additional_info ? '<th class="thead"><strong>Additional Info</strong></th>' : '').'
            <th class="thead"><strong>Currency</strong></th>
            <th class="thead"><strong>Amount</strong></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="tbody"><strong>1</strong></td>
            <td class="tbody">
              <strong>'.get_col('categories', 'category_name', 'id',
                      get_col('sub_categories', 'categories_id', 'id', 
                                              $purchase_row['sub_categories_id'])).'<br/>'
              .get_col('sub_categories', 'sub_category_name', 'id', 
                                          $purchase_row['sub_categories_id']).
              '</strong>
            </td>
            <td class="tbody" style="text-align:center;direction: rtl;">
                <strong lang="fa">'.
                  $purchase_row['description'].
                '</strong>
            </td>
            '.($has_additional_info ? '<td class="tbody"><small>'.$additional_info_html.'</small></td>' : '').'
            <td class="tbody"><strong>'.$purchase_row['currency'].'</strong></td>
            <td class="tbody"><strong>'.$purchase_row['purchase_amount'].'</strong></td>
          </tr>

        </tbody>
      </table>
    ';
    
    ?>

    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <title><?php echo $holu_system_name; ?></title>
        <link rel="shortcut icon" href="assets/images/fav.png">
        <style type="text/css">
        @page {
          size: auto;
          margin-top: 0%;
          margin-bottom: 0%; 
        }
        html{
            background-color: #FFFFFF; 
            margin: 0px;  /* this affects the margin on the html before sending to printer */
        }
        body
        {
            margin: 10px 15px 10px 15px auto; /* margin you want for the content */
        }

        @font-face {
          font-family: SourceSansPro;
          src: url(SourceSansPro-Regular.ttf);
        }

        .clearfix:after {
          content: "";
          display: table;
          clear: both;
        }

        a {
          color: #00b8a5;
          text-decoration: none;
        }

        body {
          position: relative;
          width: 98%;
          color: #000;
          background: #FFFFFF; 
          font-family: Arial, sans-serif; 
          font-size: 14px; 
          font-family: SourceSansPro;
        }

        header {
          padding: 10px 0;
          margin-bottom: 10px;
          border-bottom: 4px solid #2c5f59;
        }

        #logo {
          float: right;
          text-align: right;
        }

        #logo img {
          height: 80px;
        }

        #company {
          float: left;
          border-left: 6px solid #2c5f59;
          padding-left: 10px;
        }


        #details {
          margin-bottom: 20px;
        }

        #bank_details {
          margin-top: 20px;
          margin-bottom: 20px;
        }

        #client {
          padding-left: 6px;
          border-left: 6px solid #00b8a5;
          float: left;
        }

        #client .to {
          color: #000;
        }

        h2.name {
          font-size: 1.4em;
          font-weight: normal;
          margin: 0;
        }

        #invoice {
          float: right;
          text-align: right;
          border-right: 6px solid #00b8a5;
          padding-right: 6px;
        }

        #invoice h1 {
          color: #00b8a5;
          font-size: 2.4em;
          line-height: 1em;
          font-weight: normal;
          margin: 0  0 10px 0;
        }

        #invoice .date {
          color: #000;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          border-spacing: 0;
          margin-bottom: 0px;
        }

        table th,
        table td {
          padding: 9px 7px;
          text-align: center;
          border-bottom: 1px solid #FFFFFF;
        }

        table th {
          white-space: nowrap;        
          font-weight: normal;
        }

        table td {
          text-align: center;
        }

        table td h3{
          color: #00b8a5;
          font-size: 1.2em;
          font-weight: normal;
          margin: 0 0 0.2em 0;
        }

        table .no {
          color: #000;
          font-size: 1.6em;
          background: #dddddd;
          text-align: center;
        }

        table .item {
          text-align: center;
        }

        table .additional_info {
          text-align: center;
        }

        table .end_date {
          background: #DDDDDD;
          text-align: left;
        }

        table .currency {
          text-align: center;
          background: #DDDDDD;
        }

        table .package {
          text-align: center;
          background: #DDDDDD;
        }

        table .start_date {
          text-align: left;
        }

        table .amount {
          background: #eeeeee;
          color: #000;
          text-align: center;
        }

        
        table td.amount,
        table td.amount {
          font-size: 1.2em;
        }

        table tbody tr:last-child td {
          border: none;
        }

        table tr.tfoot td {
          padding: 5px;
          background: #FFFFFF;
          border-bottom: none;
          font-size: 1.2em;
          white-space: nowrap; 
          border-top: 1px solid #AAAAAA; 
        }

        table tr.tfoot:first-child td {
          border-top: none; 
        }

        table tr.tfoot:last-child td {
          color: #00b8a5;
          font-size: 1.4em;
          border-top: 1px solid #00b8a5; 

        }

        table tr.tfoot td:first-child {
          border: none;
        }

        #thanks{
          font-size: 2em;
          margin-bottom: 20px;
        }

        #notices{
          padding-left: 6px;
          border-left: 6px solid #00b8a5;  
        }

        #notices .notice {
          font-size: 1.2em;
        }

        .centeral{
          text-align: center !important;
          color: #00b8a5;
        }

        .signature tbody tr td{
          background-color: #fff !important;
          height: 80px;
          vertical-align: middle !important;
        }

        @font-face {
          font-family: 'Avenir';
          src: url('assets/fonts/Avenir-LT-Std-45-Book_5171.ttf');
          font-weight: normal;
          font-style: normal;
        }

        body{
          font-family: 'Avenir';
        }
        h1,h2,h3,h4,h5,h6{
          font-family: 'Avenir';
        }
        a{
          font-family: 'Avenir';
        }
        p{
          font-family: 'Avenir';
        }
        .reciept_head{
            color: #2c5f59;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bolder;
        }
        .thead{
          background: #2c5f59 !important;
          border-right: 1px solid #fff;
          border-top: 1px solid #2c5f59;
          color: #fff;
        }
        .thead:first-child{
          border-left: 1px solid #2c5f59;
        }
        .thead:last-child{
          border-right: 1px solid #2c5f59;
        }
        .tbody{
          border: 1px solid #2c5f59 !important;
          border-top: unset !important;
          text-align: center;
        }

        .header_img{
          position: absolute;
          top: -7px;
          width: 100%;
          height: 180px;
          object-fit: fill;
        }
        .header_details{
          
        }

        .header_details #company h1{
          margin-bottom: 5px;
        }

        .header_details #company h3{
          margin: 0px;
        }

        .footer{
          font-size: 12px;
          margin: 0 10px;
        }

        .footer-row{
          display: flex;
          width: 100%;
          gap: 16px;
          align-items: stretch;
        }

        .footer-col{
          width: 50%;
          vertical-align: top;
          padding: 0 10px;
          border-left: 2px solid #2c5f59;
          line-height: 1.6;
        }

        .footer-title{
          font-size: 30px;
          line-height: 1.1;
          margin-bottom: 8px;
          font-weight: bold;
          color: #2c5f59;
        }

        .footer-item{
          margin-bottom: 4px;
          position: relative;
        }

        .footer-item::before {
          content: "";
          position: absolute;
          left: -15px;
          top: 6px;
          width: 8px;
          height: 8px;
          background: #2c5f59;
          transform: rotate(45deg);
          border-radius: 1px;
          print-color-adjust: exact;
          -webkit-print-color-adjust: exact;
        }

        .footer-signature{
          color: #2c5f59;
          text-align: right;
          margin-top: 14px;
          font-size: 18px;
          font-weight: bold;
        }
        @media print{
          table {
            -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59;
           }
           .thead{
              -webkit-print-color-adjust: exact;
              background: #2c5f59 !important;
              border-right: 1px solid #fff;
              border-top: 1px solid #2c5f59;
            }
            .thead:first-child{
              -webkit-print-color-adjust: exact;
              border-left: 1px solid #2c5f59;
            }
            .thead:last-child{
              -webkit-print-color-adjust: exact;
              border-right: 1px solid #2c5f59;
            }
            .tbody{
              -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59 !important;
              border-top: unset !important;
            }
            .reciept_head{
              -webkit-print-color-adjust: exact;
              color: #2c5f59;
            }
            * {
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
            }
        }

        </style>
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      </head>
      <body>
        <input type="hidden" id="invoices_id" value="<?php echo $invoices_id; ?>">
        <header class="clearfix">
          <section class="header_details">

            <div id="logo">
                <img src="assets/images/blue-logo.png">
            </div>

            <div id="company">
                <h1>
                  <strong>Benyamin Hope Information</strong>
                </h1>
                <h3>
                  <strong><?php echo $doc_header; ?></strong>
                </h3>
                <div class="date">Number: <?php echo $bill_number; ?></div>
                <div class="date">Date: <?php echo $bill_date; ?></div>
                <?php echo $customer_name_html; ?>
                <?php echo $customer_id_html; ?>
            </div>

          </section>
        </header>
        <main>

          <?php echo $item_table; ?>
          <br/>

          <table border="0" cellspacing="0" cellpadding="0" class="signature text-center">
            <tbody>
              <tr>
                <td>
                  <strong class="reciept_head">Receipt Signature</strong>
                </td>
                <td>
                  <strong class="reciept_head">Payment Signature</strong>
                </td>
                <td>
                  <strong class="reciept_head">Approval Signature</strong>
                </td>
              </tr>
            </tbody>
          </table>

          <br/>
            
          <hr style="border: 2px solid #2c5f59;"/>
            <section class="footer">
              <div class="footer-row">
                <div class="footer-col">
                  <div class="footer-title">Contact Details</div>
                  <div class="footer-item"><strong>Main Office:</strong> <?php echo $main_office_address; ?></div>
                  <div class="footer-item"><strong>Branch Office Dasht-e-Barchi:</strong> <?php echo $branch_office_address; ?></div>
                  <div class="footer-item"><strong>Finance Email:</strong> <?php echo $finance_email; ?></div>
                  <div class="footer-item"><strong>Sales Email:</strong> <?php echo $sales_email; ?></div>
                  <div class="footer-item"><strong>Support Email:</strong> <?php echo $support_email; ?></div>
                  <div class="footer-item"><strong>Phone:</strong> <?php echo $phone; ?></div>
                  <div class="footer-item"><strong>Website:</strong> <?php echo $website; ?></div>
                </div>
                <div class="footer-col">
                  <div class="footer-title">Bank Account Details</div>
                  <div class="footer-item"><strong>Bank Name:</strong> <?php echo $bank_name; ?></div>
                  <div class="footer-item"><strong>Account Name:</strong> <?php echo $account_name; ?></div>
                  <div class="footer-item"><strong>Account No-AFN:</strong> <?php echo $account_no_afn; ?></div>
                  <div class="footer-item"><strong>Account No-USD:</strong> <?php echo $account_no_usd; ?></div>
                  <div class="footer-signature">Receipt Signature</div>
                </div>
              </div>
            </section>
          <hr style="border: 2px solid #2c5f59;"/>
        </main>
      </body>
      <!-- Vendor js -->
      <script src="assets/js/vendor.min.js"></script>
      <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
      <script src="assets/libs/peity/jquery.peity.min.js"></script>
      <script type="text/javascript">
      
        setTimeout(function () { 
          window.print(); 
        }, 300);

        window.onafterprint = function(){
          invoices_id = $("#invoices_id").val();
          $.ajax({
            url:'controller_ajax.php',
            method:'post',
            data:{
              operation:'update_invoice',
              invoices_id:invoices_id
            },
            success:function(result){
              window.close();
            }
          });
        }
        
      </script>
    </html>

  <?php
  }
}
else if(isset($_GET['transfer_id']) AND !empty($_GET['transfer_id']))
{
  $transfer_id = holu_escape(holu_decode($_GET['transfer_id']));

  $transfer_sq = $db->prepare(
    "SELECT * 
    FROM `transfers`
    WHERE id=:transfer_id
    LIMIT 1"
  );

  $transfer_sqx = $transfer_sq->execute([
    'transfer_id'=>$transfer_id
  ]);

  if($transfer_sq->rowCount()>0)
  {
    $transfer_row = $transfer_sq->fetch();

    $invoice_iq = $db->prepare("INSERT INTO `invoices` (
      reference_type,
      reference_id,
      province,
      insertion_date, 
      insertion_time, 
      users_id
    ) VALUES (
      :reference_type,
      :reference_id,
      :province,
      :holu_date, 
      :holu_time, 
      :holu_users_id
    )");
    $invoice_iqx = $invoice_iq->execute([
      'reference_type'=>'Transfer',
      'reference_id'=>$transfer_id,
      'province'=>$transfer_row['from_province'],
      'holu_date'=>$holu_date,
      'holu_time'=>$holu_time,
      'holu_users_id'=>$holu_users_id
    ]);

    $invoices_id = $db->lastInsertId();
    $main_office_address = 'Gol-e-Sorkh Square, Parwan 2, Street 16, Alley opposite Salam University.';
    $branch_office_address = 'Mahtab Qala Bus Stop, opposite the new road, inside Rasul Akram Mosque Alley.';
    $finance_email = 'billing@benyaminhope.af';
    $sales_email = 'sales@benyaminhope.af';
    $support_email = 'support@benyaminhope.af';
    $website = 'www.benyaminhope.af';
    $phone = '0787506000';
    $bank_name = 'Azizi Bank';
    $account_name = 'Benyamin Hope IT Services';
    $account_no_afn = '000101115085020';
    $account_no_usd = '000101215333739';

    $customer_name = get_additional_info_value($db, 'Transfer', $transfer_id, 'Customer Name');
    $customer_id = get_additional_info_value($db, 'Transfer', $transfer_id, 'Customer ID');
    $customer_name_html = !empty($customer_name) ? '<div class="date">Customer Name: '.htmlspecialchars($customer_name).'</div>' : '';
    $customer_id_html = !empty($customer_id) ? '<div class="date">Customer ID: '.htmlspecialchars($customer_id).'</div>' : '';

    $doc_header = "Voucher Transfer";
    $bill_number = $transfer_row['check_number'];
    $bill_date = $transfer_row['transfer_date'];
    $to_date = $transfer_row['transfer_date'];

    $item_table = '
    <table border="0" cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th class="thead"><strong>#</strong></th>
          <th class="thead"><strong>From Province</strong></th>
          <th class="thead"><strong>To Province</strong></th>
          <th class="thead"><strong>Description</strong></th>
          <th class="thead"><strong>Currency</strong></th>
          <th class="thead"><strong>Amount</strong></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="tbody"><strong>1</strong></td>
          <td class="tbody"><strong>'.$transfer_row['from_province'].'</strong></td>
          <td class="tbody"><strong>'.$transfer_row['to_province'].'</strong></td>
          <td class="tbody" style="text-align:center;direction: rtl;">
            <strong lang="fa">'.$transfer_row['description'].'</strong></td>
          <td class="tbody"><strong>'.$transfer_row['currency'].'</strong></td>
          <td class="tbody"><strong>'.$transfer_row['transfer_amount'].'</strong></td>
        </tr>

      </tbody>
    </table>
  ';
  ?>
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <title><?php echo $holu_system_name; ?></title>
        <link rel="shortcut icon" href="assets/images/fav.png">
        <style type="text/css">
        @page {
          size: auto;
          margin-top: 0%;
          margin-bottom: 0%; 
        }
        html{
            background-color: #FFFFFF; 
            margin: 0px;  /* this affects the margin on the html before sending to printer */
        }
        body
        {
            margin: 10px 15px 10px 15px auto; /* margin you want for the content */
        }

        @font-face {
          font-family: SourceSansPro;
          src: url(SourceSansPro-Regular.ttf);
        }

        .clearfix:after {
          content: "";
          display: table;
          clear: both;
        }

        a {
          color: #00b8a5;
          text-decoration: none;
        }

        body {
          position: relative;
          width: 98%;
          color: #000;
          background: #FFFFFF; 
          font-family: Arial, sans-serif; 
          font-size: 14px; 
          font-family: SourceSansPro;
        }

        header {
          padding: 10px 0;
          margin-bottom: 10px;
          border-bottom: 4px solid #2c5f59;
        }

        #logo {
          float: right;
          text-align: right;
        }

        #logo img {
          height: 80px;
        }

        #company {
          float: left;
          border-left: 6px solid #2c5f59;
          padding-left: 10px;
        }


        #details {
          margin-bottom: 20px;
        }

        #bank_details {
          margin-top: 20px;
          margin-bottom: 20px;
        }

        #client {
          padding-left: 6px;
          border-left: 6px solid #00b8a5;
          float: left;
        }

        #client .to {
          color: #000;
        }

        h2.name {
          font-size: 1.4em;
          font-weight: normal;
          margin: 0;
        }

        #invoice {
          float: right;
          text-align: right;
          border-right: 6px solid #00b8a5;
          padding-right: 6px;
        }

        #invoice h1 {
          color: #00b8a5;
          font-size: 2.4em;
          line-height: 1em;
          font-weight: normal;
          margin: 0  0 10px 0;
        }

        #invoice .date {
          color: #000;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          border-spacing: 0;
          margin-bottom: 0px;
        }

        table th,
        table td {
          padding: 9px 7px;
          text-align: center;
          border-bottom: 1px solid #FFFFFF;
        }

        table th {
          white-space: nowrap;        
          font-weight: normal;
        }

        table td {
          text-align: center;
        }

        table td h3{
          color: #00b8a5;
          font-size: 1.2em;
          font-weight: normal;
          margin: 0 0 0.2em 0;
        }

        table .no {
          color: #000;
          font-size: 1.6em;
          background: #dddddd;
          text-align: center;
        }

        table .item {
          text-align: center;
        }

        table .additional_info {
          text-align: center;
        }

        table .end_date {
          background: #DDDDDD;
          text-align: left;
        }

        table .currency {
          text-align: center;
          background: #DDDDDD;
        }

        table .package {
          text-align: center;
          background: #DDDDDD;
        }

        table .start_date {
          text-align: left;
        }

        table .amount {
          background: #eeeeee;
          color: #000;
          text-align: center;
        }

        
        table td.amount,
        table td.amount {
          font-size: 1.2em;
        }

        table tbody tr:last-child td {
          border: none;
        }

        table tr.tfoot td {
          padding: 5px;
          background: #FFFFFF;
          border-bottom: none;
          font-size: 1.2em;
          white-space: nowrap; 
          border-top: 1px solid #AAAAAA; 
        }

        table tr.tfoot:first-child td {
          border-top: none; 
        }

        table tr.tfoot:last-child td {
          color: #00b8a5;
          font-size: 1.4em;
          border-top: 1px solid #00b8a5; 

        }

        table tr.tfoot td:first-child {
          border: none;
        }

        #thanks{
          font-size: 2em;
          margin-bottom: 20px;
        }

        #notices{
          padding-left: 6px;
          border-left: 6px solid #00b8a5;  
        }

        #notices .notice {
          font-size: 1.2em;
        }

        .centeral{
          text-align: center !important;
          color: #00b8a5;
        }

        .signature tbody tr td{
          background-color: #fff !important;
          height: 80px;
          vertical-align: middle !important;
        }

        @font-face {
          font-family: 'Avenir';
          src: url('assets/fonts/Avenir-LT-Std-45-Book_5171.ttf');
          font-weight: normal;
          font-style: normal;
        }

        body{
          font-family: 'Avenir';
        }
        h1,h2,h3,h4,h5,h6{
          font-family: 'Avenir';
        }
        a{
          font-family: 'Avenir';
        }
        p{
          font-family: 'Avenir';
        }
        .reciept_head{
            color: #2c5f59;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bolder;
        }
        .thead{
          background: #2c5f59 !important;
          border-right: 1px solid #fff;
          border-top: 1px solid #2c5f59;
          color: #fff;
        }
        .thead:first-child{
          border-left: 1px solid #2c5f59;
        }
        .thead:last-child{
          border-right: 1px solid #2c5f59;
        }
        .tbody{
          border: 1px solid #2c5f59 !important;
          border-top: unset !important;
          text-align: center;
        }

        .header_img{
          position: absolute;
          top: -7px;
          width: 100%;
          height: 180px;
          object-fit: fill;
        }
        .header_details{
          
        }

        .header_details #company h1{
          margin-bottom: 5px;
        }

        .header_details #company h3{
          margin: 0px;
        }

        .footer{
          font-size: 12px;
          margin: 0 10px;
        }

        .footer-row{
          display: flex;
          width: 100%;
          gap: 16px;
          align-items: stretch;
        }

        .footer-col{
          width: 50%;
          vertical-align: top;
          padding: 0 10px;
          border-left: 2px solid #2c5f59;
          line-height: 1.6;
        }

        .footer-title{
          font-size: 30px;
          line-height: 1.1;
          margin-bottom: 8px;
          font-weight: bold;
          color: #2c5f59;
        }

        .footer-item{
          margin-bottom: 4px;
          position: relative;
        }

        .footer-item::before {
          content: "";
          position: absolute;
          left: -15px;
          top: 6px;
          width: 8px;
          height: 8px;
          background: #2c5f59;
          transform: rotate(45deg);
          border-radius: 1px;
          print-color-adjust: exact;
          -webkit-print-color-adjust: exact;
        }

        .footer-signature{
          color: #2c5f59;
          text-align: right;
          margin-top: 14px;
          font-size: 18px;
          font-weight: bold;
        }
        @media print{
          table {
            -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59;
           }
           .thead{
              -webkit-print-color-adjust: exact;
              background: #2c5f59 !important;
              border-right: 1px solid #fff;
              border-top: 1px solid #2c5f59;
            }
            .thead:first-child{
              -webkit-print-color-adjust: exact;
              border-left: 1px solid #2c5f59;
            }
            .thead:last-child{
              -webkit-print-color-adjust: exact;
              border-right: 1px solid #2c5f59;
            }
            .tbody{
              -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59 !important;
              border-top: unset !important;
            }
            .reciept_head{
              -webkit-print-color-adjust: exact;
              color: #2c5f59;
            }
            * {
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
            }
        }
        </style>
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      </head>
      <body>
        <input type="hidden" id="invoices_id" value="<?php echo $invoices_id; ?>">
        <header class="clearfix">
          <section class="header_details">

            <div id="logo">
                <img src="assets/images/blue-logo.png">
            </div>

            <div id="company">
                <h1>
                  <strong>Benyamin Hope Information</strong>
                </h1>
                <h3>
                  <strong><?php echo $doc_header; ?></strong>
                </h3>
                <div class="date">Number: <?php echo $bill_number; ?></div>
                <div class="date">Date: <?php echo $bill_date; ?></div>
                <?php echo $customer_name_html; ?>
                <?php echo $customer_id_html; ?>
            </div>

          </section>
        </header>
        <main>

          <?php echo $item_table; ?>
          <br/>

          <table border="0" cellspacing="0" cellpadding="0" class="signature text-center">
            <tbody>
              <tr>
                <td>
                  <strong class="reciept_head">Receipt Signature</strong>
                </td>
                <td>
                  <strong class="reciept_head">Payment Signature</strong>
                </td>
                <td>
                  <strong class="reciept_head">Approval Signature</strong>
                </td>
              </tr>
            </tbody>
          </table>

          <br/>
            
          <hr style="border: 2px solid #2c5f59;"/>
            <section class="footer">
              <div class="footer-row">
                <div class="footer-col">
                  <div class="footer-title">Contact Details</div>
                  <div class="footer-item"><strong>Main Office:</strong> <?php echo $main_office_address; ?></div>
                  <div class="footer-item"><strong>Branch Office Dasht-e-Barchi:</strong> <?php echo $branch_office_address; ?></div>
                  <div class="footer-item"><strong>Finance Email:</strong> <?php echo $finance_email; ?></div>
                  <div class="footer-item"><strong>Sales Email:</strong> <?php echo $sales_email; ?></div>
                  <div class="footer-item"><strong>Support Email:</strong> <?php echo $support_email; ?></div>
                  <div class="footer-item"><strong>Phone:</strong> <?php echo $phone; ?></div>
                  <div class="footer-item"><strong>Website:</strong> <?php echo $website; ?></div>
                </div>
                <div class="footer-col">
                  <div class="footer-title">Bank Account Details</div>
                  <div class="footer-item"><strong>Bank Name:</strong> <?php echo $bank_name; ?></div>
                  <div class="footer-item"><strong>Account Name:</strong> <?php echo $account_name; ?></div>
                  <div class="footer-item"><strong>Account No-AFN:</strong> <?php echo $account_no_afn; ?></div>
                  <div class="footer-item"><strong>Account No-USD:</strong> <?php echo $account_no_usd; ?></div>
                  <div class="footer-signature">Receipt Signature</div>
                </div>
              </div>
            </section>
          <hr style="border: 2px solid #2c5f59;"/>
        </main>
      </body>
      <!-- Vendor js -->
      <script src="assets/js/vendor.min.js"></script>
      <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
      <script src="assets/libs/peity/jquery.peity.min.js"></script>
      <script type="text/javascript">
      
        setTimeout(function () { 
          window.print(); 
        }, 300);

        window.onafterprint = function(){
          invoices_id = $("#invoices_id").val();
          $.ajax({
            url:'controller_ajax.php',
            method:'post',
            data:{
              operation:'update_invoice',
              invoices_id:invoices_id
            },
            success:function(result){
              window.close();
            }
          });
        }
        
      </script>
    </html>
  <?php
  }
}
?>
