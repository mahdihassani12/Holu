<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Transaction", "Exchange"];

  set_filtering_data('exchange_province');
  set_filtering_data('exchange_branch');
  set_filtering_data('exchange_date');

  set_pagination();
  $exchange_access_condition = set_province_branch_portion('province', 'branch');

  

  $exchange_sq = $db->query("SELECT * FROM `exchanges` WHERE deleted='0' $holu_filtering_data AND $exchange_access_condition ORDER BY id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `exchanges` WHERE deleted='0' $holu_filtering_data AND $exchange_access_condition");
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


          <?php
          if(check_access("system_accessibility/transaction/exchange/view_exchange")==1){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">
                  <?php echo get_table_header('fa fa-list', 'List of Exchanges', $exchange_sq->rowCount(), $record, $holu_filtering_array ) ; ?>
                </h4>

                <?php
                if(check_access("system_accessibility/transaction/exchange/filter_table")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_exchange.php', 'filter_table', 'general_lg', '0');"><i class="fa fa-filter"></i> Filter the Table</button>
                <?php
                }
                ?>

                <?php
                if(check_access("system_accessibility/transaction/exchange/add_exchange")){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_exchange.php', 'add_exchange_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add Exchange</button>
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
                        <th>Branch</th>
                        <th>Date</th>
                        <th>From Amount</th>
                        <th>From Currency</th>
                        <th>To Amount</th>
                        <th>To Currency</th>
                        <th>Description</th>
                        <th>Added By</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($exchange_sq->rowCount()>0){
                        while($exchange_row = $exchange_sq->fetch()){
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo $exchange_row['province']; ?></td>
                            <td><?php echo $exchange_row['branch']; ?></td>
                            <td><?php echo $exchange_row['exchange_date']; ?></td>
                            <td><?php echo $exchange_row['from_amount']; ?></td>
                            <td><?php echo $exchange_row['from_currency']; ?></td>
                            <td><?php echo $exchange_row['to_amount']; ?></td>
                            <td><?php echo $exchange_row['to_currency']; ?></td>
                            <td class="text-right"><?php echo $exchange_row['description']; ?></td>
                            <td><?php echo get_col('users', 'username', 'id', $exchange_row['users_id']); ?></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  
                                  <?php
                                  if(check_access("system_accessibility/transaction/exchange/edit_exchange")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_exchange.php', 'edit_exchange_form', 'general_lg', '<?php echo holu_encode($exchange_row['id']); ?>');"><i class="fas fa-edit"></i> Edit Exchange</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/transaction/exchange/delete_exchange")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_exchange.php', 'delete_exchange_form', 'general_md', '<?php echo holu_encode($exchange_row['id']); ?>');"><i class="fas fa-trash"></i> Delete Exchange</a>
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
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
