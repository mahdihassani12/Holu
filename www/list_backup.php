<?php
include("../lib/_configuration.php");
$holu_page_paths = ["Management", "Backups"];

$backup_access_path = "system_accessibility/management/list_backup/";
$download_access_path = "system_accessibility/management/list_backup/download_backup";
$delete_access_path = "system_accessibility/management/list_backup/delete_backup";

if (check_access($backup_access_path) != 1) {
  header("location:home.php");
  exit;
}

function format_backup_size($bytes)
{
  $size = is_numeric($bytes) ? (float)$bytes : 0;
  if ($size < 0) {
    $size = 0;
  }

  $units = ['B', 'KB', 'MB', 'GB'];
  $unit_index = 0;

  while ($size >= 1024 && $unit_index < count($units) - 1) {
    $size /= 1024;
    $unit_index++;
  }

  $formatted_size = number_format($size, 2, '.', '');
  $formatted_size = rtrim(rtrim($formatted_size, '0'), '.');

  return $formatted_size . ' ' . $units[$unit_index];
}

$backup_directory = realpath(__DIR__ . "/../backups");
if ($backup_directory === false || !is_dir($backup_directory)) {
  $backup_directory = null;
}

if (isset($_GET['action']) && $_GET['action'] === 'download') {
  if (check_access($download_access_path) != 1) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied");
  }

  $backup_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($backup_id <= 0) {
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid backup id");
  }

  $backup_sq = $db->prepare("SELECT id, filename FROM `backups` WHERE id=:id LIMIT 1");
  $backup_sq->execute(['id' => $backup_id]);
  if ($backup_sq->rowCount() == 0) {
    header("HTTP/1.1 404 Not Found");
    exit("Backup not found");
  }

  $backup_row = $backup_sq->fetch();
  $safe_filename = basename((string)$backup_row['filename']);
  if ($safe_filename === '' || $safe_filename !== $backup_row['filename']) {
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid file name");
  }

  if ($backup_directory === null) {
    header("HTTP/1.1 500 Internal Server Error");
    exit("Backups folder is not available");
  }

  $file_path = $backup_directory . DIRECTORY_SEPARATOR . $safe_filename;
  $real_file_path = realpath($file_path);
  if ($real_file_path === false || strpos($real_file_path, $backup_directory . DIRECTORY_SEPARATOR) !== 0 || !is_file($real_file_path) || !is_readable($real_file_path)) {
    header("HTTP/1.1 404 Not Found");
    exit("File does not exist");
  }

  while (ob_get_level()) {
    ob_end_clean();
  }

  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('X-Content-Type-Options: nosniff');
  header('Content-Disposition: attachment; filename="' . addcslashes($safe_filename, "\"\\") . '"; filename*=UTF-8\'\'' . rawurlencode($safe_filename));
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($real_file_path));
  readfile($real_file_path);
  exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'delete_backup') {
  if (check_access($delete_access_path) != 1) {
    $_SESSION['holu_error'] = "You do not have permission to delete backups.";
    header("location:list_backup.php");
    exit;
  }

  $backup_id = isset($_POST['backup_id']) ? (int)$_POST['backup_id'] : 0;
  if ($backup_id <= 0 || $backup_directory === null) {
    header("location:list_backup.php?error=1");
    exit;
  }

  if ($backup_id > 0) {
    $backup_sq = $db->prepare("SELECT id, filename FROM `backups` WHERE id=:id LIMIT 1");
    $backup_sq->execute(['id' => $backup_id]);

    if ($backup_sq->rowCount() > 0) {
      $backup_row = $backup_sq->fetch();
      $safe_filename = basename((string)$backup_row['filename']);

      if ($safe_filename !== '' && $safe_filename === $backup_row['filename']) {
        $file_path = $backup_directory . DIRECTORY_SEPARATOR . $safe_filename;
        $real_file_path = realpath($file_path);

        if ($real_file_path !== false && strpos($real_file_path, $backup_directory . DIRECTORY_SEPARATOR) === 0 && is_file($real_file_path)) {
          @unlink($real_file_path);
        }
      }

      $delete_sq = $db->prepare("DELETE FROM `backups` WHERE id=:id LIMIT 1");
      $delete_sq->execute(['id' => $backup_id]);
      if ($delete_sq->rowCount() > 0) {
        header("location:list_backup.php?success=1");
        exit;
      }
    }
  }

  header("location:list_backup.php?error=1");
  exit;
}

set_pagination();
$backup_sq = $db->query("SELECT id, type, filename, size, status, created_at FROM `backups` ORDER BY id DESC limit $holu_to OFFSET $holu_from");
$Pagenation = $db->query("SELECT count(id) as record FROM `backups`");
extract($Pagenation->fetch());

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("_head.php"); ?>
</head>
<body class="left-side-menu-dark">
<div id="wrapper">
  <div class="navbar-custom"><?php include("_navbar.php"); ?></div>
  <div class="left-side-menu"><?php include("_sidebar.php"); ?></div>
  <div class="content-page">
    <div class="content">
      <div class="container-fluid">
        <div class="row"><?php include("_page_title.php"); ?></div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card-box card-box-header">
              <h4 class="header-title"><i class="fa fa-database"></i> Backups</h4>
            </div>
            <div class="card-box">
              <div class="table-responsive slimscroll">
                <table class="table table-bordered table-sm mb-0">
                  <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th>Type</th>
                    <th>Filename</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th class="text-center">Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php if ($backup_sq->rowCount() > 0) { ?>
                    <?php while ($backup_row = $backup_sq->fetch()) { ?>
                      <tr>
                        <td class="text-center"><?php echo (int)$backup_row['id']; ?></td>
                        <td><?php echo holu_escape($backup_row['type']); ?></td>
                        <td><?php echo holu_escape($backup_row['filename']); ?></td>
                        <td><?php echo holu_escape(format_backup_size($backup_row['size'])); ?></td>
                        <td><?php echo holu_escape($backup_row['status']); ?></td>
                        <td><?php echo holu_escape($backup_row['created_at']); ?></td>
                        <td class="text-center">
                          <?php if (check_access($download_access_path) == 1) { ?>
                            <a class="btn btn-sm btn-primary" href="list_backup.php?action=download&id=<?php echo (int)$backup_row['id']; ?>"><i class="fa fa-download"></i> Download</a>
                          <?php } ?>
                          <?php if (check_access($delete_access_path) == 1) { ?>
                            <form method="post" action="list_backup.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this backup?');">
                              <input type="hidden" name="action" value="delete_backup">
                              <input type="hidden" name="backup_id" value="<?php echo (int)$backup_row['id']; ?>">
                              <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php } ?>
                  <?php } else { ?>
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
    <footer class="footer"><?php include("_footer.php"); ?></footer>
  </div>
</div>
<div class="rightbar-overlay"></div>
<?php include("_script.php"); ?>
</body>
</html>
<?php include("_additional_elements.php"); ?>
