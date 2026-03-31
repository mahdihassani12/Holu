<?php 
include("../lib/_configuration.php");

if(isset($_GET['incomes_id']) AND !empty($_GET['incomes_id'])){

  $incomes_id = holu_escape(holu_decode($_GET['incomes_id']));

  $income_sq = $db->prepare(
    "SELECT * 
    FROM `incomes`
    WHERE id=:incomes_id
    LIMIT 1"
  );

  $income_sqx = $income_sq->execute([
    'incomes_id'=>$incomes_id
  ]);

  if($income_sq->rowCount()>0){

    $income_row = $income_sq->fetch();

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
      'reference_type'=>'Income',
      'reference_id'=>$incomes_id,
      'province'=>$income_row['province'],
      'holu_date'=>$holu_date,
      'holu_time'=>$holu_time,
      'holu_users_id'=>$holu_users_id
    ]);

    $invoices_id = $db->lastInsertId();
    $address = 'Gol-e-Sorkh Square, Parwan 2, Street 16, Alley opposite Salam University';
    $finance_phone = '0787506000';
    $bank_name = 'Azizi Bank';
    $account_name = 'BENYAMIN HOME INFORMATION';
    $account_no_usd = '000101215333739';
    $account_no_afn = '000101115085020';

    $customer_name_sq = $db->prepare(
      "SELECT value_info 
      FROM `additional_informations`
      WHERE deleted='0'
      AND key_info='Customer Name'
      AND reference_type='Income'
      AND reference_id=:reference_id
      LIMIT 1"
    );
    $customer_name_sqx = $customer_name_sq->execute([
      'reference_id'=>$incomes_id
    ]);
    $customer_name_row = $customer_name_sq->fetch();
    $customer_name = $customer_name_row['value_info'];

    $customer_id_sq = $db->prepare(
      "SELECT value_info 
      FROM `additional_informations`
      WHERE deleted='0'
      AND key_info='Customer ID'
      AND reference_type='Income'
      AND reference_id=:reference_id
      LIMIT 1"
    );
    $customer_id_sqx = $customer_id_sq->execute([
      'reference_id'=>$incomes_id
    ]);
    $customer_id_row = $customer_id_sq->fetch();
    $customer_id = $customer_id_row['value_info'];

    $bill_number = $income_row['check_number'];
    $bill_date = $income_row['income_date'];
    $to_date = date("Y-m-d");

    $package_sq = $db->prepare(
      "SELECT value_info 
      FROM `additional_informations`
      WHERE deleted='0'
      AND key_info='Package'
      AND reference_type='Income'
      AND reference_id=:reference_id
      LIMIT 1"
    );
    $package_sqx = $package_sq->execute([
      'reference_id'=>$incomes_id
    ]);

    $equipment_sq = $db->prepare(
      "SELECT value_info 
      FROM `additional_informations`
      WHERE deleted='0'
      AND key_info='Equipment'
      AND reference_type='Income'
      AND reference_id=:reference_id
      LIMIT 1"
    );
    $equipment_sqx = $equipment_sq->execute([
      'reference_id'=>$incomes_id
    ]);

    $other_services_sq = $db->prepare(
      "SELECT value_info 
      FROM `additional_informations`
      WHERE deleted='0'
      AND key_info='Other Services'
      AND reference_type='Income'
      AND reference_id=:reference_id
      LIMIT 1"
    );
    $other_services_sqx = $other_services_sq->execute([
      'reference_id'=>$incomes_id
    ]);
    if($package_sq->rowCount()>0){
      $doc_header = 'Voucher Receipt';
      $package_row = $package_sq->fetch();
      $item = 'Internet';
      $package = $package_row['value_info'];

      $start_date_sq = $db->prepare(
        "SELECT value_info 
        FROM `additional_informations`
        WHERE deleted='0'
        AND key_info='Start Date'
        AND reference_type='Income'
        AND reference_id=:reference_id
        LIMIT 1"
      );
      $start_date_sqx = $start_date_sq->execute([
        'reference_id'=>$incomes_id
      ]);
      $start_date_row = $start_date_sq->fetch();
      $start_date = $start_date_row['value_info'];

      $end_date_sq = $db->prepare(
        "SELECT value_info 
        FROM `additional_informations`
        WHERE deleted='0'
        AND key_info='End Date'
        AND reference_type='Income'
        AND reference_id=:reference_id
        LIMIT 1"
      );
      $end_date_sqx = $end_date_sq->execute([
        'reference_id'=>$incomes_id
      ]);
      $end_date_row = $end_date_sq->fetch();
      $end_date = $end_date_row['value_info'];

      $currency = $income_row['currency'];
      $amount = $income_row['income_amount'];

      $item_table = '
        <table border="0" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th class="thead"><strong>#</strong></th>
              <th class="thead"><strong>Item</strong></th>
              <th class="thead"><strong>Package</strong></th>
              <th class="thead"><strong>Start Date</strong></th>
              <th class="thead"><strong>End Date</strong></th>
              <th class="thead"><strong>Currency</strong></th>
              <th class="thead"><strong>Amount</strong></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="tbody"><strong>1</strong></td>
              <td class="tbody"><strong>'.$item.'</strong></td>
              <td class="tbody"><strong>'.$package.'</strong></td>
              <td class="tbody"><strong>'.$start_date.'</strong></td>
              <td class="tbody"><strong>'.$end_date.'</strong></td>
              <td class="tbody"><strong>'.$currency.'</strong></td>
              <td class="tbody"><strong>'.$amount.'</strong></td>
            </tr>

          </tbody>
        </table>
      ';

    }else if($equipment_sq->rowCount()>0){
      $doc_header = 'Voucher Receipt';
      $equipment_row = $equipment_sq->fetch();
      $item = 'Equipment';
      $equipment = $equipment_row['value_info'];

      $currency = $income_row['currency'];
      $amount = $income_row['income_amount'];

      $item_table = '
        <table border="0" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th class="thead"><strong>#</strong></th>
              <th class="thead"><strong>Item</strong></th>
              <th class="thead"><strong>Equipment</strong></th>
              <th class="thead"><strong>Currency</strong></th>
              <th class="thead"><strong>Amount</strong></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="tbody"><strong>1</strong></td>
              <td class="tbody"><strong>'.$item.'</strong></td>
              <td class="tbody"><strong>'.$equipment.'</strong></td>
              <td class="tbody"><strong>'.$currency.'</strong></td>
              <td class="tbody"><strong>'.$amount.'</strong></td>
            </tr>

          </tbody>
        </table>
      ';

    }else if($other_services_sq->rowCount()>0){
      $doc_header = 'Voucher Receipt';
      $other_services_row = $other_services_sq->fetch();
      $item = 'Other Services';
      $other_services = $other_services_row['value_info'];

      $currency = $income_row['currency'];
      $amount = $income_row['income_amount'];

      $item_table = '
        <table border="0" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th class="thead"><strong>#</strong></th>
              <th class="thead"><strong>Item</strong></th>
              <th class="thead"><strong>Other Services</strong></th>
              <th class="thead"><strong>Currency</strong></th>
              <th class="thead"><strong>Amount</strong></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="tbody"><strong>1</strong></td>
              <td class="tbody"><strong>'.$item.'</strong></td>
              <td class="tbody"><strong>'.$other_services.'</strong></td>
              <td class="tbody"><strong>'.$currency.'</strong></td>
              <td class="tbody"><strong>'.$amount.'</strong></td>
            </tr>
          </tbody>
        </table>
      ';

    }else{
      $doc_header = 'Voucher Receipt';

      $currency = $income_row['currency'];
      $amount = $income_row['income_amount'];
      $description = $income_row['description'];
      $item_table = '
        <table border="0" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th class="thead"><strong>#</strong></th>
              <th class="thead"><strong>Description</strong></th>
              <th class="thead"><strong>Currency</strong></th>
              <th class="thead"><strong>Amount</strong></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="tbody"><strong>1</strong></td>
              <td class="tbody"><strong>'.$description.'</strong></td>
              <td class="tbody"><strong>'.$currency.'</strong></td>
              <td class="tbody"><strong>'.$amount.'</strong></td>
            </tr>
          </tbody>
        </table>
      ';
    }
    
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
          display: flex;
          justify-content: center;
          align-items: center;
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
          margin-top: 10px;
          margin-bottom: 10px;
        }

        #client {
          padding-left: 6px;
          border-left: 6px solid #2c5f59;
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
          border: 1px solid #2c5f59;
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
          text-align: right;
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
          background: #ddd;
          text-align: center;
        }

        table .item {
          text-align: left;
        }

        table .end_date {
          background: #DDDDDD;
          text-align: left;
        }

        table .currency {
          text-align: center;
        }

        table .package {
          text-align: left;
          background: #DDDDDD;
        }

        table .start_date {
          text-align: left;
        }

        table .amount {
          background: #ddd;
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
        .sign{
          color: #2c5f59;
          font-weight: bolder;
          font-size: 18px;
          display: inline-block;
          padding: 5px 10px;
          border-radius: 5px;
          margin-left: 100px;
          margin-top: 25px;
        }
        #bank_details{
          display:flex; 
          margin-bottom: 30px;
        }

        .thead{
          background: #C1D1CF !important;
          border-right: 1px solid #fff;
        }
        .thead:last-child{
          border-right: 1px solid #2c5f59;
        }
        .tbody{
          border: 1px solid #2c5f59 !important;
          border-top: unset !important;
          text-align: center;
        }
        #client:first-child{
          border-width: 20px;
          width: auto;
          margin-right: 15px;
        }
        #client:first-child i{
          display: inline-block;
          font-size: 15px;
          margin-left: -6.5%;
          margin-right: 15px;
          color: #c1d1cf;
        }
        .header_img{
          position: absolute;
          top: -10px;
          width: 100%;
          height: 200px;
          object-fit: fill;
        }
        .header_details{
          display: flex;
          justify-content: space-between;
          margin-top: 5%;
        }
        .header_details div:first-child{
            margin-left: 12%;
        }
        
        @media print{
          table {
              border: 1px solid #2c5f59;
              -webkit-print-color-adjust: exact;
           }
           .thead{
              -webkit-print-color-adjust: exact;
              background: #C1D1CF !important;
              border-right: 1px solid #fff;
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
            .sign{
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
              <div>
                <h2 class="name">Benyamin Hope</h2>
                <h2 class="name">Customer Details</h2>
                <div class="address">Customer Name: <?php echo $customer_name; ?></div>
                <div class="address">Customer ID: <?php echo $customer_id; ?></div>
                <h2 class="name"><?php echo $doc_header; ?></h2>
                <div class="date">Number: <?php echo $bill_number; ?></div>
                <div class="date">Date: <?php echo $bill_date; ?></div>
              </div>

              <div id="logo">
                <img src="assets/images/logo.png">
              </div>
            </section>
        </header>
        <main>

          <?php echo $item_table; ?>

          <div id="bank_details" class="clearfix">

            <div id="client" style="line-height: 1.5;">
              <h2 class="name">Contact Details</h2>
              <div>
                Address: <?php echo $address; ?>
              </div>
              <div>
                Finance Email: billing@benyaminhome.af
              </div>
              <div>
                Sales Email: sales@benyaminhome.af
              </div>
              <div>
                Support Email: support@benyaminhome.af
              </div>
              <div>
                Phone: <?php echo $finance_phone; ?>
              </div>
              <div>
                Website: www.benyaminhome.af
              </div>
            </div>

            <div id="client" style="line-height: 1.5;">
              <h2 class="name">Bank Account Details</h2>
              <div class="address">Bank Name: <?php echo $bank_name; ?></div>
              <div class="address">Account Name: <?php echo $account_name; ?></div>
              <div class="address">Account No-USD: <?php echo $account_no_usd; ?></div>
              <div class="address">Account No-AFN: <?php echo $account_no_afn; ?></div>
              <div>
                <strong class="sign">Receipt Signature</strong>
              </div>
            </div>
          </div>

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