<?php

  include("../lib/_configuration.php");
  $holu_page_paths = ["Management", "List of Sub Categories"];

  set_pagination();

  $sub_category_sq = $db->query("SELECT * FROM `sub_categories` WHERE deleted='0' ORDER BY id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `sub_categories` WHERE deleted='0'");
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
                <h4 class="header-title"><i class="fa fa-list"></i> List of Sub Categories</h4>

                <?php
                if(check_access("system_accessibility/management/list_sub_category/add_sub_category")==1){
                ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_sub_category.php', 'add_sub_category_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add New</button>
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
                        <th>Category</th>
                        <th>Sub Category Name</th>
                        <th>Description</th>
                        <th>Created By</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($sub_category_sq->rowCount()>0){
                        $row_count = 0;
                        while($sub_category_row = $sub_category_sq->fetch()){
                          ?>
                          <tr>
                            <th class="text-center"><?php echo $holu_count++; ?></th>
                            <td><?php echo get_col('categories', 'category_name', 'id', $sub_category_row['categories_id']); ?></td>
                            <td><?php echo $sub_category_row['sub_category_name']; ?></td>
                            <td><?php echo $sub_category_row['description']; ?></td>
                            <td><?php echo isset($sub_category_row['users_id']) ? get_col('users', 'username', 'id', $sub_category_row['users_id']) : '-'; ?></td>
                            <td class="text-center">
                              <div class="dropdown mt-1 opertation_container">
                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                                <div class="dropdown-content dropdown-menu-right operation_list">
                                  <?php
                                  if(check_access("system_accessibility/management/list_sub_category/edit_sub_category")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_sub_category.php', 'edit_sub_category_form', 'general_lg', '<?php echo $sub_category_row['id']; ?>');"><i class="fas fa-edit"></i> Edit Sub Category</a>
                                  <?php
                                  }
                                  ?>

                                  <?php
                                  if(check_access("system_accessibility/management/list_sub_category/delete_sub_category")==1){
                                  ?>
                                  <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_sub_category.php', 'delete_sub_category_form', 'general_md', '<?php echo $sub_category_row['id']; ?>');"><i class="fas fa-trash"></i> Delete Sub Category</a>
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