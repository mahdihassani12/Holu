<?php

  include("../lib/_configuration.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape(holu_decode($_POST['data_id']));
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

  			case "add_purchase_form":

        ?>

        <div class="modal-header">
	        <h4 class="modal-title"><i class="fa fa-plus"></i> Add Purchase</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal parsley-form" role="form" action="controller_purchase.php" method="POST" enctype="multipart/form-data">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_purchase"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="logistic_cashes_id">Logistic Cash</label>
              <div class="col-sm-6">
                <select id="logistic_cashes_id" name="logistic_cashes_id" class="form-control" required>
                  <?php echo get_logistic_cash_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="province">Province</label>
              <div class="col-sm-6">
                <select id="province" name="province" class="form-control" required>
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_province_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
              <div class="col-sm-6">
                <select id="categories_id" name="categories_id" class="form-control" required onchange="get_sub_category_option('0', this.value, 'sub_categories_id');">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_category_option("Purchase", "0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
              <div class="col-sm-6">
                <select id="sub_categories_id" name="sub_categories_id" class="form-control" required>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="purchase_date">Purchase Date</label>
              <div class="col-sm-6">
                <input type="text" id="purchase_date" name="purchase_date" class="form-control date_picker" placeholder="Pick a date..." required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="purchase_amount">Amount</label>
              <div class="col-sm-6">
                <input type="number" step="0.01" id="purchase_amount" name="purchase_amount" class="form-control" placeholder="Type here..." required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="currency">Currency</label>
              <div class="col-sm-6">
                <select id="currency" name="currency" class="form-control" required onchange="specify_rate();">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_currency_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="purchase_attachment">Attachment</label>
              <div class="col-sm-6">
                <input type="file" id="purchase_attachment" name="purchase_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
              </div>
            </div>

            <div id="additional_information_input_containers">
              
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="description">Description</label>
              <div class="col-sm-6">
                <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl"></textarea>
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
        <?php
	      break;

	      case "edit_purchase_form":

		    	$purchase_sq = $db->prepare(
		    		"SELECT * 
		    		FROM `purchases` 
		    		WHERE deleted='0' 
		    		AND id=:data_id 
		    		LIMIT 1"
		    	);

		    	$purchase_sqx = $purchase_sq->execute([
		    		'data_id'=>$data_id
		    	]);

		    	if($purchase_sq->rowCount()>0){
		    		$purchase_row = $purchase_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Purchase</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_purchase.php" method="POST" enctype="multipart/form-data">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_purchase"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

			        	<input type="hidden" name="province" id="province" value="<?php echo $purchase_row['province']; ?>"/>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="logistic_cashes_id">Logistic Cash</label>
                  <div class="col-sm-6">
                    <select id="logistic_cashes_id" name="logistic_cashes_id" class="form-control" required onchange="get_sub_cat_conf();">
                      <?php echo get_logistic_cash_option($purchase_row['logistic_cashes_id']); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
		              <div class="col-sm-6">
		                <select id="categories_id" name="categories_id" class="form-control" required onchange="get_sub_category_option('0', this.value, 'sub_categories_id');">
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_category_option("Purchase", get_col('sub_categories', 'categories_id', 'id', $purchase_row['sub_categories_id'])); ?>
		                </select>
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
		              <div class="col-sm-6">
		                <select id="sub_categories_id" name="sub_categories_id" class="form-control" required onchange="get_sub_cat_conf();">
		                	<?php echo get_sub_category_option($purchase_row['sub_categories_id'], get_col('sub_categories', 'categories_id', 'id', $purchase_row['sub_categories_id'])); ?>
		                </select>
		              </div>
		            </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="purchase_date">Purchase Date</label>
                  <div class="col-sm-6">
                    <input type="text" id="purchase_date" name="purchase_date" class="form-control date_picker" placeholder="Pick a date..." required value="<?php echo $purchase_row['purchase_date'];?>">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="purchase_amount">Amount</label>
                  <div class="col-sm-6">
                    <input type="number" step="0.01" id="purchase_amount" name="purchase_amount" class="form-control" placeholder="Type here..." required value="<?php echo $purchase_row['purchase_amount'];?>">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="currency">Currency</label>
                  <div class="col-sm-6">
                    <select id="currency" name="currency" class="form-control" required onchange="specify_rate();">
                      <option selected hidden value="">Select an option</option>
                      <?php echo get_currency_option($purchase_row['currency']); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="purchase_attachment">Attachment</label>
                  <div class="col-sm-6">
                    <input type="file" id="purchase_attachment" name="purchase_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
                  </div>
                </div>

                <div id="additional_information_input_containers">
                	<?php

                	$ai = json_decode($purchase_row['additional_informations'] ?? '');
                  $additional_informations = '';
                  if(!empty($ai)){
                    foreach ($ai as $key => $value) {
                      echo get_ai_input('label', $key, $value);
                    }
                  }

                	?>
                </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Description</label>
			            <div class="col-sm-6">
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl"><?php echo $purchase_row['description']; ?></textarea>
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
		        <?php
	      	}
	      break;

	      	case "delete_purchase_form":
		    	
	      ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Purchase</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_purchase.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_purchase"/>

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

	      case "approve_purchase_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-circle"></i> Approve Purchase</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_purchase.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="approve_purchase"/>

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
		              <button type="submit" id="submit" name="submit" class="btn success_btn waves-effect waves-light mr-1"><i class="fa fa-save"></i> Register</button>
                	<button type="reset" class="btn btn-secondary waves-effect waves-light"><i class="fa fa-eraser"></i> Reset</button>
		            </div>
		          </div>

		        </form>
	        </div>
	        <?php
	      	
	      break;

	      case "include_purchase_form":

	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-circle"></i> Include Purchase</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_purchase.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="include_purchase"/>

		        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

		        	

		        	<div class="form-group row">
		            <label class="col-sm-3 col-form-label" for="include_description">Include Description</label>
		            <div class="col-sm-6">
		              <textarea id="include_description" name="include_description" class="form-control" placeholder="Type here..." dir="rtl"></textarea>
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
	        <?php
	      	
	      break;

	      case "filter_table":
		    	
				?>
				<div class="modal-header">
					<h4 class="modal-title" id="add_purchaseTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form" action="list_purchase.php" method="GET">

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="province">Province</label>
						<div class="col-sm-6">
						<select id="province" name="province" class="form-control">
							<option selected hidden value="">Select an option</option>
							<?php echo get_province_option('0'); ?>
						</select>
						</div>
					</div>

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="purchase_date">Purchase Date</label>
						<div class="col-sm-3">
						<input type="text" id="from_purchase_date" name="from_purchase_date" class="form-control date_picker" placeholder="From">
						</div>
						<div class="col-sm-3">
						<input type="text" id="to_purchase_date" name="to_purchase_date" class="form-control date_picker" placeholder="To">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="categories_id">Category</label>
						<div class="col-sm-6">
						<select id="categories_id" name="categories_id" class="form-control" onchange="get_sub_category_option('0', this.value, 'sub_categories_id2');">
							<option selected hidden value="">Select an option</option>
							<?php echo get_category_option('purchase','0'); ?>
						</select>
						</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
					<div class="col-sm-6">
						<select id="sub_categories_id2" name="sub_categories_id" class="form-control">
						</select>
					</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="is_printed">Is Printed</label>
					<div class="col-sm-6">
						<select id="is_printed" name="is_printed" class="form-control">
							<option selected hidden value="">Select an option</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>
					</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="purchase_currency">Currency</label>
					<div class="col-sm-6">
						<select id="purchase_currency" name="purchase_currency" class="form-control">
						<option selected hidden value="">Select an option</option>
						<?php echo get_currency_option('0'); ?>
						</select>
					</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="purchase_amount">Amount</label>
					<div class="col-sm-6">
						<input type="text" id="purchase_amount" name="purchase_amount" class="form-control" placeholder="Type here..." value="">
					</div>
					</div>

					<div class="form-group row">
	          <label class="col-sm-3 col-form-label" for="purchase_users_id">Added By</label>
	          <div class="col-sm-6">
	            <select id="purchase_users_id" name="purchase_users_id" class="form-control">
	              <?php echo get_user_option('0'); ?>
	            </select>
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

	      case "filter_table_purchase_include":
		    	
				?>
				<div class="modal-header">
					<h4 class="modal-title" id="add_purchaseTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form" action="report_purchase_include.php" method="GET">

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="province">Province</label>
						<div class="col-sm-6">
						<select id="province" name="province" class="form-control">
							<option selected hidden value="">Select an option</option>
							<?php echo get_province_option('0'); ?>
						</select>
						</div>
					</div>

						<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="purchase_date">Purchase Date</label>
						<div class="col-sm-3">
						<input type="text" id="from_purchase_date" name="from_purchase_date" class="form-control date_picker" placeholder="From">
						</div>
						<div class="col-sm-3">
						<input type="text" id="to_purchase_date" name="to_purchase_date" class="form-control date_picker" placeholder="To">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="categories_id">Category</label>
						<div class="col-sm-6">
						<select id="categories_id" name="categories_id" class="form-control" onchange="get_sub_category_option('0', this.value, 'sub_categories_id2');">
							<option selected hidden value="">Select an option</option>
							<?php echo get_category_option('purchase','0'); ?>
						</select>
						</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
					<div class="col-sm-6">
						<select id="sub_categories_id2" name="sub_categories_id" class="form-control">
						</select>
					</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="is_printed">Is Printed</label>
					<div class="col-sm-6">
						<select id="is_printed" name="is_printed" class="form-control">
							<option selected hidden value="">Select an option</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>
					</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="purchase_currency">Currency</label>
					<div class="col-sm-6">
						<select id="purchase_currency" name="purchase_currency" class="form-control">
						<option selected hidden value="">Select an option</option>
						<?php echo get_currency_option('0'); ?>
						</select>
					</div>
					</div>

					<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="purchase_amount">Amount</label>
					<div class="col-sm-6">
						<input type="text" id="purchase_amount" name="purchase_amount" class="form-control" placeholder="Type here..." value="">
					</div>
					</div>

					<div class="form-group row">
	          <label class="col-sm-3 col-form-label" for="users_id">Added By</label>
	          <div class="col-sm-6">
	            <select id="users_id" name="users_id" class="form-control">
	            	<option hidden selected value="">Select an option...</option>
	              <?php echo get_user_option('0'); ?>
	            </select>
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

	      case "view_attachment":

			$purchase_attachment_sq = $db->prepare(
			"SELECT *
			FROM `attachments`
			WHERE deleted='0'
			AND type='Purchase Attachment'
			AND reference_id=:data_id"
			);

			$purchase_attachment_sqx = $purchase_attachment_sq->execute([
				'data_id'=>$data_id
			]);

		    	
			?>
			<div class="modal-header">
				<h4 class="modal-title" id="add_purchaseTitle"><i class="far fa-file-image"></i> View Attachment</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row filterable-content">

					<?php

					if($purchase_attachment_sq->rowCount()>0){
						while($purchase_attachment_row = $purchase_attachment_sq->fetch()){
							$attachment_name = basename($purchase_attachment_row['path']);
							?>
							<div class="col-sm-6 col-xl-3 filter-item all web illustrator">
						<div class="gal-box">
							<a title="Screenshot-1">
							<img src="<?php echo $purchase_attachment_row['path']; ?>" class="img-fluid" alt="work-thumbnail">
							</a>
							<div class="gall-info">
							<h4 class="font-16 mt-0"><?php echo $attachment_name; ?></h4>
							</div> <!-- gallery info -->
						</div> <!-- end gal-box -->
						</div> <!-- end col -->
							<?php
						}
					}else{
						?>
							<h2 class="text-center">No attachment</h2>
						<?php
					}

					?>
				</div>
			</div>
			<?php
	      break;

	      case "view_full_info":
	      	$old_data_rows = [];
	      	$new_data_rows = [];

	      	$transaction_edition_sq = $db->prepare("SELECT old_data, new_data FROM `transaction_editions` WHERE reference_type='Purchase' AND reference_id=:data_id ORDER BY id DESC LIMIT 1");
	      	$transaction_edition_sq->execute([
	      		'data_id'=>$data_id
	      	]);

	      	if($transaction_edition_sq->rowCount()>0){
	      		$transaction_edition_row = $transaction_edition_sq->fetch();
	      		foreach(explode('###', $transaction_edition_row['old_data']) as $old_data_item){
	      			$old_data_item_array = explode('=>', $old_data_item);
	      			if(sizeof($old_data_item_array)>1){
	      				$key = str_replace('`', '', $old_data_item_array[0]);
	      				$value = str_replace('`', '', $old_data_item_array[1]);
	      				if($key == 'Sub Category'){
	      					$value = get_col('sub_categories', 'sub_category_name', 'id', $value);
	      				}
	      				if($key == 'Logistic Cash'){
	      					$value = get_col('logistic_cashes', 'name', 'id', $value);
	      				}
	      				$old_data_rows[] = ['key'=>$key, 'value'=>$value];
	      			}
	      		}
	      		foreach(explode('###', $transaction_edition_row['new_data']) as $new_data_item){
	      			$new_data_item_array = explode('=>', $new_data_item);
	      			if(sizeof($new_data_item_array)>1){
	      				$key = str_replace('`', '', $new_data_item_array[0]);
	      				$value = str_replace('`', '', $new_data_item_array[1]);
	      				if($key == 'Sub Category'){
	      					$value = get_col('sub_categories', 'sub_category_name', 'id', $value);
	      				}
	      				if($key == 'Logistic Cash'){
	      					$value = get_col('logistic_cashes', 'name', 'id', $value);
	      				}
	      				$new_data_rows[] = ['key'=>$key, 'value'=>$value];
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
				          		FROM `purchases`
				          		WHERE id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Edition' AS operation_type
				          		FROM `transaction_editions`
				          		WHERE reference_type='Purchase'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Deletion' AS operation_type
				          		FROM `transaction_deletions`
				          		WHERE reference_type='Purchase'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		markup_type AS operation_type
				          		FROM `markups`
				          		WHERE reference_type='Purchase'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Print' AS operation_type
				          		FROM `invoices`
				          		WHERE reference_type='Purchase'
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

	      case "add_attachment_form":

        ?>

        <div class="modal-header">
	        <h4 class="modal-title">Add Attachment</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal parsley-form" role="form" action="controller_purchase.php" method="POST" enctype="multipart/form-data">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_attachment"/>

            <input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="purchase_attachment">Attachment</label>
              <div class="col-sm-6">
                <input type="file" id="purchase_attachment" name="purchase_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
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

  			case "add_purchase":

  			if(check_duplicate_purchase($_POST)==0){

          $logistic_cashes_id = holu_escape($_POST['logistic_cashes_id']);
	  			$province = holu_escape($_POST['province']);
	  			$sub_categories_id = holu_escape($_POST['sub_categories_id']);
			    $purchase_date = holu_escape($_POST['purchase_date']);
	  			$purchase_amount = holu_escape($_POST['purchase_amount']);
			    $currency = holu_escape($_POST['currency']);
			    $description = holu_escape($_POST['description']);
			    

			    $bill_number_extention = get_province_bill_extension($province, 'purchase');

			    $num_purchase_sq = $db->prepare(
			    	"SELECT 
			    	count(id) AS num_purchase
			    	FROM `purchases`
			    	WHERE province=:province
	      		LIMIT 1"
			    );

			    $num_purchase_sqx = $num_purchase_sq->execute([
			    	'province'=>$province
			    ]);

			    $num_purchase_row = $num_purchase_sq->fetch();
			    $num_purchase = $num_purchase_row['num_purchase'];
			    $check_number = $bill_number_extention.(1000073+$num_purchase);


			    $purchase_iq = $db->prepare(
			    	"INSERT INTO `purchases` (
              logistic_cashes_id, 
			    		province, 
			    		sub_categories_id, 
			    		purchase_date, 
			    		purchase_amount, 
			    		currency, 
			    		check_number, 
			    		description, 
			    		insertion_date, 
			    		insertion_time, 
			    		users_id
			    	) VALUES (
              :logistic_cashes_id, 
			    		:province, 
			    		:sub_categories_id, 
			    		:purchase_date, 
			    		:purchase_amount, 
			    		:currency, 
			    		:check_number, 
			    		:description, 
			    		:holu_date, 
			    		:holu_time,
			    		:holu_users_id
			    	)"
			    );

			    $purchase_iqx = $purchase_iq->execute([
            'logistic_cashes_id'=>$logistic_cashes_id,
			    	'province'=>$province,
			    	'sub_categories_id'=>$sub_categories_id,
			    	'purchase_date'=>$purchase_date,
			    	'purchase_amount'=>$purchase_amount,
			    	'currency'=>$currency,
			    	'check_number'=>$check_number,
			    	'description'=>$description,
			    	'holu_date'=>$holu_date,
			    	'holu_time'=>$holu_time,
			    	'holu_users_id'=>$holu_users_id
			    ]);

			    $purchases_id = $db->lastInsertId();
			
			    

			    if(!empty(array_filter($_FILES['purchase_attachment']['name']))){
						foreach ($_FILES['purchase_attachment']['name'] as $key => $value) {
					    if($_FILES['purchase_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/purchase_attachment/".holu_encode($purchases_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['purchase_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['purchase_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Purchase Attachment',
									'reference_id'=>$purchases_id,
									'path'=>$target_file,
									'insertion_date'=>$holu_date,
									'insertion_time'=>$holu_time,
									'users_id'=>$holu_users_id
								]);
					    }
					  }
					}

			    if($purchase_iq){
			      header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
			      exit();
			    }else{
			      header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
			      exit();
			    }

			  }else{
					header("location:".set_referer($_SERVER['HTTP_REFERER'])."duplicated");
			    exit();
				}

  			break;

  			case "edit_purchase":

  			if(check_duplicate_purchase($_POST)==0){

  				track_editions('edit_purchase', ['purchases_id'=>$_POST['data_id'], 'data_array'=>$_POST]);

	  			$data_id = holu_escape(holu_decode($_POST['data_id']));
			    $logistic_cashes_id = holu_escape($_POST['logistic_cashes_id']);
			    $sub_categories_id = holu_escape($_POST['sub_categories_id']);
	  			$purchase_amount = holu_escape($_POST['purchase_amount']);
			    $purchase_date = holu_escape($_POST['purchase_date']);
			    $currency = holu_escape($_POST['currency']);
			    $description = holu_escape($_POST['description']);
			         
	  			$purchase_uq = $db->prepare(
	  				"UPDATE `purchases` SET 
	  				logistic_cashes_id=:logistic_cashes_id, 
	  				sub_categories_id=:sub_categories_id, 
	  				purchase_amount=:purchase_amount, 
	  				purchase_date=:purchase_date, 
	  				currency=:currency, 
	  				description=:description 
	  				WHERE id=:data_id 
	  				LIMIT 1"
	  			);

	  			$purchase_uqx = $purchase_uq->execute([
	  				'logistic_cashes_id'=>$logistic_cashes_id,
	  				'sub_categories_id'=>$sub_categories_id,
	  				'purchase_amount'=>$purchase_amount,
	  				'purchase_date'=>$purchase_date,
	  				'currency'=>$currency,
	  				'description'=>$description,
	  				'data_id'=>$data_id
	  			]);

	  			if(!empty(array_filter($_FILES['purchase_attachment']['name']))){
			    	$purchases_id = $data_id;
						foreach ($_FILES['purchase_attachment']['name'] as $key => $value) {
					    if($_FILES['purchase_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/purchase_attachment/".holu_encode($purchases_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['purchase_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['purchase_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Purchase Attachment',
									'reference_id'=>$purchases_id,
									'path'=>$target_file,
									'insertion_date'=>$holu_date,
									'insertion_time'=>$holu_time,
									'users_id'=>$holu_users_id
								]);
					    }
					  }
					}

	  			if($purchase_uq){
	  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
	  				exit();
	  			}else{
	  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
	  				exit();
	  			}

	  		}else{
					header("location:".set_referer($_SERVER['HTTP_REFERER'])."duplicated");
			    exit();
				}

  			break;

  			case "approve_purchase":

  			$data_id = holu_escape(holu_decode($_POST['data_id']));
  			$approve_description = holu_escape($_POST['approve_description']);

  			$purchase_uq = $db->query("UPDATE `purchases` SET is_approved='1', approve_date='$holu_date', approve_time='$holu_time', approve_description='$approve_description', approved_by='$holu_users_id' WHERE id='$data_id' LIMIT 1");

  			if($purchase_uq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}


  			break;

  			case "include_purchase":

  			$data_id = holu_escape(holu_decode($_POST['data_id']));
  			$include_description = holu_escape($_POST['include_description']);
  			

  			$purchase_uq = $db->query("UPDATE `purchases` SET is_included='1', include_date='$holu_date', include_time='$holu_time', include_description='$include_description', included_by='$holu_users_id' WHERE id='$data_id' LIMIT 1");

  			if($purchase_uq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}


  			break;

  			case "delete_purchase":

  			track_deletions('delete_purchase', ['purchases_id'=>$_POST['data_id']]);

  			$data_id = holu_escape(holu_decode($_POST['data_id']));

  			$purchase_dq = $db->prepare(
  				"UPDATE `purchases` SET 
  				deleted='1' 
  				WHERE id=:data_id 
  				LIMIT 1"
  			);

  			$purchase_dqx = $purchase_dq->execute([
  				'data_id'=>$data_id
  			]);

  			if($purchase_dq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}

  			break;

  			case 'add_attachment':{
	  			$data_id = holu_escape(holu_decode($_POST['data_id']));
	  			if(!empty(array_filter($_FILES['purchase_attachment']['name']))){
		  				
			    	$purchases_id = $data_id;
						foreach ($_FILES['purchase_attachment']['name'] as $key => $value) {
					    if($_FILES['purchase_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/purchase_attachment/".holu_encode($purchases_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['purchase_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['purchase_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Purchase Attachment',
									'reference_id'=>$purchases_id,
									'path'=>$target_file,
									'insertion_date'=>$holu_date,
									'insertion_time'=>$holu_time,
									'users_id'=>$holu_users_id
								]);
					    }
					  }
					}

		      header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
		      exit();
		      
	  		}break;

  			default:

  			break;

  		}
  	}
  }
?>
