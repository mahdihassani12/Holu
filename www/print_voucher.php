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
          margin: 0 0 6px 0;
          color: #fff;
          padding: 30px 15px;
          font-size: 13px;
          line-height: 1.35;
          background-image: url('assets/images/footer-shape.png');
          background-position: center center;
          background-repeat: no-repeat;
          background-size: cover;
        }

        .footer-row{
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          justify-content: flex-start;
          column-gap: 16px;
          row-gap: 8px;
        }

        .footer-item{
          display: inline-flex;
          align-items: center;
          font-weight: 600;
          white-space: nowrap;
        }

        .footer-item.address{
          white-space: normal;
        }

        .footer-icon{
          font-size: 16px;
          margin-right: 6px;
          line-height: 1;
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
            
          <section class="footer">
            <div class="footer-row">
              <div class="footer-item">
                <span class="footer-icon">
                  <!-- Phone -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 16.92V21a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h4.09a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L9.09 9.91a16 16 0 0 0 6 6l1.46-1.23a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92z"/>
                  </svg>
                </span>
                +93 (0)<?php echo $phone; ?>
              </div>

              <div class="footer-item">
                <span class="footer-icon">
                  <!-- Email -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 4h16v16H4z"/>
                    <path d="M22 6l-10 7L2 6"/>
                  </svg>
                </span>
                <?php echo $support_email; ?>
              </div>

              <div class="footer-item">
                <span class="footer-icon">
                  <!-- Website / Globe -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M2 12h20"/>
                    <path d="M12 2a15 15 0 0 1 0 20a15 15 0 0 1 0-20"/>
                  </svg>
                </span>
                <?php echo $website; ?>
              </div>
              <div class="footer-item address">
                <span class="footer-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 21s-6-4.35-6-10a6 6 0 1 1 12 0c0 5.65-6 10-6 10z"/>
                    <circle cx="12" cy="11" r="2.5"/>
                  </svg>
                </span>
                <?php echo $main_office_address; ?>
              </div>
              <div class="footer-item address">
                <span class="footer-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 21s-6-4.35-6-10a6 6 0 1 1 12 0c0 5.65-6 10-6 10z"/>
                    <circle cx="12" cy="11" r="2.5"/>
                  </svg>
                </span>
                <?php echo $branch_office_address; ?>
              </div>
            </div>
          </section>
          
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
