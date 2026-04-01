<?php 
include("../lib/_configuration.php");

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
    $address = 'Gol-e-Sorkh Square, Parwan 2, Street 16, Alley opposite Salam University';
    $phone = '0787506000';
    $bank_name = 'Azizi Bank';
    $account_name = 'BENYAMIN HOPE INFORMATION';
    $account_no_usd = '000101215333739';
    $account_no_afn = '000101115085020';

    $doc_header = "Voucher Payment";
    $bill_number = $expense_row['check_number'];
    $bill_date = $expense_row['expense_date'];
    $to_date = $expense_row['expense_date'];

    $item_table = '
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="thead"><strong>#</strong></th>
            <th class="thead"><strong>Category</strong></th>
            <th class="thead"><strong>Description</strong></th>
            <th class="thead"><strong>Additional Info</strong></th>
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
            <td class="tbody">
              <small>'
                .print_ai_labels(json_decode($expense_row['additional_informations'] ?? '')).
              '</small>
            </td>
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
          padding: 7px;
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
          background: #C1D1CF !important;
          border-right: 1px solid #fff;
          border-top: 1px solid #2c5f59;
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
          margin-top: 5%;
          margin-bottom: 20%;
        }
        .header_details #company{
          margin-left: 12%;
        }
        .footer{
          display: flex;
          justify-content: space-between;
          font-size: 12px;
        }

        .footer > div {
          max-width: 25%;
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
              background: #C1D1CF !important;
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
        }
        </style>
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      </head>
      <body>
        <input type="hidden" id="invoices_id" value="<?php echo $invoices_id; ?>">
        <header class="clearfix">
          <img src="assets/images/header.png" class="header_img" />
          <section class="header_details">

            <div id="logo">
                <img src="assets/images/logo.png">
            </div>

            <div id="company">
                <div>
                  <strong>Benyamin Hope Information</strong>
                </div>
                <div>
                  <strong><?php echo $doc_header; ?></strong>
                </div>
                <div class="date">Number: <?php echo $bill_number; ?></div>
                <div class="date">Date: <?php echo $bill_date; ?></div>
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
            <section class="footer" style="margin: 0px 15px;">
              <div>
                 <i class="fas fa-map-marker-alt"></i>
                 <?php echo $address; ?>
              </div>
              <div>
                  <i class="fab fa-internet-explorer"></i>
                  www.benyaminhope.af
              </div>
              <div>
                  <i class="far fa-envelope"></i>
                  finance@benyaminhope.af
              </div>
              <div>
                  <i class="fas fa-phone"></i>
                  <?php echo $phone; ?>
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
    $address = 'Gol-e-Sorkh Square, Parwan 2, Street 16, Alley opposite Salam University';
    $phone = '0787506000';
    $bank_name = 'Azizi Bank';
    $account_name = 'BENYAMIN HOPE INFORMATION';
    $account_no_usd = '000101215333739';
    $account_no_afn = '000101115085020';

    $doc_header = "Voucher Payment";
    $bill_number = $purchase_row['check_number'];
    $bill_date = $purchase_row['purchase_date'];
    $to_date = $purchase_row['purchase_date'];

    $item_table = '
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="thead"><strong>#</strong></th>
            <th class="thead"><strong>Category</strong></th>
            <th class="thead"><strong>Description</strong></th>
            <th class="thead"><strong>Additional Info</strong></th>
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
            <td class="tbody">
              <small>'
                .print_ai_labels(json_decode($purchase_row['additional_informations'] ?? '')).
              '</small>
            </td>
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
          padding: 7px;
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
          background: #C1D1CF !important;
          border-right: 1px solid #fff;
          border-top: 1px solid #2c5f59;
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
          margin-top: 5%;
          margin-bottom: 20%;
        }
        .header_details #company{
          margin-left: 12%;
        }
        @media print{
          table {
            -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59;
           }
           .thead{
              -webkit-print-color-adjust: exact;
              background: #C1D1CF !important;
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
        }

        </style>
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      </head>
      <body>
        <input type="hidden" id="invoices_id" value="<?php echo $invoices_id; ?>">
        <header class="clearfix">
          <img src="assets/images/header.png" class="header_img" />
          <section class="header_details">

            <div id="logo">
                <img src="assets/images/logo.png">
            </div>

            <div id="company">
                <div>
                  <strong>Benyamin Hope Information</strong>
                </div>
                <div>
                  <strong><?php echo $doc_header; ?></strong>
                </div>
                <div class="date">Number: <?php echo $bill_number; ?></div>
                <div class="date">Date: <?php echo $bill_date; ?></div>
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
            <section class="footer" style="margin: 0px 15px;">
              <div>
                 <i class="fas fa-map-marker-alt"></i>
                 <?php echo $address; ?>
              </div>
              <div>
                  <i class="fab fa-internet-explorer"></i>
                  www.benyaminhope.af
              </div>
              <div>
                  <i class="far fa-envelope"></i>
                  finance@benyaminhope.af
              </div>
              <div>
                  <i class="fas fa-phone"></i>
                  <?php echo $phone; ?>
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
    $address = 'Gol-e-Sorkh Square, Parwan 2, Street 16, Alley opposite Salam University';
    $phone = '0787506000';
    $bank_name = 'Azizi Bank';
    $account_name = 'BENYAMIN HOPE INFORMATION';
    $account_no_usd = '000101215333739';
    $account_no_afn = '000101115085020';

    $doc_header = "Voucher Payment";
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
          padding: 7px;
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
          background: #C1D1CF !important;
          border-right: 1px solid #fff;
          border-top: 1px solid #2c5f59;
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
          margin-top: 5%;
          margin-bottom: 20%;
        }
        .header_details #company{
          margin-left: 12%;
        }
        @media print{
          table {
            -webkit-print-color-adjust: exact;
              border: 1px solid #2c5f59;
           }
           .thead{
              -webkit-print-color-adjust: exact;
              background: #C1D1CF !important;
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
        }
        </style>
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      </head>
      <body>
        <input type="hidden" id="invoices_id" value="<?php echo $invoices_id; ?>">
        <header class="clearfix">
          <img src="assets/images/header.png" class="header_img" />
          <section class="header_details">

            <div id="logo">
                <img src="assets/images/logo.png">
            </div>

            <div id="company">
                <div>
                  <strong>Benyamin Hope Information</strong>
                </div>
                <div>
                  <strong><?php echo $doc_header; ?></strong>
                </div>
                <div class="date">Number: <?php echo $bill_number; ?></div>
                <div class="date">Date: <?php echo $bill_date; ?></div>
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
            <section class="footer" style="margin: 0px 15px;">
              <div>
                 <i class="fas fa-map-marker-alt"></i>
                 <?php echo $address; ?>
              </div>
              <div>
                  <i class="fab fa-internet-explorer"></i>
                  www.benyaminhope.af
              </div>
              <div>
                  <i class="far fa-envelope"></i>
                  finance@benyaminhope.af
              </div>
              <div>
                  <i class="fas fa-phone"></i>
                  <?php echo $phone; ?>
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
