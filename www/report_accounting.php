<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Reports", "Report of Accounting"];

  $income_filtering_data = "";

  $province = "0";

  if(isset($_GET['flag_operation']) AND $_GET['flag_operation']=="report_accounting"){

    if(isset($_GET['province']) AND !empty($_GET['province'])){
      $province = $_GET['province'];
      $income_filtering_data .= " AND incomes.province='".$province."' ";
    }

    set_pagination();

    $income_sq = $db->query( 
    "SELECT *
    FROM `incomes`
    WHERE deleted='0' 
    AND province IN ($accessed_provinces) 
    AND sub_categories_id IN ($accessed_sub_categories_income)
    AND ( 
      incomes.id IN (
        SELECT reference_id 
        FROM markups 
        WHERE reference_type = 'Income' 
        AND markup_type = 'SIB Markup' 
        AND deleted = '1') 
      OR incomes.id NOT IN (
        SELECT reference_id 
        FROM markups 
        WHERE reference_type = 'Income' 
        AND markup_type = 'SIB Markup'
      )
    )
    $income_filtering_data
    ORDER BY income_date DESC
    limit $holu_to OFFSET $holu_from
    "
    );

    $Pagenation = $db->query(
    "SELECT count(id) as record FROM `incomes`
    WHERE deleted='0' 
    AND province IN ($accessed_provinces) 
    AND sub_categories_id IN ($accessed_sub_categories_income)
    AND ( 
      incomes.id IN (
        SELECT reference_id 
        FROM markups 
        WHERE reference_type = 'Income' 
        AND markup_type = 'SIB Markup' 
        AND deleted = '1') 
      OR incomes.id NOT IN (
        SELECT reference_id 
        FROM markups 
        WHERE reference_type = 'Income' 
        AND markup_type = 'SIB Markup'
      )
    )
    $income_filtering_data");
    extract($Pagenation->fetch());

  }
  
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
                <form class="form-horizontal" id="form_report_accounting" role="form" action="report_accounting.php" method="GET" enctype="multipart/form-data">

                  <input type="hidden" name="flag_request" id="flag_request" value="operation"/>

                  <input type="hidden" name="flag_operation" id="flag_operation" value="report_accounting"/>

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
          if(isset($_GET['flag_operation']) AND $_GET['flag_operation']=="report_accounting"){
          ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">

                <h4 class="header-title">
                  <i class="fa fa-list"></i>
                  Report of Accounting
                </h4>

              </div>
              <div class="card-box">
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0" style="margin-bottom: 20px !important;">
                    <thead>
                      <tr style="border: 0px solid red !important;">
                        <th class="text-center">#</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Date</th>
                        <th>Province</th>
                        <th>Check Number</th>
                        <th>Additional Information</th>
                        <th>Markups</th>
                        <th>SIB Number</th>
                        <th>Accounting Note</th>
                        <th>Added By</th>
                        <th>Operation</th>
                      </tr>
                    </thead>
                    <tbody id="report_accounting_tbody">
                      <?php


                        if($income_sq->rowCount()>0){
                          
                          while($income_row = $income_sq->fetch()){

                            
                            

                            $sib_number_container = "";
                            $accounting_note_container = "";
                            $additional_informations = '';
                            $operations = '';

                            
                            
                            $check_number = $income_row['check_number'];

                            $sib_number = $income_row['sib_number'];
                            
                            if($sib_number!=""){
                              $sib_number_container = '
                              <p>'.$sib_number.'</p>
                              ';
                            }

                            $accounting_note = $income_row['accounting_note'];
                            
                            if($accounting_note!=""){
                              $accounting_note_container = '
                              <p>'.$accounting_note.'</p>
                              ';
                            }

                            if(check_access("system_accessibility/report/report_accounting/edit_sib_number/")){
                              $sib_number_container .= '
                                <span onclick="edit_sib_number(\''.$income_row['id'].'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
                              ';
                            }

                            if(check_access("system_accessibility/report/report_accounting/edit_accounting_note/")){
                              $accounting_note_container .= '
                                <span onclick="edit_accounting_note(\''.$income_row['id'].'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
                              ';
                            }

                            

                            if(check_access("system_accessibility/report/report_accounting/view_commnet/")){
                            $operations .= '
                            <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \'controller_income.php\', \'load_comment_panel\', \'general_md\', \''.holu_encode($income_row['id']).'\');"><i class="fas fa-comment"></i> view Comment</a>
                            ';
                            }

                            if(check_access("system_accessibility/report/report_accounting/view_full_info/")){
                            $operations .= '
                            <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \'controller_income.php\', \'view_full_info\', \'general_lg\', \''.holu_encode($income_row['id']).'\');"><i class="fas fa-info-circle"></i> View Full Info</a>
                            ';
                            }

                            if(check_access("system_accessibility/report/report_accounting/edit_transaction/")){
                              $operations .= '
                              <a class="dropdown-item" onclick="load_modal(\''.$_SERVER['PHP_SELF'].'\', \'controller_income.php\', \'edit_income_form\', \'general_lg\', \''.holu_encode($income_row['id']).'\');"><i class="fas fa-edit"></i> Edit Transaction</a>
                              ';
                            }

                            echo '
                            <tr>
                              <th class="text-center">'.($holu_count++).'</th>
                              <td class="text-right"><p lang="fa" dir="rtl">'.$income_row['description'].'</p></td>
                              <td>'.$income_row['income_amount'].'</td>
                              <td>'.$income_row['currency'].'</td>
                              <td>'.$income_row['income_date'].'</td>
                              <td>'.$income_row['province'].'</td>
                              <td>'.$check_number.'</td>
                              <td class="text-center">'.print_ai_labels(json_decode($income_row['additional_informations'] ?? '')).'</td>
                              <td class="text-center" id="markupsIncome'.$income_row['id'].'">
                                '.get_markups('system_accessibility/report/report_accounting/', 'Income', $income_row['id'], $income_row['tms_markup'], $income_row['qb_markup'], $income_row['sib_markup'], $income_row['ad_markup']).'
                              </td>
                              <td class="text-center" id="sib_number_containerIncome'.$income_row['id'].'">
                                '.$sib_number_container.'
                              </td>
                              <td class="text-center" id="accounting_note_containerIncome'.$income_row['id'].'">
                                '.$accounting_note_container.'
                              </td>
                              <td>'.get_col('users', 'username', 'id', $income_row['users_id']).'</td>
                              <td class="text-center">
                                <div class="dropdown mt-1 opertation_container">
                                  <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                  <div class="dropdown-content dropdown-menu-right operation_list">
                                    
                                    '.$operations.'

                                  </div>
                                </div>
                              </td>
                            </tr>
                            ';
                          }
                        }else{
                          echo '
                          <tr>
                            <th class="text-center" colspan="100">No data to show</th>
                          </tr>
                          ';
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

  function edit_sib_number(incomes_id, operation_type){
  
    if(operation_type=="edit"){
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_sib_number',
          operation_type:operation_type,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#sib_number_containerIncome"+incomes_id).html(result);
        }
      });
    }else if(operation_type=="save"){
      var sib_number = $("#sib_number"+incomes_id).val();
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_sib_number',
          operation_type:operation_type,
          sib_number:sib_number,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#sib_number_containerIncome"+incomes_id).html(result);
        }
      });
    }
  }

  function edit_accounting_note(incomes_id, operation_type){
  
    if(operation_type=="edit"){
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_accounting_note',
          operation_type:operation_type,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#accounting_note_containerIncome"+incomes_id).html(result);
        }
      });
    }else if(operation_type=="save"){
      var accounting_note = $("#accounting_note"+incomes_id).val();
      $.ajax({
        url:'controller_ajax.php',
        method:'post',
        data:{
          operation:'edit_accounting_note',
          operation_type:operation_type,
          accounting_note:accounting_note,
          incomes_id:incomes_id
        },
        success:function(result){
          $("#accounting_note_containerIncome"+incomes_id).html(result);
        }
      });
    }
  }
  </script>
</body>
</html>
<?php include("_additional_elements.php"); ?>
