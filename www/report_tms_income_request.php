<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of TMS Income Request"];

  $filter_portion1 = '';
  $filter_portion2 = '';
  $filter_portion3 = '';

  if(isset($_GET['filter_customer_name']) AND !empty($_GET['filter_customer_name'])){
    $filter_customer_name = $_GET['filter_customer_name'];
    $filter_portion1 .= " AND internet_income_requests.customer_name LIKE '%".$filter_customer_name."%' ";
    $filter_portion2 .= " AND receiver_income_requests.customer_name LIKE '%".$filter_customer_name."%' ";
    $filter_portion3 .= " AND router_income_requests.customer_name LIKE '%".$filter_customer_name."%' ";
  }
  

  set_pagination();

  $tms_accessed_provinces = '';
  foreach($holu_provinces as $holu_province){
    $access_point = 'province_accessibility/'.$holu_province.'/';
    if(check_access($access_point)==1){

      switch ($holu_province) {
        case 'Herat':{
          $tms_accessed_provinces .= '\'Herat\',';
        }break;

        case 'Kabul':{
          $tms_accessed_provinces .= '\'Kabul\',\'Kabul-AQ\',';
        }break;

        case 'Mazar_Sharif':{
          $tms_accessed_provinces .= '\'Mazar-Sharif\',';
        }break;

        case 'Badghis':{
          $tms_accessed_provinces .= '\'Badghis\',';
        }break;

        case 'Jalaal_Abad':{
          $tms_accessed_provinces .= '\'Jalaal-Abad\',';
        }break;
        
        default:{

        }break;
      }

      
    }
  }
  $tms_accessed_provinces = rtrim($tms_accessed_provinces, ",");

  

  $tms_income_request_sq = $db->query(
    "SELECT 
      internet_income_requests.id as id,
      internet_income_requests.province as province,
      internet_income_requests.customer_name as customer_name,
      internet_income_requests.customer_id as customer_id,
      'Internet Cost' as request_type,
      internet_income_requests.cost as cost,
      internet_income_requests.currency as currency,
      income_request_approvals.id as income_request_approvals_id,
      income_request_approvals.incomes_id as incomes_id
    FROM `tms_customers` as internet_income_requests
    LEFT JOIN income_request_approvals 
      ON income_request_approvals.deleted='0'
      AND income_request_approvals.tms_customers_id=internet_income_requests.id
      AND income_request_approvals.request_type='Internet Cost'
    WHERE internet_income_requests.deleted='0' 
    AND internet_income_requests.cost>0
    AND internet_income_requests.customer_type='Home User'
    AND internet_income_requests.province IN ($tms_accessed_provinces)
    AND internet_income_requests.insert_date>='2021-06-08'
    $filter_portion1
    UNION
    SELECT 
      receiver_income_requests.id as id,
      receiver_income_requests.province as province,
      receiver_income_requests.customer_name as customer_name,
      receiver_income_requests.customer_id as customer_id,
      'Receiver Cost' as request_type,
      receiver_income_requests.receiver_cost as cost,
      receiver_income_requests.receiver_currency as currency,
      income_request_approvals.id as income_request_approvals_id,
      income_request_approvals.incomes_id as incomes_id
    FROM `tms_customers` as receiver_income_requests
    LEFT JOIN income_request_approvals
      ON income_request_approvals.deleted='0'
      AND income_request_approvals.tms_customers_id=receiver_income_requests.id
      AND income_request_approvals.request_type='Receiver Cost'
    WHERE receiver_income_requests.deleted='0' 
    AND receiver_income_requests.receiver_cost>0
    AND receiver_income_requests.customer_type='Home User'
    AND receiver_income_requests.province IN ($tms_accessed_provinces)
    AND receiver_income_requests.insert_date>='2021-06-08'
    $filter_portion2
    UNION
    SELECT 
      router_income_requests.id as id,
      router_income_requests.province as province,
      router_income_requests.customer_name as customer_name,
      router_income_requests.customer_id as customer_id,
      'Router Cost' as request_type,
      router_income_requests.router_cost as cost,
      router_income_requests.router_currency as currency,
      income_request_approvals.id as income_request_approvals_id,
      income_request_approvals.incomes_id as incomes_id
    FROM `tms_customers` as router_income_requests
    LEFT JOIN income_request_approvals
      ON income_request_approvals.deleted='0'
      AND income_request_approvals.tms_customers_id=router_income_requests.id
      AND income_request_approvals.request_type='Router Cost'
    WHERE router_income_requests.deleted='0' 
    AND router_income_requests.router_cost>0
    AND router_income_requests.customer_type='Home User'
    AND router_income_requests.province IN ($tms_accessed_provinces)
    AND router_income_requests.insert_date>='2021-06-08'
    $filter_portion3
    ORDER BY id DESC, request_type DESC limit $holu_to OFFSET $holu_from"
  );


  $Pagenation = $db->query(
    "SELECT count(id) as record 
    FROM (
      SELECT 
        id as id,
        'Internet Cost' as request_type
      FROM `tms_customers` as internet_income_requests
      WHERE deleted='0' 
      AND cost>0
      AND customer_type='Home User'
      AND province IN ($tms_accessed_provinces)
      AND insert_date>='2021-06-08'
      $filter_portion1
      UNION
      SELECT 
        id as id,
        'Receiver Cost' as request_type
      FROM `tms_customers` as receiver_income_requests
      WHERE deleted='0' 
      AND receiver_cost>0
      AND customer_type='Home User'
      AND province IN ($tms_accessed_provinces)
      AND insert_date>='2021-06-08'
      $filter_portion2
      UNION
      SELECT 
        id as id,
        'Router Cost' as request_type
      FROM `tms_customers` as router_income_requests
      WHERE deleted='0' 
      AND router_cost>0
      AND customer_type='Home User'
      AND province IN ($tms_accessed_provinces)
      AND insert_date>='2021-06-08'
      $filter_portion3
    ) as tms_income_requests"
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
                  <?php echo get_table_header('fa fa-list', 'List of TMS Income Requests', $tms_income_request_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php
                if(check_access("system_accessibility/request/report_tms_income_request/print_receipt/")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_income.php', 'filter_table_report_tms_income_request', 'general_lg', '0');"><i class="fa fa-filter"></i> Filter the Table</button>
                <?php
                }
                ?>

              </div>
              <div class="card-box">
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Province</th>
                        <th>Customer Name</th>
                        <th>Customer ID</th>
                        <th>Request Type</th>
                        <th>Cost</th>
                        <th>Currency</th>
                        <th>Is Approved</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($tms_income_request_sq->rowCount()>0){
                        while($tms_income_request_row = $tms_income_request_sq->fetch()){
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo $tms_income_request_row['province']; ?></td>
                            <td><?php echo $tms_income_request_row['customer_name']; ?></td>
                            <td><?php echo $tms_income_request_row['customer_id']; ?></td>
                            <td><?php echo $tms_income_request_row['request_type']; ?></td>
                            <td><?php echo $tms_income_request_row['cost']; ?></td>
                            <td><?php echo $tms_income_request_row['currency']; ?></td>
                            <td><?php echo ($tms_income_request_row['income_request_approvals_id']==null?'No':'Yes'); ?></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  
                                  <?php
                                  if(check_access("system_accessibility/request/report_tms_income_request/approve_tms_income_request/")==1 AND $tms_income_request_row['income_request_approvals_id']==null){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_income.php', 'approve_tms_income_request_form', 'general_lg', '<?php echo holu_encode($tms_income_request_row['id'].'###'.$tms_income_request_row['request_type']); ?>');"><i class="fas fa-circle"></i> Approve TMS Income Request</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/request/report_tms_income_request/print_receipt/") AND $tms_income_request_row['income_request_approvals_id']!=null){
                                  ?>
                                  <a class="dropdown-item" href="print_receipt.php?incomes_id=<?php echo holu_encode($tms_income_request_row['incomes_id']); ?>" target=" _ "><i class="fas fa-print"></i> Print Receipt</a>
                                  <?php
                                  }
                                  ?>

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
</body>
</html>
<?php include("_additional_elements.php"); ?>