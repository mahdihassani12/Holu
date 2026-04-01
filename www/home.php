<?php
include("../lib/_configuration.php");
$holu_page_paths = ["Home"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("_head.php"); ?>
  <style>
    table tr td {
      width: calc(33.3%);
    }
    .widget-number {
      font-size: 1rem;
      font-weight: 700;
    }
    .widget-icon {
      right: -15px;
      top: -20px;
      opacity: .1;
      position: absolute;
      font-size: 6rem;
    }

  </style>
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

          <?php if(check_access("system_accessibility/home/closing_balance/")==1) : ?>
          <div class="row">
            
            <?php
            foreach ($holu_provinces as $holu_province) :
              if (check_access('province_accessibility/' . $holu_province . '/') != 1)
                continue;
              else
              
              $accessed_province = $holu_province;

              
            ?>
              <div class="col-lg-4 col-md-6">
                <div class="card">
                  <div class="card-body widget">
                  <div class="widget-icon"><i class="fa fa-money-check-alt"></i></div>
                  <div class="card-title"><div class="stat-heading" style="font-size: 20px;"><?= $accessed_province ?></div></div>
                    <div class="stat-widget-five">
                      <div class="stat-content">
                        <table class="w-100">
                          <tr>
                            <td class="widget-number" id="closing_balance_AFN_<?php echo $accessed_province; ?>"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></td>
                            <td><small>AFN</small></td>
                          </tr>
                          <tr>
                            <td class="widget-number" id="closing_balance_USD_<?php echo $accessed_province; ?>"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></td>
                            <td><small>USD</small></td>
                          </tr>
                          <tr>
                            <td class="widget-number" id="closing_balance_IRT_<?php echo $accessed_province; ?>"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></td>
                            <td><small>IRT</small></td>
                          </tr>
                        </table>  
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php
            endforeach;
            ?>
          </div>

          <?php endif; ?>

          <?php if(check_access("system_accessibility/home/ten_highest_expenses/")==1) : ?>

          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">10 Highest Expenses</h4>

              </div>
              <div class="card-box">

                <div class="row">
                  <div class="col-lg-3" id="dashboard_highest_expenses_field">

                    <div class="text-center"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></div>

                  </div>
                  <div class="col-lg-9" style="height: 300px !important;">

                    <div class="table-responsive slimscroll" id="dashboard_highest_expenses_table" style="min-height: 250px !important;">
                      <div class="text-center"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></div>
                    </div>
                    
                  </div>
                </div>

              </div> <!-- end card-box -->
            </div> <!-- end col -->


          </div>

          <?php endif; ?>

          <?php if(check_access("system_accessibility/home/num_transaction/")==1) : ?>

          <div class="row">
            <div class="col-lg-4">
              <div class="card-box card-box-header">

                <h4 class="header-title">Income in Province</h4>

              </div>
              <div class="card-box" id="container_income_in_province_donut" style="height: 300px !important;">

                <div class="w-100 text-center" id="canvas_income_in_province_donut_spinner">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>
                <canvas id="canvas_income_in_province_donut"></canvas>

              </div> <!-- end card-box -->
            </div> <!-- end col -->

            <div class="col-lg-4">
              <div class="card-box card-box-header">

                <h4 class="header-title">Expense in Province</h4>

              </div>
              <div class="card-box" id="container_expense_in_province_donut" style="height: 300px !important;">

                <div class="w-100 text-center" id="canvas_expense_in_province_donut_spinner">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>
                <canvas id="canvas_expense_in_province_donut"></canvas>

              </div> <!-- end card-box -->
            </div> <!-- end col -->

            <div class="col-lg-4">
              <div class="card-box card-box-header">

                <h4 class="header-title">Purchase in Province</h4>

              </div>
              <div class="card-box" id="container_purchase_in_province_donut" style="height: 300px !important;">

                <div class="w-100 text-center" id="canvas_purchase_in_province_donut_spinner">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>
                <canvas id="canvas_purchase_in_province_donut"></canvas>

              </div> <!-- end card-box -->
            </div> <!-- end col -->

            <div class="col-lg-4">
              <div class="card-box card-box-header">

                <h4 class="header-title">Exchange in Province</h4>

              </div>
              <div class="card-box" id="container_exchange_in_province_donut" style="height: 300px !important;">

                <div class="w-100 text-center" id="canvas_exchange_in_province_donut_spinner">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>
                <canvas id="canvas_exchange_in_province_donut"></canvas>

              </div> <!-- end card-box -->
            </div> <!-- end col -->

            <div class="col-lg-4">
              <div class="card-box card-box-header">

                <h4 class="header-title">Transfer(IN) in Province</h4>

              </div>
              <div class="card-box" id="container_transferin_in_province_donut" style="height: 300px !important;">

                <div class="w-100 text-center" id="canvas_transferin_in_province_donut_spinner">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>
                <canvas id="canvas_transferin_in_province_donut"></canvas>

              </div> <!-- end card-box -->
            </div> <!-- end col -->

            <div class="col-lg-4">
              <div class="card-box card-box-header">

                <h4 class="header-title">Transfer(OUT) in Province</h4>

              </div>
              <div class="card-box" id="container_transferout_in_province_donut" style="height: 300px !important;">

                <div class="w-100 text-center" id="canvas_transferout_in_province_donut_spinner">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>
                <canvas id="canvas_transferout_in_province_donut"></canvas>

              </div> <!-- end card-box -->
            </div> <!-- end col -->


          </div>

          <?php endif; ?>

          <?php if(check_access("system_accessibility/home/monthly_income/")==1) : ?>

          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">Monthly Income</h4>

              </div>
              <div class="card-box" style="height: 280px !important;" id="monthly_income">

                <div class="w-100 text-center">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>

              </div> <!-- end card-box -->
            </div> <!-- end col -->
          </div>

          <?php endif; ?>

          <?php if(check_access("system_accessibility/home/monthly_expense/")==1) : ?>

          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">Monthly Expense</h4>

              </div>
              <div class="card-box" style="height: 280px !important;" id="monthly_expense">

                <div class="w-100 text-center">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>

              </div> <!-- end card-box -->
            </div> <!-- end col -->
          </div>

          <?php endif; ?>

          <?php if(check_access("system_accessibility/home/monthly_purchase/")==1) : ?>

          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">Monthly Exchange</h4>

              </div>
              <div class="card-box" style="height: 280px !important;" id="monthly_exchange">

                <div class="w-100 text-center">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>

              </div> <!-- end card-box -->
            </div> <!-- end col -->
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">Monthly Transfer</h4>

              </div>
              <div class="card-box" style="height: 280px !important;" id="monthly_transfer">

                <div class="w-100 text-center">
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                </div>

              </div> <!-- end card-box -->
            </div> <!-- end col -->
          </div>

          <?php endif; ?>
          
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
  <script>
    function sync_dashboard_branch_filter(province, branch_target_id, refresh_callback){
      get_branch_option(province, '0', branch_target_id, true);
      setTimeout(function(){
        if(typeof refresh_callback === 'function'){
          refresh_callback();
        }
      }, 150);
    }
  </script>

  <?php if(check_access('system_accessibility/home/closing_balance/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_closing_balance(){
        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_closing_balance'
          },
          success: function(data_array){

            const new_array = JSON.parse(data_array);
            for (var key in new_array) {
              if (new_array.hasOwnProperty(key)) {
                $("#"+key).html(new_array[key]);
              }
            }
          },
          error: function(xhr, status, error){
              get_dashboard_closing_balance();
          }
        });
      }
      get_dashboard_closing_balance();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/ten_highest_expenses/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_highest_expenses_table(){

        var highest_expense_province = $("#highest_expense_province").val();
        var highest_expense_branch = $("#highest_expense_branch").val();
        var highest_expense_currency = $("#highest_expense_currency").val();
        var highest_expense_expense_date = $("#highest_expense_expense_date").val();

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_highest_expenses_table',
            highest_expense_province:highest_expense_province,
            highest_expense_branch:highest_expense_branch,
            highest_expense_currency:highest_expense_currency,
            highest_expense_expense_date:highest_expense_expense_date
          },
          success: function(data){
            $("#dashboard_highest_expenses_table").html(data);
          },
          error: function(xhr, status, error){
            get_dashboard_highest_expenses_table();
          }
        });
      }

      function get_dashboard_highest_expenses_field(){
        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_highest_expenses_field'
          },
          success: function(data){
            $("#dashboard_highest_expenses_field").html(data);
            get_dashboard_highest_expenses_table();
            reload_js();
          },
          error: function(xhr, status, error){
            get_dashboard_highest_expenses_field();
          }
        });
      }
      get_dashboard_highest_expenses_field();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/num_transaction/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_income_in_province(){

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_income_in_province'
          },
          success: function(data){

            $("#income_in_province_donut_property").html(data);
          },
          error: function(xhr, status, error){
              get_dashboard_income_in_province();
          }
        });
      }
      get_dashboard_income_in_province();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/num_transaction/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_expense_in_province(){

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_expense_in_province'
          },
          success: function(data){

            $("#expense_in_province_donut_property").html(data);
          },
          error: function(xhr, status, error){
              get_dashboard_expense_in_province();
          }
        });
      }
      get_dashboard_expense_in_province();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/num_transaction/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_purchase_in_province(){

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_purchase_in_province'
          },
          success: function(data){

            $("#purchase_in_province_donut_property").html(data);
          },
          error: function(xhr, status, error){
              get_dashboard_purchase_in_province();
          }
        });
      }
      get_dashboard_purchase_in_province();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/num_transaction/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_exchange_in_province(){

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_exchange_in_province'
          },
          success: function(data){

            $("#exchange_in_province_donut_property").html(data);
          },
          error: function(xhr, status, error){
              get_dashboard_exchange_in_province();
          }
        });
      }
      get_dashboard_exchange_in_province();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/num_transaction/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_transferin_in_province(){

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_transferin_in_province'
          },
          success: function(data){

            $("#transferin_in_province_donut_property").html(data);
          },
          error: function(xhr, status, error){
              get_dashboard_transferin_in_province();
          }
        });
      }
      get_dashboard_transferin_in_province();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/num_transaction/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_transferout_in_province(){

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_transferout_in_province'
          },
          success: function(data){

            $("#transferout_in_province_donut_property").html(data);
          },
          error: function(xhr, status, error){
              get_dashboard_transferout_in_province();
          }
        });
      }
      get_dashboard_transferout_in_province();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/monthly_income/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_monthly_income_line(){

        var monthly_income_province = $("#monthly_income_province").val();
        var monthly_income_branch = $("#monthly_income_branch").val();
        var monthly_income_currency = $("#monthly_income_currency").val();
        var monthly_income_year = $("#monthly_income_year").val();

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_income_line',
            monthly_income_province:monthly_income_province,
            monthly_income_branch:monthly_income_branch,
            monthly_income_currency:monthly_income_currency,
            monthly_income_year:monthly_income_year
          },
          success: function(data){
            $("#monthly_income_line_property").html(data);
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_income_line();
          }
        });
      }

      function get_dashboard_monthly_income_field(){
        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_income_field'
          },
          success: function(data){
            $("#monthly_income").html(data);
            get_dashboard_monthly_income_line();
            reload_js();
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_income_field();
          }
        });
      }
      get_dashboard_monthly_income_field();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/monthly_expense/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_monthly_expense_line(){

        var monthly_expense_province = $("#monthly_expense_province").val();
        var monthly_expense_branch = $("#monthly_expense_branch").val();
        var monthly_expense_currency = $("#monthly_expense_currency").val();
        var monthly_expense_year = $("#monthly_expense_year").val();

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_expense_line',
            monthly_expense_province:monthly_expense_province,
            monthly_expense_branch:monthly_expense_branch,
            monthly_expense_currency:monthly_expense_currency,
            monthly_expense_year:monthly_expense_year
          },
          success: function(data){
            $("#monthly_expense_line_property").html(data);
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_expense_line();
          }
        });
      }

      function get_dashboard_monthly_expense_field(){
        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_expense_field'
          },
          success: function(data){
            $("#monthly_expense").html(data);
            get_dashboard_monthly_expense_line();
            reload_js();
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_expense_field();
          }
        });
      }
      get_dashboard_monthly_expense_field();
    </script>
  <?php } ?>

  <?php if(check_access('system_accessibility/home/monthly_purchase/')==1){ ?>
    <script>
      //////////////////////////////////////////
      function get_dashboard_monthly_exchange_line(){

        var monthly_exchange_province = $("#monthly_exchange_province").val();
        var monthly_exchange_branch = $("#monthly_exchange_branch").val();
        var monthly_exchange_currency = $("#monthly_exchange_currency").val();
        var monthly_exchange_year = $("#monthly_exchange_year").val();

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_exchange_line',
            monthly_exchange_province:monthly_exchange_province,
            monthly_exchange_branch:monthly_exchange_branch,
            monthly_exchange_currency:monthly_exchange_currency,
            monthly_exchange_year:monthly_exchange_year
          },
          success: function(data){
            $("#monthly_exchange_line_property").html(data);
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_exchange_line();
          }
        });
      }

      function get_dashboard_monthly_exchange_field(){
        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_exchange_field'
          },
          success: function(data){
            $("#monthly_exchange").html(data);
            get_dashboard_monthly_exchange_line();
            reload_js();
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_exchange_field();
          }
        });
      }
      get_dashboard_monthly_exchange_field();

      function get_dashboard_monthly_transfer_line(){

        var monthly_transfer_province = $("#monthly_transfer_province").val();
        var monthly_transfer_branch = $("#monthly_transfer_branch").val();
        var monthly_transfer_currency = $("#monthly_transfer_currency").val();
        var monthly_transfer_year = $("#monthly_transfer_year").val();

        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_transfer_line',
            monthly_transfer_province:monthly_transfer_province,
            monthly_transfer_branch:monthly_transfer_branch,
            monthly_transfer_currency:monthly_transfer_currency,
            monthly_transfer_year:monthly_transfer_year
          },
          success: function(data){
            $("#monthly_transfer_line_property").html(data);
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_transfer_line();
          }
        });
      }

      function get_dashboard_monthly_transfer_field(){
        $.ajax({
          url:'controller_ajax.php',
          type:'post',
          data:{
            operation:'get_dashboard_monthly_transfer_field'
          },
          success: function(data){
            $("#monthly_transfer").html(data);
            get_dashboard_monthly_transfer_line();
            reload_js();
          },
          error: function(xhr, status, error){
            get_dashboard_monthly_transfer_field();
          }
        });
      }
      get_dashboard_monthly_transfer_field();
    </script>
  <?php } ?>
  <div id="income_in_province_donut_property"></div>
  <div id="expense_in_province_donut_property"></div>
  <div id="purchase_in_province_donut_property"></div>
  <div id="exchange_in_province_donut_property"></div>
  <div id="transferin_in_province_donut_property"></div>
  <div id="transferout_in_province_donut_property"></div>
  <div id="monthly_income_line_property"></div>
  <div id="monthly_expense_line_property"></div>
  <div id="monthly_exchange_line_property"></div>
  <div id="monthly_transfer_line_property"></div>
</body>

</html>
