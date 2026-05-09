<?php

  include("../lib/_configuration.php");
  include_once("_attachment_preview.php");

  function transfer_is_accessible_to_user($transfer_id){
  	global $db, $accessed_provinces, $holu_users_id;

  	$transfer_access_sq = $db->prepare(
  		"SELECT id
  		FROM `transfers`
  		WHERE deleted='0'
  		AND id=:transfer_id
  		AND (
  			from_province IN ($accessed_provinces)
  			OR to_province IN ($accessed_provinces)
  			OR users_id=:holu_users_id
  		)
  		LIMIT 1"
  	);

  	$transfer_access_sq->execute([
  		'transfer_id'=>$transfer_id,
  		'holu_users_id'=>$holu_users_id
  	]);

  	return ($transfer_access_sq->rowCount()>0);
  }

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape(holu_decode($_POST['data_id']));
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

  			case "add_transfer_form":

        ?>

        <div class="modal-header">
	        <h4 class="modal-title"><i class="fa fa-plus"></i> Add Transfer</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal parsley-form" role="form" action="controller_transfer.php" method="POST" enctype="multipart/form-data">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_transfer"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="from_province">From Province</label>
              <div class="col-sm-6">
                <select id="from_province" name="from_province" class="form-control" required onchange="get_branch_option(this.value, '0', 'from_branch', true);">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_all_province_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="from_branch">From Branch</label>
              <div class="col-sm-6">
                <select id="from_branch" name="from_branch" class="form-control" required>
                  <option selected hidden value="">Select an option</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="to_province">To Province</label>
              <div class="col-sm-6">
                <select id="to_province" name="to_province" class="form-control" required onchange="get_branch_option(this.value, '0', 'to_branch', true);">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_all_province_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="to_branch">To Branch</label>
              <div class="col-sm-6">
                <select id="to_branch" name="to_branch" class="form-control" required>
                  <option selected hidden value="">Select an option</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="transfer_date">Transfer Date</label>
              <div class="col-sm-6">
                <input type="text" id="transfer_date" name="transfer_date" class="form-control date_picker" placeholder="Pick a date..." required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="transfer_amount">Transfer Amount</label>
              <div class="col-sm-6">
                <input type="number" step="0.01" id="transfer_amount" name="transfer_amount" class="form-control" placeholder="Type here..." required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="currency">Currency</label>
              <div class="col-sm-6">
                <select id="currency" name="currency" class="form-control" required>
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_currency_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="transfer_attachment">Attachment</label>
              <div class="col-sm-6">
                <input type="file" id="transfer_attachment" name="transfer_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="description">Description</label>
              <div class="col-sm-6">
                <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl" ></textarea>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="submit"></label>
              <div class="col-sm-6">
                <button type="submit" id="submit" name="submit" class="btn success_btn waves-effect waves-light mr-1"><i class="fa fa-save"></i> Register</button>
                <button type="reset" class="btn btn-secondary waves-effect waves-light"><i class="fa fa-eraser"></i> Reset</button>
              </div>
            </div>

	        </form>
        </div>
		<script type="text/javascript">
			function update_transfer_to_branch_restriction(){
				var fromProvince = $('#from_province').val();
				var fromBranch = $('#from_branch').val();
				var toProvince = $('#to_province').val();
				var $toBranch = $('#to_branch');

				if(!$toBranch.length){
					return;
				}

				$toBranch.find('option').prop('disabled', false);

				if(fromProvince!=='' && toProvince!=='' && fromBranch!=='' && fromProvince===toProvince){
					$toBranch.find('option').filter(function(){
						return $(this).val()===fromBranch;
					}).prop('disabled', true);

					if($toBranch.val()===fromBranch){
						$toBranch.val('');
					}
				}
			}

			$(document)
				.off('change.transferRestriction', '#from_province, #from_branch, #to_province')
				.on('change.transferRestriction', '#from_province, #from_branch, #to_province', function(){
					setTimeout(update_transfer_to_branch_restriction, 150);
				});

			$(document)
				.off('change.transferRestrictionTarget', '#to_branch')
				.on('change.transferRestrictionTarget', '#to_branch', update_transfer_to_branch_restriction);

			setTimeout(update_transfer_to_branch_restriction, 150);
		</script>
        <?php
	      	
	      break;

	      case "edit_transfer_form":

	      	if(!transfer_is_accessible_to_user($data_id)){
	      		?>
	      		<div class="modal-header">
			        <h4 class="modal-title">Not Allowed</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <h3 class="text-center">You are not allowed to view this transfer.</h3>
			      </div>
	      		<?php
	      		break;
	      	}

		    	$transfer_sq = $db->prepare(
		    		"SELECT * 
		    		FROM `transfers` 
		    		WHERE deleted='0' 
		    		AND id=:data_id 
		    		LIMIT 1"
		    	);

		    	$transfer_sqx = $transfer_sq->execute([
		    		'data_id'=>$data_id
		    	]);

		    	if($transfer_sq->rowCount()>0){
		    		$transfer_row = $transfer_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Transfer</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_transfer.php" method="POST" enctype="multipart/form-data">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_transfer"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

		        	<div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="from_province">From Province</label>
		              <div class="col-sm-6">
		                <select id="from_province" name="from_province" class="form-control" required onchange="get_branch_option(this.value, '<?php echo $transfer_row['from_branch']; ?>', 'from_branch', true);">
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_all_province_option($transfer_row['from_province']); ?>
		                </select>
		              </div>
		            </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="from_branch">From Branch</label>
                  <div class="col-sm-6">
                    <select id="from_branch" name="from_branch" class="form-control" required>
                      <?php echo get_branch_option($transfer_row['from_province'], $transfer_row['from_branch'], true); ?>
                    </select>
                  </div>
                </div>

			        	<div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="to_province">To Province</label>
		              <div class="col-sm-6">
		                <select id="to_province" name="to_province" class="form-control" required onchange="get_branch_option(this.value, '<?php echo $transfer_row['to_branch']; ?>', 'to_branch', true);">
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_all_province_option($transfer_row['to_province']); ?>
		                </select>
		              </div>
		            </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="to_branch">To Branch</label>
                  <div class="col-sm-6">
                    <select id="to_branch" name="to_branch" class="form-control" required>
                      <?php echo get_branch_option($transfer_row['to_province'], $transfer_row['to_branch'], true); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="transfer_date">Transfer Date</label>
                  <div class="col-sm-6">
                    <input type="text" id="transfer_date" name="transfer_date" class="form-control date_picker" placeholder="Pick a date..." required value="<?php echo $transfer_row['transfer_date'];?>">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="transfer_amount">Transfer Amount</label>
                  <div class="col-sm-6">
                    <input type="number" step="0.01" id="transfer_amount" name="transfer_amount" class="form-control" placeholder="Type here..." required value="<?php echo $transfer_row['transfer_amount'];?>">
                  </div>
                </div>
                

                <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="currency">Currency</label>
		              <div class="col-sm-6">
		                <select id="currency" name="currency" class="form-control" required>
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_currency_option($transfer_row['currency']); ?>
		                </select>
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="transfer_attachment">Attachment</label>
		              <div class="col-sm-6">
		                <input type="file" id="transfer_attachment" name="transfer_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
		              </div>
		            </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Description</label>
			            <div class="col-sm-6">
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl"><?php echo $transfer_row['description']; ?></textarea>
			            </div>
			          </div>
			          

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="submit"></label>
			            <div class="col-sm-6">
			              <button type="submit" id="submit" name="submit" class="btn success_btn waves-effect waves-light mr-1"><i class="fa fa-save"></i> Register</button>
                		<button type="reset" class="btn btn-secondary waves-effect waves-light"><i class="fa fa-eraser"></i> Reset</button>
			            </div>
			          </div>

			        </form>
		        </div>
		        <script type="text/javascript">
		        	function update_transfer_to_branch_restriction(){
		        		var fromProvince = $('#from_province').val();
		        		var fromBranch = $('#from_branch').val();
		        		var toProvince = $('#to_province').val();
		        		var $toBranch = $('#to_branch');

		        		if(!$toBranch.length){
		        			return;
		        		}

		        		$toBranch.find('option').prop('disabled', false);

		        		if(fromProvince!=='' && toProvince!=='' && fromBranch!=='' && fromProvince===toProvince){
		        			$toBranch.find('option').filter(function(){
		        				return $(this).val()===fromBranch;
		        			}).prop('disabled', true);

		        			if($toBranch.val()===fromBranch){
		        				$toBranch.val('');
		        			}
		        		}
		        	}

		        	$(document)
		        		.off('change.transferRestriction', '#from_province, #from_branch, #to_province')
		        		.on('change.transferRestriction', '#from_province, #from_branch, #to_province', function(){
		        			setTimeout(update_transfer_to_branch_restriction, 150);
		        		});

		        	$(document)
		        		.off('change.transferRestrictionTarget', '#to_branch')
		        		.on('change.transferRestrictionTarget', '#to_branch', update_transfer_to_branch_restriction);

		        	setTimeout(update_transfer_to_branch_restriction, 150);
		        </script>
		        <?php
	      		}
	      break;

	      case "delete_transfer_form":

	      	if(!transfer_is_accessible_to_user($data_id)){
	      		?>
	      		<div class="modal-header">
			        <h4 class="modal-title">Not Allowed</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <h3 class="text-center">You are not allowed to view this transfer.</h3>
			      </div>
	      		<?php
	      		break;
	      	}

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Transfer</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_transfer.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_transfer"/>

		        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

		        	<div class="form-group row">
		            <label class="col-sm-1 col-form-label" for="submit"></label>
		            <div class="col-sm-11">
		              <h3>This item will be permanently deleted. Do you continue?</h3>
		            </div>
		          </div>
		          

		          <div class="form-group row">
		            <label class="col-sm-1 col-form-label" for="submit"></label>
		            <div class="col-sm-10">
		              <button type="submit" id="submit" name="submit" class="btn btn-success waves-effect waves-light mr-1"><i class="fa fa-check-circle"></i> Yes</button>
                  <button type="reset" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal"><i class="fa fa-times-circle"></i> No</button>
		            </div>
		          </div>

		        </form>
	        </div>
	        <?php
	      	
	      break;

	      case "approve_transfer_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title">Approve Transfer</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_transfer.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="approve_transfer"/>

		        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

		        	<div class="form-group row">
		            <label class="col-sm-3 col-form-label" for="approve_description">Approve Description</label>
		            <div class="col-sm-6">
		              <textarea id="approve_description" name="approve_description" class="form-control" placeholder="Type here..." dir="rtl"></textarea>
		            </div>
		          </div>

		          <div class="form-group row">
		            <label class="col-sm-3 col-form-label" for="submit"></label>
		            <div class="col-sm-6">
		              <button type="submit" id="submit" name="submit" class="btn success_btn waves-effect waves-light mr-1">Register</button>
		              <button type="reset" class="btn btn-secondary waves-effect waves-light">Reset</button>
		            </div>
		          </div>

		        </form>
	        </div>
	        <?php
	      	
	      break;

	      case "filter_table":
		    	
				?>
				<div class="modal-header">
					<h4 class="modal-title" id="add_transferTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form" action="list_transfer.php" method="GET">

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="province">Province</label>
						<div class="col-sm-6">
						<select id="province" name="province" class="form-control" data-branch-target="branch" data-branch-value="0" onchange="get_branch_option(this.value, this.getAttribute('data-branch-value') || '0', this.getAttribute('data-branch-target') || 'branch', true); this.setAttribute('data-branch-value', '0');">
							<option selected hidden value="">Select an option</option>
							<?php echo get_all_province_option('0'); ?>
						</select>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="branch">Branch</label>
						<div class="col-sm-6">
							<select id="branch" name="branch" class="form-control">
								<?php echo get_branch_option('0', '', true); ?>
							</select>
						</div>
					</div>

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="transfer_date">Transfer Date</label>
						<div class="col-sm-3">
						<input type="text" id="from_transfer_date" name="from_transfer_date" class="form-control date_picker" placeholder="From">
						</div>
						<div class="col-sm-3">
						<input type="text" id="to_transfer_date" name="to_transfer_date" class="form-control date_picker" placeholder="To">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="submit"></label>
						<div class="col-sm-6">
						<button type="submit" id="submit" name="submit" class="btn success_btn waves-effect waves-light mr-1"><i class="fa fa-filter"></i> Filter</button>
						<button type="reset" class="btn btn-secondary waves-effect waves-light"><i class="fas fa-window-close"></i> Reset</button>
						</div>
					</div>

					</form>
				</div>
				<?php
	      break;

	      case "filter_table_report_transfer":

		    	
				?>
				<div class="modal-header">
					<h4 class="modal-title" id="add_transferTitle"><i class="fas fa-filter"></i> Filter the Table</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form" action="report_transfer.php" method="GET">

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="province">Province</label>
						<div class="col-sm-6">
						<select id="province" name="province" class="form-control" data-branch-target="branch" data-branch-value="0" onchange="get_branch_option(this.value, this.getAttribute('data-branch-value') || '0', this.getAttribute('data-branch-target') || 'branch', true); this.setAttribute('data-branch-value', '0');">
							<option selected hidden value="">Select an option</option>
							<?php echo get_all_province_option('0'); ?>
						</select>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="branch">Branch</label>
						<div class="col-sm-6">
							<select id="branch" name="branch" class="form-control">
								<?php echo get_branch_option('0', '', true); ?>
							</select>
						</div>
					</div>

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="transfer_date">Transfer Date</label>
						<div class="col-sm-3">
						<input type="text" id="from_transfer_date" name="from_transfer_date" class="form-control date_picker" placeholder="From">
						</div>
						<div class="col-sm-3">
						<input type="text" id="to_transfer_date" name="to_transfer_date" class="form-control date_picker" placeholder="To">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="submit"></label>
						<div class="col-sm-6">
						<button type="submit" id="submit" name="submit" class="btn success_btn waves-effect waves-light mr-1"><i class="fa fa-filter"></i> Filter</button>
						<button type="reset" class="btn btn-secondary waves-effect waves-light"><i class="fas fa-window-close"></i> Reset</button>
						</div>
					</div>

					</form>
				</div>
				<?php
	      break;

		  /*  Begin Case __ View Attachment __ Added By Mohsen __ 2021-04-04 */
	      case "view_attachment":

				if(!transfer_is_accessible_to_user($data_id)){
					?>
					<div class="modal-header">
						<h4 class="modal-title">Not Allowed</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<h3 class="text-center">You are not allowed to view this transfer.</h3>
					</div>
					<?php
					break;
				}

				$transfer_attachment_sq = $db->prepare(
					"SELECT *
					FROM `attachments`
					WHERE deleted='0'
					AND type='Transfer Attachment'
					AND reference_id=:data_id"
				);
	  
				$transfer_attachment_sqx = $transfer_attachment_sq->execute([
					'data_id'=>$data_id
				]);
	  
					  
			  ?>
				<div class="modal-header">
				  <h4 class="modal-title" id="add_transferTitle"><i class="far fa-file-image"></i> View Attachment</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<div class="modal-body">
				  <div class="row filterable-content">
	  
					  <?php
	  
					  if($transfer_attachment_sq->rowCount()>0){
						  while($transfer_attachment_row = $transfer_attachment_sq->fetch()){
								holu_render_attachment_preview($transfer_attachment_row['path']);
						  }
					  }else{
						  holu_render_no_attachment();
					  }
	  
					  ?>
				  </div>
				</div>
			  <?php
	      break;
		  /*  End Case */

	      case "view_full_info":

	      	
	      	$old_data_rows = [];
	      	$new_data_rows = [];

	      	$transaction_edition_sq = $db->prepare("SELECT old_data, new_data FROM `transaction_editions` WHERE reference_type='Transfer' AND reference_id=:data_id ORDER BY id DESC LIMIT 1");
	      	$transaction_edition_sq->execute([
	      		'data_id'=>$data_id
	      	]);

	      	if($transaction_edition_sq->rowCount()>0){
	      		$transaction_edition_row = $transaction_edition_sq->fetch();
	      		foreach(explode('###', $transaction_edition_row['old_data']) as $old_data_item){
	      			$old_data_item_array = explode('=>', $old_data_item);
	      			if(sizeof($old_data_item_array)>1){
	      				$old_data_rows[] = ['key'=>str_replace('`', '', $old_data_item_array[0]), 'value'=>str_replace('`', '', $old_data_item_array[1])];
	      			}
	      		}
	      		foreach(explode('###', $transaction_edition_row['new_data']) as $new_data_item){
	      			$new_data_item_array = explode('=>', $new_data_item);
	      			if(sizeof($new_data_item_array)>1){
	      				$new_data_rows[] = ['key'=>str_replace('`', '', $new_data_item_array[0]), 'value'=>str_replace('`', '', $new_data_item_array[1])];
	      			}
	      		}
	      	}

	        ?>

	        

	        <div class="modal-header">
		        <h4 class="modal-title">View Full Info</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>

		      <div class="modal-body">

	        	<ul class="nav nav-pills navtab-bg nav-justified">
              <li class="nav-item">
                <a href="#old_data" data-toggle="tab" aria-expanded="true" class="nav-link active">
                  <span class="d-inline-block d-sm-none"><i class="fas fa-home"></i></span>
                  <span class="d-none d-sm-inline-block">Old Data</span>   
                </a>
              </li>
              <li class="nav-item">
                <a href="#new_data" data-toggle="tab" aria-expanded="false" class="nav-link">
                  <span class="d-inline-block d-sm-none"><i class="fas fa-home"></i></span>
                  <span class="d-none d-sm-inline-block">New Data</span>   
                </a>
              </li>
              <li class="nav-item">
                <a href="#history" data-toggle="tab" aria-expanded="false" class="nav-link">
                  <span class="d-inline-block d-sm-none"><i class="far fa-user"></i></span>
                  <span class="d-none d-sm-inline-block">History</span> 
                </a>
              </li>
            </ul>

            <div class="tab-content">

              <div class="tab-pane fade show active" id="old_data">
			        	<div class="item form-group"  >
				          <label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
				          <div class="col-md-12 col-sm-12 col-xs-12">
				          	
				            <div class="table-responsive">
		                  <table class="table table-bordered table-sm mb-0">
		                    <thead>
		                      <tr>
		                        <th colspan="2" class="text-center">Old Data</th>
		                      </tr>
		                    </thead>
		                    <tbody>
		                    	<?php
													if(sizeof($old_data_rows)>0){
														foreach($old_data_rows as $old_data_row){
													?>
													<tr><th><?php echo $old_data_row['key']; ?></th><td><?php echo $old_data_row['value']; ?></td></tr>
													<?php
														}
													}else{
													?>
													<tr><th colspan="2" class="text-center">No old data found</th></tr>
													<?php
													}
							            ?>
		                    </tbody>
		                  </table>
		                </div> <!-- end table-responsive-->
				              	
				          </div>
				        </div>
				      </div>
				      <div class="tab-pane fade show" id="new_data">
			        	<div class="item form-group"  >
				          <label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
				          <div class="col-md-12 col-sm-12 col-xs-12">
				          	<div class="table-responsive">
		                  <table class="table table-bordered table-sm mb-0">
		                    <thead><tr><th colspan="2" class="text-center">New Data</th></tr></thead>
		                    <tbody>
		                    <?php if(sizeof($new_data_rows)>0){ foreach($new_data_rows as $new_data_row){ ?>
		                    	<tr><th><?php echo $new_data_row['key']; ?></th><td><?php echo $new_data_row['value']; ?></td></tr>
		                    <?php }}else{ ?>
		                    	<tr><th colspan="2" class="text-center">No new data found</th></tr>
		                    <?php } ?>
		                    </tbody>
		                  </table>
		                </div>
				          </div>
				        </div>
				      </div>

				      

				      <div class="tab-pane fade show" id="history">
			        	<div class="item form-group"  >
				          <label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
				          <div class="col-md-12 col-sm-12 col-xs-12">
				          	<ul class="list-unstyled timeline-sm">
				          	<?php
				          	$operation_sq = $db->prepare(
				          		"SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Insertion' AS operation_type
				          		FROM `transfers`
				          		WHERE id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Edition' AS operation_type
				          		FROM `transaction_editions`
				          		WHERE reference_type='Transfer'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Deletion' AS operation_type
				          		FROM `transaction_deletions`
				          		WHERE reference_type='Transfer'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		markup_type AS operation_type
				          		FROM `markups`
				          		WHERE reference_type='Transfer'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Print' AS operation_type
				          		FROM `invoices`
				          		WHERE reference_type='Transfer'
				          		AND reference_id='$data_id'
				          		ORDER BY operation_date ASC, operation_time ASC
				          		"
				          	);

				          	$operation_sqx = $operation_sq->execute();

				          	if($operation_sq->rowCount()>0){
				          		while($operation_row = $operation_sq->fetch()){
				          			?>
				          			<li class="timeline-sm-item">
							            <span class="timeline-sm-date">
								            <?php echo $operation_row['operation_date']; ?><br/>
								            <?php echo $operation_row['operation_time']; ?>
							            </span>
							            <h5 class="mt-0 mb-1"><?php echo $operation_row['operation_type'].' by '.get_col('users', 'username', 'id', $operation_row['operation_users_id']); ?></h5>
							            <p></p>
							          </li>
				          			<?php
				          		}
				          	}

				          	?>
							      </ul>
				            
				          </div>
				        </div>
				      </div>

				    </div>
	        </div>

        	<?php
	      	
	      break;

		    default:
	        ?>

	        <div class="modal-header">
			      <h4 class="modal-title">Not Found</h4>
			      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			        <span aria-hidden="true">&times;</span>
			      </button>
			    </div>
			    <div class="modal-body">
			      <h3 class="text-center">Sorry! we couldn't find this operation.</h3>
			    </div>
	        	
	      <?php
	      break;
			}
  	}else if($flag_request=="operation"){
  		$flag_operation = holu_escape($_POST['flag_operation']);

  		switch ($flag_operation){

  			case "add_transfer":

	  			$from_province = holu_escape($_POST['from_province']);
	  			$from_branch = holu_escape($_POST['from_branch']);
	  			$to_province = holu_escape($_POST['to_province']);
	  			$to_branch = holu_escape($_POST['to_branch']);
			    $transfer_date = holu_escape($_POST['transfer_date']);
	  			$transfer_amount = holu_escape($_POST['transfer_amount']);
			    $currency = holu_escape($_POST['currency']);
			    $description = holu_escape($_POST['description']);

				$num_transfers_sq = $db->prepare(
			    	"SELECT 
						count(id) AS num_transfers
						FROM `transfers`
						WHERE from_province=:from_province AND from_branch=:from_branch
					LIMIT 1"
			    );

			    $num_transfers_sqx = $num_transfers_sq->execute([
			    	'from_province'=>$from_province,
			    	'from_branch'=>$from_branch
			    ]);

			    $num_transfers_row = $num_transfers_sq->fetch();
			    $num_transfers = $num_transfers_row['num_transfers'];
			    $check_number = generate_check_number('transfer', $from_province, $from_branch, $num_transfers+1);


			    $transfer_iq = $db->prepare("INSERT INTO `transfers` (
			    	from_province, 
			    	from_branch,
			    	to_province, 
			    	to_branch,
			    	transfer_date, 
			    	transfer_amount, 
			    	currency, 
			    	description, 
			    	insertion_date, 
			    	insertion_time, 
			    	users_id,
					  check_number
			    ) VALUES (
				    :from_province, 
				    :from_branch,
				    :to_province, 
				    :to_branch,
				    :transfer_date, 
				    :transfer_amount, 
				    :currency, 
				    :description, 
				    :holu_date, 
				    :holu_time,
				    :holu_users_id,
					  :check_number
				  )");

			    $transfer_iqx = $transfer_iq->execute([
			    	'from_province'=>$from_province,
			    	'from_branch'=>$from_branch,
			    	'to_province'=>$to_province,
			    	'to_branch'=>$to_branch,
			    	'transfer_date'=>$transfer_date,
			    	'transfer_amount'=>$transfer_amount,
			    	'currency'=>$currency,
			    	'description'=>$description,
			    	'holu_date'=>$holu_date,
			    	'holu_time'=>$holu_time,
			    	'holu_users_id'=>$holu_users_id,
					'check_number'=>$check_number
			    ]);

			    $transfers_id = $db->lastInsertId();

			    if(!empty(array_filter($_FILES['transfer_attachment']['name']))){
						foreach ($_FILES['transfer_attachment']['name'] as $key => $value) {
					    if($_FILES['transfer_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/transfer_attachment/".holu_encode($transfers_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['transfer_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['transfer_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Transfer Attachment',
									'reference_id'=>$transfers_id,
									'path'=>$target_file,
									'insertion_date'=>$holu_date,
									'insertion_time'=>$holu_time,
									'users_id'=>$holu_users_id
								]);
					    }
					  }
					}

			    if($transfer_iqx){
			      header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
			      exit();
			    }else{
			      header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
			      exit();
			    }

  			break;

  			case "edit_transfer":

  			

	  			track_editions('edit_transfer', ['transfers_id'=>$_POST['data_id'], 'data_array'=>$_POST]);

	  			$data_id = holu_escape(holu_decode($_POST['data_id']));
	  			if(!transfer_is_accessible_to_user($data_id)){
	  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
	  				exit();
	  			}
			    $from_province = holu_escape($_POST['from_province']);
			    $from_branch = holu_escape($_POST['from_branch']);
			    $to_province = holu_escape($_POST['to_province']);
			    $to_branch = holu_escape($_POST['to_branch']);
			    $transfer_date = holu_escape($_POST['transfer_date']);
	  			$transfer_amount = holu_escape($_POST['transfer_amount']);
			    $currency = holu_escape($_POST['currency']);
			    $description = holu_escape($_POST['description']);

	  			$transfer_uq = $db->prepare("UPDATE `transfers` SET 
	  				from_province=:from_province, 
	  				from_branch=:from_branch,
	  				to_province=:to_province, 
	  				to_branch=:to_branch,
	  				transfer_date=:transfer_date, 
	  				transfer_amount=:transfer_amount, 
	  				currency=:currency, 
	  				description=:description,
	  				is_approved='0',
	  				approve_date='',
	  				approve_time='',
	  				approve_description='',
	  				approved_by='0'
	  			WHERE id=:data_id 
	  			LIMIT 1");

	  			$transfer_uqx = $transfer_uq->execute([
			    	'from_province'=>$from_province,
			    	'from_branch'=>$from_branch,
			    	'to_province'=>$to_province,
			    	'to_branch'=>$to_branch,
			    	'transfer_date'=>$transfer_date,
			    	'transfer_amount'=>$transfer_amount,
			    	'currency'=>$currency,
			    	'description'=>$description,
			    	'data_id'=>$data_id
	  			]);

	  			if(!empty(array_filter($_FILES['transfer_attachment']['name']))){
	  				$expenses_id = $data_id;
						foreach ($_FILES['transfer_attachment']['name'] as $key => $value) {
					    if($_FILES['transfer_attachment']['size'][$key]<10485760){
					    	
							    $target_dir = "../uploads/transfer_attachment/".holu_encode($data_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['transfer_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['transfer_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Transfer Attachment',
									'reference_id'=>$data_id,
									'path'=>$target_file,
									'insertion_date'=>$holu_date,
									'insertion_time'=>$holu_time,
									'users_id'=>$holu_users_id
								]);
					    }
					  }
					}

	  			if($transfer_uqx){
	  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
	  				exit();
	  			}else{
	  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
	  				exit();
	  			}

	  		

  			break;

  			case "approve_transfer":

  			$data_id = holu_escape(holu_decode($_POST['data_id']));
  			$approve_access_sq = $db->prepare("SELECT id FROM `transfers` WHERE deleted='0' AND id=:data_id AND to_province IN ($accessed_provinces) LIMIT 1");
  			$approve_access_sq->execute(['data_id'=>$data_id]);
  			if($approve_access_sq->rowCount()==0){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}
  			$approve_description = holu_escape($_POST['approve_description']);

  			$transfer_uq = $db->query("UPDATE `transfers` SET is_approved='1', approve_date='$holu_date', approve_time='$holu_time', approve_description='$approve_description', approved_by='$holu_users_id' WHERE id='$data_id' LIMIT 1");

  			if($transfer_uq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}


  			break;

  			case "delete_transfer":

  			track_deletions('delete_transfer', ['transfers_id'=>$_POST['data_id']]);

  			$data_id = holu_escape(holu_decode($_POST['data_id']));
  			if(!transfer_is_accessible_to_user($data_id)){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}

  			$transfer_dq = $db->prepare("UPDATE `transfers` SET 
  				deleted='1' 
  			WHERE id=:data_id 
  			LIMIT 1");

  			$transfer_dqx = $transfer_dq->execute([
  				'data_id'=>$data_id
  			]);

  			if($transfer_dqx){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}

  			break;

  			default:

  			break;

  		}
  	}
  }
?>
