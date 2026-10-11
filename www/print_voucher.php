<?php 
include("../lib/_configuration.php");
require_once __DIR__."/../lib/_receipt_contacts.php";

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
    $print_contacts = cashtrack_receipt_contacts($expense_row['province']);
    $print_contacts['contacts']['Phone'] = '0748706114';
    unset($print_contacts['bank']);

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
          color: #0f9287;
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
          border-bottom: 4px solid #33364d;
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
          border-left: 18px solid #33364d;
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
          border-left: 6px solid #0f9287;
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
          border-right: 6px solid #0f9287;
          padding-right: 6px;
        }

        #invoice h1 {
          color: #0f9287;
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
          color: #0f9287;
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
          color: #0f9287;
          font-size: 1.4em;
          border-top: 1px solid #0f9287; 

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
          border-left: 6px solid #0f9287;  
        }

        #notices .notice {
          font-size: 1.2em;
        }

        .centeral{
          text-align: center !important;
          color: #0f9287;
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
            color: #33364d;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bolder;
        }
        .thead{
          background: #33364d !important;
          border-right: 1px solid #fff;
          border-top: 1px solid #33364d;
          color: #fff;
        }
        .thead:first-child{
          border-left: 1px solid #33364d;
        }
        .thead:last-child{
          border-right: 1px solid #33364d;
        }
        .tbody{
          border: 1px solid #33364d !important;
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
          font-weight: 800;
          font-size: 2em;
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
          background-color: #33364d;
          background-image: linear-gradient(110deg, #33364d 0%, #08786f 65%, #0f9287 100%);
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
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
              border: 1px solid #33364d;
           }
           .thead{
              -webkit-print-color-adjust: exact;
              background: #33364d !important;
              border-right: 1px solid #fff;
              border-top: 1px solid #33364d;
            }
            .thead:first-child{
              -webkit-print-color-adjust: exact;
              border-left: 1px solid #33364d;
            }
            .thead:last-child{
              -webkit-print-color-adjust: exact;
              border-right: 1px solid #33364d;
            }
            .tbody{
              -webkit-print-color-adjust: exact;
              border: 1px solid #33364d !important;
              border-top: unset !important;
            }
            .reciept_head{
              -webkit-print-color-adjust: exact;
              color: #33364d;
            }
            * {
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
            }
        }
        
        .vira-company-subtitle {
          display: block;
          margin-top: 3px;
          font-size: 0.52em;
          font-weight: 600;
          line-height: 1.35;
          letter-spacing: 0.025em;
          color: #0f9287;
          white-space: normal;
        }
.header_details #company h1 { font-size:28px; line-height:1.15; letter-spacing:-.5px; color:#33364d; }
.vira-company-subtitle { font-size:12px; letter-spacing:1px; margin:6px 0 10px; color:#08786f; }

        .vira-print-contact-footer { position: static; padding: 16px 18px; margin-top: 14px; background: linear-gradient(110deg,#33364d,#08786f); color: #fff; break-inside: avoid; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .vira-print-contact-heading { margin:0 0 10px; font-size:17px; color:#fff; line-height:1.3; }
        .vira-print-contact-heading span { font-size:11px; font-weight:500; letter-spacing:1px; margin-left:8px; }
        .vira-print-contact-grid { display:grid; grid-template-columns:1fr 1fr; gap:6px 20px; }
        .vira-print-contact-item { font-size:11px; line-height:1.55; min-width:0; overflow-wrap:anywhere; }
        @media screen and (max-width:600px) { .vira-print-contact-grid { grid-template-columns:1fr; } }
</style>
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      <link href="assets/css/cashtrack-font.css" rel="stylesheet" type="text/css" />
<link href="assets/css/cashtrack-print.css?v=2" rel="stylesheet" type="text/css" />
</head>
      <body>
        <input type="hidden" id="invoices_id" value="<?php echo $invoices_id; ?>">
        <header class="clearfix">
          <section class="header_details">

            <div id="logo">
                <img src="assets/images/blue-logo.png" alt="CashTrack">
            </div>

            <div id="company">
                <h1>
                  Vira Nawawaran<span class="vira-company-subtitle">ISP &amp; ICT Service Provider</span>
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
            
          <section class="footer vira-print-contact-footer">
            <h3 class="vira-print-contact-heading">Contact Details</h3>
            <div class="vira-print-contact-grid">
              <?php foreach($print_contacts['contacts'] as $label => $value){ ?>
                <div class="vira-print-contact-item"><strong><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></div>
              <?php } ?>
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
