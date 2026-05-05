<?php
include("../lib/_configuration.php");
$holu_page_paths = ["Dashboards", "Transactions"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("_head.php"); ?>
  <style>
    .dashboard-summary-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }
    .dashboard-summary-item {
      flex: 1 1 calc(16.666% - 12px);
      min-width: 210px;
    }
    .dashboard-summary-card {
      background-color: #2fa9c0;
      border-radius: 8px;
      color: #fff;
      padding: 12px 14px;
      min-height: 112px;
    }
    .dashboard-summary-title {
      font-weight: 700;
      font-size: 22px;
      line-height: 1.1;
      margin-bottom: 6px;
    }
    .dashboard-summary-lines {
      font-size: 15px;
      line-height: 1.4;
      font-weight: 500;
    }
  </style>
</head>
<body class="left-side-menu-dark">
  <div id="wrapper">
    <div class="navbar-custom"><?php include("_navbar.php"); ?></div>
    <div class="left-side-menu"><?php include("_sidebar.php"); ?></div>
    <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row"><?php include("_page_title.php"); ?></div>

          <div class="dashboard-summary-grid">
            <div class="dashboard-summary-item">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Income:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="dashboard-summary-item">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Expense:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="dashboard-summary-item">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Exchange:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN to 30,000 USD</div>
                <div class="dashboard-summary-lines">3000 USD to 0000 AFN</div>
                <div class="dashboard-summary-lines">90 IRT to 0999 AFN</div>
              </div>
            </div>
            <div class="dashboard-summary-item">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Transfer:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="dashboard-summary-item">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Total:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
            <div class="dashboard-summary-item">
              <div class="dashboard-summary-card">
                <div class="dashboard-summary-title">Total With Closing:</div>
                <div class="dashboard-summary-lines">35,000,000 AFN</div>
                <div class="dashboard-summary-lines">3000 USD</div>
                <div class="dashboard-summary-lines">90 IRT</div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <?php include("_footer.php"); ?>
    </div>
  </div>
  <script>
    (function () {
      var sideMenu = document.getElementById('side-menu');
      if (!sideMenu) { return; }
      var menuLinks = sideMenu.querySelectorAll('a');
      var currentPage = window.location.pathname.split('/').pop();
      if (!currentPage) { return; }
      var currentLink = null;
      for (var i = 0; i < menuLinks.length; i++) {
        var href = menuLinks[i].getAttribute('href');
        if (href === currentPage) {
          currentLink = menuLinks[i];
          break;
        }
      }
      var openItems = sideMenu.querySelectorAll('li.mm-active');
      for (var j = 0; j < openItems.length; j++) {
        openItems[j].classList.remove('mm-active');
      }
      var shownMenus = sideMenu.querySelectorAll('ul.mm-show');
      for (var k = 0; k < shownMenus.length; k++) {
        shownMenus[k].classList.remove('mm-show');
      }
      if (!currentLink) { return; }
      currentLink.classList.add('active');
      var parentItem = currentLink.closest('li');
      while (parentItem) {
        parentItem.classList.add('mm-active');
        var parentMenu = parentItem.parentElement;
        if (parentMenu && parentMenu.classList.contains('nav-second-level')) {
          parentMenu.classList.add('mm-show');
          parentItem = parentMenu.closest('li');
        } else {
          break;
        }
      }
    })();
  </script>
</body>
</html>
