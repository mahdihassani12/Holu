<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Management", "List of Branches"];

  set_pagination();

  $branch_sq = $db->query("SELECT branches.*, provinces.name AS province_name
    FROM `branches`
    LEFT JOIN `provinces` ON provinces.id=branches.province_id
    ORDER BY branches.id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `branches`");
  extract($Pagenation->fetch());
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("_head.php"); ?>
</head>
<body class="left-side-menu-dark">
  <div id="wrapper">
    <div class="navbar-custom">
      <?php include("_navbar.php"); ?>
    </div>
    <div class="left-side-menu">
      <?php include("_sidebar.php"); ?>
    </div>
    <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <?php include("_page_title.php"); ?>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card-box card-box-header">
                <h4 class="header-title"><i class="fa fa-list"></i> List of Branches</h4>

                <?php if(check_access("system_accessibility/management/list_branch/add_branch")==1){ ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_branch.php', 'add_branch_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add New</button>
                <?php } ?>
              </div>

              <div class="card-box">
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Branch Name</th>
                        <th>Province</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($branch_sq->rowCount()>0){
                        while($branch_row = $branch_sq->fetch()){
                      ?>
                      <tr>
                        <th class="text-center"><?php echo $holu_count++; ?></th>
                        <td><?php echo $branch_row['name']; ?></td>
                        <td><?php echo $branch_row['province_name'] ?? '-'; ?></td>
                        <td class="text-center">
                          <div class="dropdown mt-1 opertation_container">
                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                            <div class="dropdown-content dropdown-menu-right operation_list">
                              <?php if(check_access("system_accessibility/management/list_branch/edit_branch")==1){ ?>
                              <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_branch.php', 'edit_branch_form', 'general_lg', '<?php echo $branch_row['id']; ?>');"><i class="fas fa-edit"></i> Edit Branch</a>
                              <?php } ?>

                              <?php if(check_access("system_accessibility/management/list_branch/delete_branch")==1){ ?>
                              <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_branch.php', 'delete_branch_form', 'general_md', '<?php echo $branch_row['id']; ?>');"><i class="fas fa-trash"></i> Delete Branch</a>
                              <?php } ?>
                            </div>
                          </div>
                        </td>
                      </tr>
                      <?php
                        }
                      } else {
                      ?>
                      <tr>
                        <th class="text-center" colspan="100">No data to show</th>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  <div style="text-align: center;">
                    <?php set_page_numbers(); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="footer">
        <?php include("_footer.php"); ?>
      </footer>
    </div>
  </div>

  <div class="rightbar-overlay"></div>
  <?php include("_script.php"); ?>
</body>
</html>
<?php include("_additional_elements.php"); ?>
