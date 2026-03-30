<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>
<script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
<script src="assets/libs/peity/jquery.peity.min.js"></script>

<!-- Sparkline charts -->
<script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>

<!-- init js -->
<script src="assets/js/pages/dashboard-1.init.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>

<!-- Toast Notification -->
<script src="assets/additional/bootoast/bootoast.min.js"></script>

<!-- date_picker -->
<script src="assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>

<!-- chart_js -->
<script src="assets/additional/Chartjs/Chart.min.js"></script>
<script src="assets/additional/Chartjs/samples/utils.js"></script>

<!-- js_pdf -->
<script src="assets/additional/jspdf/jspdf.debug.js"></script>

<!-- tree_js -->
<script src="assets/additional/tree_js/tree.min.js"></script>

<!-- Tree view js -->
<script src="assets/libs/treeview/jstree.min.js"></script>
<script src="assets/js/pages/treeview.init.js"></script>

<!-- Magnific Popup-->
<script src="assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>

<!-- select2-->
<script src="assets/libs/select2/select2.min.js"></script>

<!-- Gallery Init-->
<script src="assets/js/pages/gallery.init.js"></script>

<!-- Additional js -->
<script src="assets/js/custom.js"></script>
<script>
  if (typeof window.get_branch_option !== 'function') {
    window.get_branch_option = function(province, branch, target_id){
      $.ajax({
        url: 'controller_ajax.php',
        method: 'post',
        data: {
          operation: 'get_branch_option',
          province: province,
          branch: branch
        },
        success: function(result){
          $("#" + target_id).html(result);
        }
      });
    };
  }
</script>
