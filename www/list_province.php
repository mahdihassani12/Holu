<?php
  include("../lib/_configuration.php");
  $holu_page_paths = ["Management", "List of Provinces"];

  set_pagination();

  $province_sq = $db->query("SELECT * FROM `provinces` ORDER BY id DESC limit $holu_to OFFSET $holu_from");

  $Pagenation = $db->query("SELECT count(id) as record FROM `provinces`");
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
                <h4 class="header-title"><i class="fa fa-list"></i> List of Provinces</h4>

                <?php if(check_access("system_accessibility/management/list_province/add_province")==1){ ?>
                <button type="button" class="btn waves-effect waves-light adder_button" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_province.php', 'add_province_form', 'general_lg', '0');"><i class="fa fa-plus"></i> Add New</button>
                <?php } ?>
              </div>

              <div class="card-box">
                <div class="table-responsive slimscroll">
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Province Name</th>
                        <th>Abbreviation</th>
                        <th>Created By</th>
                        <th class="text-center">Operation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if($province_sq->rowCount()>0){
                        while($province_row = $province_sq->fetch()){
                      ?>
                      <tr>
                        <th class="text-center"><?php echo $holu_count++; ?></th>
                        <td><?php echo $province_row['name']; ?></td>
                        <td><?php echo htmlspecialchars($province_row['abbreviation'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo isset($province_row['users_id']) ? get_col('users', 'username', 'id', $province_row['users_id']) : '-'; ?></td>
                        <td class="text-center">
                          <div class="dropdown mt-1 opertation_container">
                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light operation_button" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></button>
                            <div class="dropdown-content dropdown-menu-right operation_list">
                              <?php if(check_access("system_accessibility/management/list_province/edit_province")==1){ ?>
                              <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_province.php', 'edit_province_form', 'general_lg', '<?php echo $province_row['id']; ?>');"><i class="fas fa-edit"></i> Edit Province</a>
                              <?php } ?>

                              <?php if(check_access("system_accessibility/management/list_province/delete_province")==1){ ?>
                              <a class="dropdown-item" onclick="load_modal('<?php echo $_SERVER['PHP_SELF']; ?>', 'controller_province.php', 'delete_province_form', 'general_md', '<?php echo $province_row['id']; ?>');"><i class="fas fa-trash"></i> Delete Province</a>
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
