<div class="modal fade " id="general_lg" tabindex="-1" role="dialog" aria-labelledby="add_userTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>

<div class="modal fade " id="general_md" tabindex="-1" role="dialog" aria-labelledby="add_userTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>

<?php

if(isset($_GET['success'])){
	?>
	<script type="text/javascript">
	  bootoast.toast({
	    message: 'Operation was successfully completed.',
	    type: 'success'
	  });
	</script>
	<?php
}else if(isset($_GET['error'])){
	?>
	<script type="text/javascript">
	  bootoast.toast({
	    message: 'Operation couldn\'t be completed.',
	    type: 'danger'
	  });
	</script>
	<?php
}else if(isset($_GET['duplicated'])){
	?>
	<script type="text/javascript">
	  bootoast.toast({
	    message: 'You have entered duplicated information.',
	    type: 'warning'
	  });
	</script>
	<?php
}

?>