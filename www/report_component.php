<?php
include("../lib/_configuration.php");
$holu_page_paths = ["Reports", "Report of Components"];

$income_filtering_data = "";
$expense_filtering_data = "";
$exchange_filtering_data = "";

$province = "0";
$from_date = "";
$to_date = "";

if(isset($_POST['province']) AND !empty($_POST['province'])){
  $province = $_POST['province'];
  $income_filtering_data .= " AND province='".$province."' ";
  $expense_filtering_data .= " AND province='".$province."' ";
  $exchange_filtering_data .= " AND province='".$province."' ";
}
if(isset($_POST['from_date']) AND !empty($_POST['from_date'])){
  $from_date = $_POST['from_date'];
  $income_filtering_data .= " AND income_date>='".$from_date."' ";
  $expense_filtering_data .= " AND expense_date>='".$from_date."' ";
  $exchange_filtering_data .= " AND exchange_date>='".$from_date."' ";
}
if(isset($_POST['to_date']) AND !empty($_POST['to_date'])){
  $to_date = $_POST['to_date'];
  $income_filtering_data .= " AND income_date<='".$to_date."' ";
  $expense_filtering_data .= " AND expense_date<='".$to_date."' ";
  $exchange_filtering_data .= " AND exchange_date<='".$to_date."' ";
}

$income_category_sq = $db->query(
  "SELECT * 
  FROM `categories` 
  WHERE deleted='0' 
  AND category_type='Income'"
);

$expense_category_sq = $db->query(
  "SELECT * 
  FROM `categories` 
  WHERE deleted='0' 
  AND category_type='Expense'"
);

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

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="province">Province</label>
                  <div class="col-sm-6">
                    <select id="province" name="province" class="form-control" required onchange="load_data_for_report_component('Balance', '');">
                      <option selected hidden value="">Select an option</option>
                      <?php echo get_province_option($province); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="from_date">Date</label>
                  <div class="col-sm-3">
                    <input type="text" id="from_date" name="from_date" class="form-control date_picker" placeholder="From" value="<?php echo $from_date; ?>" required onchange="load_data_for_report_component('Balance', '');">
                  </div>
                  <div class="col-sm-3">
                    <input type="text" id="to_date" name="to_date" class="form-control date_picker" placeholder="To" value="<?php echo $to_date; ?>" required onchange="load_data_for_report_component('Balance', '');">
                  </div>
                </div>

              </div> <!-- end card-box -->
            </div> <!-- end col -->


          </div>

          <div class="row" id="">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">
                  <i class="fa fa-list"></i>
                  Report of Components
                </h4>

              </div>
              <div class="card-box">
                <div id="tree_view_container">
                  <ul class="tree_view" id="BalanceContainer"></ul>
                </div>
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
  <script type="text/javascript">
    function load_data_for_report_component(information_level, selector){
      var province = $("#province").val();
      var from_date = $("#from_date").val();
      var to_date = $("#to_date").val();
      
      if(province!="" && from_date!="" && to_date!=""){
        if(information_level=="Balance"){
          $("#"+information_level+selector+"Container").html("");
        }
        $.ajax({
          url:'controller_ajax.php',
          method:'post',
          data:{
            operation:'load_data_for_report_component',
            information_level:information_level, 
            selector:selector, 
            province:province,
            from_date:from_date,
            to_date:to_date
          },
          success:function(result){
            if($("#"+information_level+selector+"Container").html()==""){
              $("#"+information_level+selector+"Container").html(result);
            }else{
              $("#"+information_level+selector+"Container").html("");
            }
            
          }
        });
      }
      
    }
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>