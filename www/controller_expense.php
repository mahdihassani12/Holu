<?php

  include("../lib/_configuration.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape(holu_decode($_POST['data_id']));
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

  			case "add_expense_form":

        ?>

        <div class="modal-header">
	        <h4 class="modal-title"><i class="fa fa-plus"></i> Add Expense</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal parsley-form" role="form" action="controller_expense.php" method="POST" enctype="multipart/form-data">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_expense"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="province">Province</label>
              <div class="col-sm-6">
                <select id="province" name="province" class="form-control" required onchange="get_branch_option(this.value, '0', 'branch');">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_province_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="branch">Branch</label>
              <div class="col-sm-6">
                <select id="branch" name="branch" class="form-control" required>
                  <option selected hidden value="">Select an option</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
              <div class="col-sm-6">
                <select id="categories_id" name="categories_id" class="form-control" required onchange="get_sub_category_option('0', this.value, 'sub_categories_id');">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_category_option("expense", "0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
              <div class="col-sm-6">
                <select id="sub_categories_id" name="sub_categories_id" class="form-control" required onchange="get_sub_cat_conf();">
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="expense_date">Expense Date</label>
              <div class="col-sm-6">
                <input type="text" id="expense_date" name="expense_date" class="form-control date_picker" placeholder="Pick a date..." required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="expense_amount">Amount</label>
              <div class="col-sm-6">
                <input type="number" 
                		   step="0.01" 
                		   id="expense_amount" 
                		   name="expense_amount" 
                		   class="form-control" 
                		   placeholder="Type here..." 
                		   autocomplete="off" 
                		   required />
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
              <label class="col-sm-3 col-form-label" for="expense_attachment">Attachment</label>
              <div class="col-sm-6">
                <input type="file" id="expense_attachment" name="expense_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
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

	      case "edit_expense_form":

		    	$expense_sq = $db->prepare(
		    		"SELECT * 
		    		FROM `expenses` 
		    		WHERE deleted='0' 
		    		AND id=:data_id 
		    		LIMIT 1"
		    	);

		    	$expense_sqx = $expense_sq->execute([
		    		'data_id'=>$data_id
		    	]);

		    	if($expense_sq->rowCount()>0){
		    		$expense_row = $expense_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Expense</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_expense.php" method="POST" enctype="multipart/form-data">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_expense"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

                <input type="hidden" name="branch" id="branch" value="<?php echo $expense_row['branch']; ?>"/>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
                  <div class="col-sm-6">
                    <select id="categories_id" name="categories_id" class="form-control" required onchange="get_sub_category_option('0', this.value, 'sub_categories_id');">
                      <option selected hidden value="">Select an option</option>
                      <?php echo get_category_option("expense", get_col('sub_categories', 'categories_id', 'id', $expense_row['sub_categories_id'])); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
                  <div class="col-sm-6">
                    <select id="sub_categories_id" name="sub_categories_id" class="form-control" required onchange="get_sub_cat_conf();">
                    	<?php echo get_sub_category_option($expense_row['sub_categories_id'], get_col('sub_categories', 'categories_id', 'id', $expense_row['sub_categories_id'])); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="expense_date">Expense Date</label>
                  <div class="col-sm-6">
                    <input type="text" id="expense_date" name="expense_date" class="form-control date_picker" placeholder="Pick a date..." required value="<?php echo $expense_row['expense_date'];?>">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="expense_amount">Amount</label>
                  <div class="col-sm-6">
                    <input type="number" 
                    			 step="0.01" 
                    			 id="expense_amount" 
                    			 name="expense_amount" 
                    			 class="form-control" 
                    			 placeholder="Type here..." 
                    			 required 
                    			 autocomplete="off"
                    			 value="<?php echo $expense_row['expense_amount'];?>" />
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="currency">Currency</label>
                  <div class="col-sm-6">
                    <select id="currency" name="currency" class="form-control" required onchange="specify_rate();">
                      <option selected hidden value="">Select an option</option>
                      <?php echo get_currency_option($expense_row['currency']); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="expense_attachment">Attachment</label>
                  <div class="col-sm-6">
                    <input type="file" id="expense_attachment" name="expense_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
                  </div>
                </div>

                <div id="additional_information_input_containers">
                  <?php

                  $json = $expense_row['additional_informations'] ?? '';

				$ai = $json !== ''
					? json_decode($json, true)
					: null;
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
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl"><?php echo $expense_row['description']; ?></textarea>
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

	      case "delete_expense_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Expense</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_expense.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_expense"/>

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

	      case "filter_table":

		    	
        ?>
      	<div class="modal-header">
	        <h4 class="modal-title" id="add_expenseTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal" role="form" action="list_expense.php" method="GET">

	        	<div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="province">Province</label>
	            <div class="col-sm-6">
	              <select id="province" name="province" class="form-control" data-branch-target="branch" data-branch-value="0" onchange="get_branch_option(this.value, this.getAttribute('data-branch-value') || '0', this.getAttribute('data-branch-target') || 'branch', this); this.setAttribute('data-branch-value', '0');">
	              	<option selected hidden value="">Select an option</option>
	              	<?php echo get_province_option('0'); ?>
	              </select>
	            </div>
	          </div>

	          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="branch">Branch</label>
            <div class="col-sm-6">
              <select id="branch" name="branch" class="form-control">
                <?php echo get_branch_option('0', ''); ?>
              </select>
            </div>
          </div>

	        	<div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="expense_date">Expense Date</label>
	            <div class="col-sm-3">
	              <input type="text" id="from_expense_date" name="from_expense_date" class="form-control date_picker" placeholder="From">
	            </div>
	            <div class="col-sm-3">
	              <input type="text" id="to_expense_date" name="to_expense_date" class="form-control date_picker" placeholder="To">
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
	            <div class="col-sm-6">
	              <select id="categories_id" name="categories_id" class="form-control" onchange="get_sub_category_option('0', this.value, 'sub_categories_id2');">
	              	<option selected hidden value="">Select an option</option>
	              	<?php echo get_category_option('expense','0'); ?>
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
              <label class="col-sm-3 col-form-label" for="expense_currency">Currency</label>
              <div class="col-sm-6">
                <select id="expense_currency" name="expense_currency" class="form-control">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_currency_option('0'); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="expense_amount">Amount</label>
              <div class="col-sm-6">
                <input type="text" 
                			 id="expense_amount" 
                			 name="expense_amount" 
                			 class="form-control" 
                			 placeholder="Type here..." 
                			 value=""
                			 autocomplete="off" 
                			 />
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

	      $expense_attachment_sq = $db->prepare(
	      "SELECT *
	      FROM `attachments`
	      WHERE deleted='0'
	      AND type='Expense Attachment'
	      AND reference_id=:data_id"
	      );

	      $expense_attachment_sqx = $expense_attachment_sq->execute([
	      	'data_id'=>$data_id
	      ]);

		    	
        ?>
      	<div class="modal-header">
	        <h4 class="modal-title" id="add_expenseTitle"><i class="far fa-file-image"></i> View Attachment</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <div class="row filterable-content">

	        	<?php

	        	if($expense_attachment_sq->rowCount()>0){
	        		while($expense_attachment_row = $expense_attachment_sq->fetch()){
	        			$attachment_name = basename($expense_attachment_row['path']);
	        			?>
	        			<div class="col-sm-6 col-xl-3 filter-item all web illustrator">
		              <div class="gal-box">
		                <a title="Screenshot-1">
		                  <img src="<?php echo $expense_attachment_row['path']; ?>" class="img-fluid" alt="work-thumbnail">
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
                <a href="#basic_info" data-toggle="tab" aria-expanded="true" class="nav-link active">
                  <span class="d-inline-block d-sm-none"><i class="fas fa-home"></i></span>
                  <span class="d-none d-sm-inline-block">Basic Info</span>   
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

              <div class="tab-pane fade show active" id="basic_info">
			        	<div class="item form-group"  >
				          <label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
				          <div class="col-md-12 col-sm-12 col-xs-12">
				          	
				            <div class="table-responsive">
		                  <table class="table table-bordered table-sm mb-0">
		                    <thead>
		                      <tr>
		                        <th colspan="2" class="text-center">Basic Info</th>
		                      </tr>
		                    </thead>
		                    <tbody>
		                    	<?php
						          		$expense_sq = $db->prepare("SELECT * FROM `expenses` WHERE id=:data_id LIMIT 1");
										    	$expense_sqx = $expense_sq->execute([
										    		'data_id'=>$data_id
										    	]);

										    	if($expense_sq->rowCount()>0){
										    		$expense_row = $expense_sq->fetch();
						          			?>
	                      		<tr>
			                        <th>Province</th>
			                        <td><?php echo $expense_row['province']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Branch</th>
			                        <td><?php echo $expense_row['branch']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Category</th>
			                        <td><?php echo get_col('sub_categories', 'sub_category_name', 'id', $expense_row['sub_categories_id']); ?></td>
			                      </tr>
			                      <tr>
			                        <th>Sub Category</th>
			                        <td><?php echo get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $expense_row['sub_categories_id'])); ?></td>
			                      </tr>
			                      <tr>
			                        <th>Date</th>
			                        <td><?php echo $expense_row['expense_date']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Amount</th>
			                        <td><?php echo $expense_row['expense_amount']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Currency</th>
			                        <td><?php echo $expense_row['currency']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Check Number</th>
			                        <td><?php echo $expense_row['check_number']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Description</th>
			                        <td><?php echo $expense_row['description']; ?></td>
			                      </tr>
		                      	<?php
													}
							            ?>
		                    </tbody>
		                  </table>
		                </div> <!-- end table-responsive-->
				              	
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
				          		FROM `expenses`
				          		WHERE id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Edition' AS operation_type
				          		FROM `transaction_editions`
				          		WHERE reference_type='Expense'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Deletion' AS operation_type
				          		FROM `transaction_deletions`
				          		WHERE reference_type='Expense'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		markup_type AS operation_type
				          		FROM `markups`
				          		WHERE reference_type='Expense'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Print' AS operation_type
				          		FROM `invoices`
				          		WHERE reference_type='Expense'
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
	        <h4 class="modal-title"><i class="fas fa-file-import"></i> Add Attachment</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal parsley-form" role="form" action="controller_expense.php" method="POST" enctype="multipart/form-data">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_attachment"/>

            <input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="expense_attachment">Attachment</label>
              <div class="col-sm-6">
                <input type="file" id="expense_attachment" name="expense_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
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

  			case "add_expense":

  			if(check_duplicate_expense($_POST)==0){

	  			$province = holu_escape($_POST['province']);
			    $branch = holu_escape($_POST['branch']);
			    $sub_categories_id = holu_escape($_POST['sub_categories_id']);
			    $expense_date = holu_escape($_POST['expense_date']);
	  			$expense_amount = holu_escape($_POST['expense_amount']);
			    $currency = holu_escape($_POST['currency']);
			    $description = holu_escape($_POST['description']);
			    
          $key_infos = array();
          $value_infos = array();

          if(!empty($_POST['customer_name'])){
            array_push($key_infos, 'Customer Name');
            array_push($value_infos, holu_escape($_POST['customer_name']));
          }
          if(!empty($_POST['customer_id'])){
            array_push($key_infos, 'Customer ID');
            array_push($value_infos, holu_escape($_POST['customer_id']));
          }
          if(!empty($_POST['package'])){
            array_push($key_infos, 'Package');
            array_push($value_infos, holu_escape($_POST['package']));
          }
          if(!empty($_POST['start_date'])){
            array_push($key_infos, 'Start Date');
            array_push($value_infos, holu_escape($_POST['start_date']));
          }
          if(!empty($_POST['end_date'])){
            array_push($key_infos, 'End Date');
            array_push($value_infos, holu_escape($_POST['end_date']));
          }
          if(!empty($_POST['equipment'])){
            array_push($key_infos, 'Equipment');
            array_push($value_infos, holu_escape($_POST['equipment']));
          }
          if(!empty($_POST['other_services'])){
            array_push($key_infos, 'Other Services');
            array_push($value_infos, holu_escape($_POST['other_services']));
          }
          if(!empty($_POST['employee'])){
            array_push($key_infos, 'Employee');
            array_push($value_infos, holu_escape($_POST['employee']));
          }

			    $num_expense_sq = $db->prepare(
			    	"SELECT 
			    	count(id) AS num_expense
			    	FROM `expenses`
			    	WHERE province=:province AND branch=:branch
	      		LIMIT 1"
			    );

			    $num_expense_sqx = $num_expense_sq->execute([
			    	'province'=>$province,
			    	'branch'=>$branch
			    ]);

			    $num_expense_row = $num_expense_sq->fetch();
			    $num_expense = $num_expense_row['num_expense'];
			    $check_number = generate_check_number('expense', $province, $branch, $num_expense+1);

			    $expense_iq = $db->prepare(
			    	"INSERT INTO `expenses` (
			    		province, 
			    		branch,
			    		sub_categories_id, 
			    		expense_date, 
			    		expense_amount, 
			    		currency, 
			    		check_number, 
			    		description, 
			    		insertion_date, 
			    		insertion_time, 
			    		users_id
			    	) VALUES (
			    		:province, 
			    		:branch,
			    		:sub_categories_id, 
			    		:expense_date, 
			    		:expense_amount, 
			    		:currency, 
			    		:check_number, 
			    		:description, 
			    		:holu_date, 
			    		:holu_time,
			    		:holu_users_id
			    	)"
			    );

			    $expense_iqx = $expense_iq->execute([
			    	'province'=>$province,
			    	'branch'=>$branch,
			    	'sub_categories_id'=>$sub_categories_id,
			    	'expense_date'=>$expense_date,
			    	'expense_amount'=>$expense_amount,
			    	'currency'=>$currency,
			    	'check_number'=>$check_number,
			    	'description'=>$description,
			    	'holu_date'=>$holu_date,
			    	'holu_time'=>$holu_time,
			    	'holu_users_id'=>$holu_users_id
			    ]);

			    $expenses_id = $db->lastInsertId();
			
			    for ($i=0; $i<sizeof($key_infos); $i++) {

			    	$key_info = holu_escape($key_infos[$i]);
			    	$value_info = holu_escape($value_infos[$i]);

			    	$additional_information_sq = $db->prepare(
			    		"SELECT id
			    		FROM `additional_informations`
			    		WHERE reference_type='Expense'
			    		AND reference_id=:expenses_id
			    		AND key_info=:key_info
			    		AND deleted='0'"
			    	);

			    	$additional_information_sqx = $additional_information_sq->execute([
			    		'expenses_id'=>$expenses_id,
			    		'key_info'=>$key_info
			    	]);

			    	if($key_info!="" AND $additional_information_sq->rowCount()<1){
			    		$additional_information_iq = $db->prepare(
			    			"INSERT INTO `additional_informations` (
					    		reference_type, 
						    	reference_id, 
						    	key_info, 
						    	value_info, 
						    	insertion_date, 
						    	insertion_time, 
						    	users_id
						    ) VALUES (
						    	:reference_type, 
						    	:reference_id, 
						    	:key_info, 
						    	:value_info, 
						    	:holu_date, 
						    	:holu_time,
						    	:holu_users_id
						    )"
					    );

					    $additional_information_iqx = $additional_information_iq->execute([
					    	'reference_type'=>'Expense',
					    	'reference_id'=>$expenses_id,
					    	'key_info'=>$key_info,
					    	'value_info'=>$value_info,
					    	'holu_date'=>$holu_date,
					    	'holu_time'=>$holu_time,
					    	'holu_users_id'=>$holu_users_id
					    ]);
			    	}
			    	
			    }

          $ai_sq = $db->query("SELECT * FROM additional_informations WHERE deleted='0' AND reference_type='Expense' AND reference_id='$expenses_id'");
          if($ai_sq->rowCount()>0){
            $ai_array = [];
            while($ai_row = $ai_sq->fetch()){
              $ai_array[$ai_row['key_info']] = $ai_row['value_info'];
            }
            $ai_array = json_encode($ai_array, JSON_UNESCAPED_UNICODE);

            $expense_uq = $db->query("UPDATE expenses SET additional_informations='$ai_array' WHERE id='$expenses_id'");
          }

			    if(!empty(array_filter($_FILES['expense_attachment']['name']))){
						foreach ($_FILES['expense_attachment']['name'] as $key => $value) {
					    if($_FILES['expense_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/expense_attachment/".holu_encode($expenses_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['expense_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['expense_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Expense Attachment',
									'reference_id'=>$expenses_id,
									'path'=>$target_file,
									'insertion_date'=>$holu_date,
									'insertion_time'=>$holu_time,
									'users_id'=>$holu_users_id
								]);
					    }
					  }
					}

			    if($expense_iq){
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

  			case "edit_expense":

  			if(check_duplicate_expense($_POST)==0){

	  			track_editions('edit_expense', ['expenses_id'=>$_POST['data_id'], 'data_array'=>$_POST]);

	  			$data_id = holu_escape(holu_decode($_POST['data_id']));
			    $branch = holu_escape($_POST['branch']);
			    $sub_categories_id = holu_escape($_POST['sub_categories_id']);
	  			$expense_amount = holu_escape($_POST['expense_amount']);
			    $expense_date = holu_escape($_POST['expense_date']);
			    $currency = holu_escape($_POST['currency']);
			    $description = holu_escape($_POST['description']);
			    
          $key_infos = array();
          $value_infos = array();

          if(!empty($_POST['customer_name'])){
            array_push($key_infos, 'Customer Name');
            array_push($value_infos, holu_escape($_POST['customer_name']));
          }
          if(!empty($_POST['customer_id'])){
            array_push($key_infos, 'Customer ID');
            array_push($value_infos, holu_escape($_POST['customer_id']));
          }
          if(!empty($_POST['package'])){
            array_push($key_infos, 'Package');
            array_push($value_infos, holu_escape($_POST['package']));
          }
          if(!empty($_POST['start_date'])){
            array_push($key_infos, 'Start Date');
            array_push($value_infos, holu_escape($_POST['start_date']));
          }
          if(!empty($_POST['end_date'])){
            array_push($key_infos, 'End Date');
            array_push($value_infos, holu_escape($_POST['end_date']));
          }
          if(!empty($_POST['equipment'])){
            array_push($key_infos, 'Equipment');
            array_push($value_infos, holu_escape($_POST['equipment']));
          }
          if(!empty($_POST['other_services'])){
            array_push($key_infos, 'Other Services');
            array_push($value_infos, holu_escape($_POST['other_services']));
          }
          if(!empty($_POST['employee'])){
            array_push($key_infos, 'Employee');
            array_push($value_infos, holu_escape($_POST['employee']));
          }

	  			$expense_uq = $db->prepare(
	  				"UPDATE `expenses` SET 
	  				branch=:branch,
	  				sub_categories_id=:sub_categories_id, 
	  				expense_amount=:expense_amount, 
	  				expense_date=:expense_date, 
	  				currency=:currency, 
	  				description=:description 
	  				WHERE id=:data_id 
	  				LIMIT 1"
	  			);

	  			$expense_uqx = $expense_uq->execute([
	  				'branch'=>$branch,
	  				'sub_categories_id'=>$sub_categories_id,
	  				'expense_amount'=>$expense_amount,
	  				'expense_date'=>$expense_date,
	  				'currency'=>$currency,
	  				'description'=>$description,
	  				'data_id'=>$data_id
	  			]);

	  			$additional_information_uq = $db->prepare(
	  				"UPDATE `additional_informations` SET
	  				deleted='1'
	  				WHERE reference_type='Expense'
	  				AND reference_id=:data_id"
	  			);
	  			$additional_information_uqx = $additional_information_uq->execute([
	  				'data_id'=>$data_id
	  			]);

	  			for ($i=0; $i<sizeof($key_infos); $i++) {

			    	$key_info = holu_escape($key_infos[$i]);
			    	$value_info = holu_escape($value_infos[$i]);

			    	$additional_information_sq = $db->prepare(
			    		"SELECT id
			    		FROM `additional_informations`
			    		WHERE reference_type='Expense'
			    		AND reference_id=:data_id
			    		AND key_info=:key_info
			    		AND deleted='0'"
			    	);

			    	$additional_information_sqx = $additional_information_sq->execute([
			    		'data_id'=>$data_id,
			    		'key_info'=>$key_info
			    	]);

			    	if($key_info!="" AND $additional_information_sq->rowCount()<1){

			    		$additional_information_iq = $db->prepare(
				    		"INSERT INTO `additional_informations` (
						    	reference_type, 
						    	reference_id, 
						    	key_info, 
						    	value_info, 
						    	insertion_date, 
						    	insertion_time, 
						    	users_id
						    ) VALUES (
						    	:reference_type, 
						    	:reference_id, 
						    	:key_info, 
						    	:value_info, 
						    	:holu_date, 
						    	:holu_time,
						    	:holu_users_id
						    )"
					    );

					    $additional_information_iqx = $additional_information_iq->execute([
					    	'reference_type'=>'Expense',
					    	'reference_id'=>$data_id,
					    	'key_info'=>$key_info,
					    	'value_info'=>$value_info,
					    	'holu_date'=>$holu_date,
					    	'holu_time'=>$holu_time,
					    	'holu_users_id'=>$holu_users_id
					    ]);
			    	}
			    }

          $ai_sq = $db->query("SELECT * FROM additional_informations WHERE deleted='0' AND reference_type='Expense' AND reference_id='$data_id'");
          if($ai_sq->rowCount()>0){
            $ai_array = [];
            while($ai_row = $ai_sq->fetch()){
              $ai_array[$ai_row['key_info']] = $ai_row['value_info'];
            }
            $ai_array = json_encode($ai_array, JSON_UNESCAPED_UNICODE);

            $expense_uq = $db->query("UPDATE expenses SET additional_informations='$ai_array' WHERE id='$data_id'");
          }

	  			if(!empty(array_filter($_FILES['expense_attachment']['name']))){
			    	$expenses_id = $data_id;
						foreach ($_FILES['expense_attachment']['name'] as $key => $value) {
					    if($_FILES['expense_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/expense_attachment/".holu_encode($expenses_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['expense_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['expense_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Expense Attachment',
									'reference_id'=>$expenses_id,
									'path'=>$target_file,
									'insertion_date'=>$holu_date,
									'insertion_time'=>$holu_time,
									'users_id'=>$holu_users_id
								]);
					    }
					  }
					}

	  			if($expense_uq){
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

  			case "delete_expense":

  			track_deletions('delete_expense', ['expenses_id'=>$_POST['data_id']]);

  			$data_id = holu_escape(holu_decode($_POST['data_id']));

  			$expense_dq = $db->prepare(
  				"UPDATE `expenses` SET 
  				deleted='1' 
  				WHERE id=:data_id 
  				LIMIT 1"
  			);

  			$expense_dqx = $expense_dq->execute([
  				'data_id'=>$data_id
  			]);

  			if($expense_dq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}

  			break;

  			case 'add_attachment':{
	  			$data_id = holu_escape(holu_decode($_POST['data_id']));
	  			if(!empty(array_filter($_FILES['expense_attachment']['name']))){
		  				
			    	$expenses_id = $data_id;
						foreach ($_FILES['expense_attachment']['name'] as $key => $value) {
					    if($_FILES['expense_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/expense_attachment/".holu_encode($expenses_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['expense_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['expense_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Expense Attachment',
									'reference_id'=>$expenses_id,
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
