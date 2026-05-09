<?php

  include("../lib/_configuration.php");
  include_once("_attachment_preview.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape(holu_decode($_POST['data_id']));
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

	      case "add_income_form":

        ?>

        <div class="modal-header">
	        <h4 class="modal-title"><i class="fa fa-plus"></i> Add Income</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal parsley-form" role="form" action="controller_income.php" method="POST" enctype="multipart/form-data">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_income"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="province">Province</label>
              <div class="col-sm-6">
                <select id="province" name="province" class="form-control select2" required onchange="get_branch_option(this.value, '0', 'branch');">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_province_option("0"); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="branch">Branch</label>
              <div class="col-sm-6">
                <select id="branch" name="branch" class="form-control select2" required>
                  <option selected hidden value="">Select an option</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
              <div class="col-sm-6">
                <select id="categories_id" name="categories_id" class="form-control" required onchange="get_sub_category_option('0', this.value, 'sub_categories_id');">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_category_option("income", "0"); ?>
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
              <label class="col-sm-3 col-form-label" for="income_date">Income Date</label>
              <div class="col-sm-6">
                <input type="text" id="income_date" name="income_date" class="form-control date_picker" placeholder="Pick a date..." required value="<?php echo date("Y-m-d"); ?>">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="income_amount">Amount</label>
              <div class="col-sm-6">
                <input type="number" 
                       step="0.01" 
                       id="income_amount" 
                       name="income_amount" 
                       class="form-control" 
                       placeholder="Type here..."
                       autocomplete="off" 
                       required />
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="income_percentage">Percentage</label>
              <div class="col-sm-6">
                <input type="number" 
                		   step="0.01" 
                		   id="income_percentage" 
                		   name="income_percentage" 
                		   class="form-control" 
                		   placeholder="Type here..." />
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
              <label class="col-sm-3 col-form-label" for="income_attachment">Attachment</label>
              <div class="col-sm-6">
                <input type="file" id="income_attachment" name="income_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
              </div>
            </div>

            <div id="additional_information_input_containers">
              
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="description">Description</label>
              <div class="col-sm-6">
                <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl" required></textarea>
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

	      case "edit_income_form":

		    	$income_sq = $db->prepare("SELECT * FROM `incomes` WHERE deleted='0' AND id=:data_id LIMIT 1");
		    	$income_sqx = $income_sq->execute([
		    		'data_id'=>$data_id
		    	]);

		    	if($income_sq->rowCount()>0){
		    		$income_row = $income_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Income</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_income.php" method="POST" enctype="multipart/form-data">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_income"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

			        	<input type="hidden" name="province" id="province" value="<?php echo $income_row['province']; ?>"/>

                <input type="hidden" name="branch" id="branch" value="<?php echo $income_row['branch']; ?>"/>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
                  <div class="col-sm-6">
                    <select id="categories_id" name="categories_id" class="form-control" required onchange="get_sub_category_option('0', this.value, 'sub_categories_id');">
                      <option selected hidden value="">Select an option</option>
                      <?php echo get_category_option("income", get_col('sub_categories', 'categories_id', 'id', $income_row['sub_categories_id'])); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
                  <div class="col-sm-6">
                    <select id="sub_categories_id" name="sub_categories_id" class="form-control" required onchange="get_sub_cat_conf();">
                    	<?php echo get_sub_category_option($income_row['sub_categories_id'], get_col('sub_categories', 'categories_id', 'id', $income_row['sub_categories_id'])); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="income_date">Income Date</label>
                  <div class="col-sm-6">
                    <input type="text" id="income_date" name="income_date" class="form-control date_picker" placeholder="Pick a date..." required value="<?php echo $income_row['income_date'];?>">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="income_amount">Amount</label>
                  <div class="col-sm-6">
                    <input type="number" 
                    		   step="0.01" 
                    		   id="income_amount" 
                    		   name="income_amount" 
                    		   class="form-control" 
                    		   placeholder="Type here..." 
                    		   required 
                    		   autocomplete="off"
                    		   value="<?php echo $income_row['income_amount'];?>" />
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="currency">Currency</label>
                  <div class="col-sm-6">
                    <select id="currency" name="currency" class="form-control" required onchange="specify_rate();">
                      <option selected hidden value="">Select an option</option>
                      <?php echo get_currency_option($income_row['currency']); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="income_attachment">Attachment</label>
                  <div class="col-sm-6">
                    <input type="file" id="income_attachment" name="income_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
                  </div>
                </div>

                <div id="additional_information_input_containers">
                	<?php

                	$ai = json_decode($income_row['additional_informations'] ?? '');
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
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..."  dir="rtl" required><?php echo $income_row['description']; ?></textarea>
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

	      case "approve_tms_income_request_form":

		    	$data_id_array = explode('###', $data_id);

		    	$tms_customers_id = $data_id_array[0];
		    	$request_type = $data_id_array[1];

		    	switch ($request_type) {
		    		case 'Internet Cost':{

		    			$tms_customer_sq = $db->query(
		    				"SELECT *
		    				FROM tms_customers
		    				WHERE deleted='0'
		    				AND id='$tms_customers_id'
		    				LIMIT 1"
		    			);

		    			if($tms_customer_sq->rowCount()>0){
		    				$tms_customer_row = $tms_customer_sq->fetch();

		    				$tms_province = $tms_customer_row['province'];
						$holu_province = map_tms_province_to_holu_province($tms_province);

		    				$holu_categories_id = 99;

		    				$holu_sub_categories_id = 6;

		    				$holu_cost = $tms_customer_row['cost'];

		    				$holu_currency = $tms_customer_row['currency'];

		    				$holu_customer_name = $tms_customer_row['customer_name'];
		    				$holu_customer_id = $tms_customer_row['customer_id'];
		    				$holu_packages_id = $tms_customer_row['package_id'];

		    				$holu_package = get_col('tms_packages', 'package', 'id', $holu_packages_id).' - '.get_col('tms_packages', 'speed', 'id', $holu_packages_id).' - '.get_col('tms_packages', 'period', 'id', $holu_packages_id).' Month';

		    				$holu_start_date = $tms_customer_row['contract_start_date'];



		    			}
		    		}break;

		    		case 'Receiver Cost':{

		    			$tms_customer_sq = $db->query(
		    				"SELECT *
		    				FROM tms_customers
		    				WHERE deleted='0'
		    				AND id='$tms_customers_id'
		    				LIMIT 1"
		    			);

		    			if($tms_customer_sq->rowCount()>0){
		    				$tms_customer_row = $tms_customer_sq->fetch();

		    				$tms_province = $tms_customer_row['province'];
						$holu_province = map_tms_province_to_holu_province($tms_province);

		    				$holu_categories_id = 100;

		    				$holu_sub_categories_id = 9;

		    				$holu_cost = $tms_customer_row['receiver_cost'];

		    				$holu_currency = $tms_customer_row['receiver_currency'];

		    				$holu_customer_name = $tms_customer_row['customer_name'];
		    				$holu_customer_id = $tms_customer_row['customer_id'];
		    				$holu_device_types_id = $tms_customer_row['device_type_id'];

		    				$holu_equipment = get_col('tms_device_types', 'type', 'id', $holu_device_types_id).' - '.get_col('tms_device_types', 'model', 'id', $holu_device_types_id);

		    			}

		    		}break;

		    		case 'Router Cost':{

		    			$tms_customer_sq = $db->query(
		    				"SELECT *
		    				FROM tms_customers
		    				WHERE deleted='0'
		    				AND id='$tms_customers_id'
		    				LIMIT 1"
		    			);

		    			if($tms_customer_sq->rowCount()>0){
		    				$tms_customer_row = $tms_customer_sq->fetch();

		    				$tms_province = $tms_customer_row['province'];
						$holu_province = map_tms_province_to_holu_province($tms_province);

		    				$holu_categories_id = 100;

		    				$holu_sub_categories_id = 9;

		    				$holu_cost = $tms_customer_row['router_cost'];

		    				$holu_currency = $tms_customer_row['router_currency'];

		    				$holu_customer_name = $tms_customer_row['customer_name'];
		    				$holu_customer_id = $tms_customer_row['customer_id'];
		    				$holu_router_types_id = $tms_customer_row['router_type_id'];

		    				$holu_equipment = get_col('tms_router_types', 'name', 'id', $holu_router_types_id);


		    			}

		    		}break;
		    		
		    		default:{

		    		}break;
		    	}

		    	if(1>0){

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fa fa-plus"></i> Add Income</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_income.php" method="POST" enctype="multipart/form-data">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		            <input type="hidden" name="flag_operation" id="flag_operation" value="approve_tms_income_request"/>

		            <input type="hidden" name="tms_customers_id" id="tms_customers_id" value="<?php echo $tms_customers_id; ?>"/>
		            <input type="hidden" name="request_type" id="request_type" value="<?php echo $request_type; ?>"/>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="province">Province</label>
		              <div class="col-sm-6">
		                <select id="province" class="form-control" required disabled="disabled">
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_province_option($holu_province); ?>
		                </select>
		                <input type="hidden" name="province" value="<?php echo $holu_province; ?>" />
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
		              <div class="col-sm-6">
		                <select id="categories_id" class="form-control" required disabled="disabled">
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_category_option("income", $holu_categories_id); ?>
		                </select>
		                <input type="hidden" name="categories_id" value="<?php echo $holu_categories_id; ?>" />
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="sub_categories_id">Sub Category</label>
		              <div class="col-sm-6">
		                <select id="sub_categories_id" class="form-control" required disabled="disabled">
		                	<?php echo get_sub_category_option($holu_sub_categories_id, $holu_categories_id); ?>
		                </select>
		                <input type="hidden" name="sub_categories_id" value="<?php echo $holu_sub_categories_id; ?>" />
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="income_date">Income Date</label>
		              <div class="col-sm-6">
		                <input type="text" id="income_date" name="income_date" class="form-control date_picker" placeholder="Pick a date..." required value="<?php echo date("Y-m-d"); ?>">
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="income_amount">Amount</label>
		              <div class="col-sm-6">
		                <input type="number" 
		                       step="0.01" 
		                       id="income_amount" 
		                       name="income_amount" 
		                       class="form-control" 
		                       placeholder="Type here..." 
		                       required 
		                       autocomplete="off"
		                       value="<?php echo $holu_cost; ?>">
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="currency">Currency</label>
		              <div class="col-sm-6">
		                <select id="currency" name="currency" class="form-control" required onchange="specify_rate();">
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_currency_option($holu_currency); ?>
		                </select>
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="income_attachment">Attachment</label>
		              <div class="col-sm-6">
		                <input type="file" id="income_attachment" name="income_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
		              </div>
		            </div>

		            

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="customer_name">Customer Name</label>
		              <div class="col-sm-6">
		                <input type="text" step="0.01" id="customer_name" name="customer_name" class="form-control" placeholder="Type here..." required value="<?php echo $holu_customer_name; ?>" readonly>
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="customer_id">Customer ID</label>
		              <div class="col-sm-6">
		                <input type="text" step="0.01" id="customer_id" name="customer_id" class="form-control" placeholder="Type here..." required value="<?php echo $holu_customer_id; ?>" readonly>
		              </div>
		            </div>

		            <?php
		            if($request_type=='Internet Cost'){
		            ?>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="package">Package</label>
		              <div class="col-sm-6">
		                <input type="text" id="package" name="package" class="form-control" placeholder="Type here..." required value="<?php echo $holu_package; ?>" readonly>
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="start_date">Start Date</label>
		              <div class="col-sm-6">
		                <input type="text" id="start_date" name="start_date" class="form-control" placeholder="Pick a date..." required value="<?php echo $holu_start_date; ?>" readonly>
		              </div>
		            </div>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="end_date">End Date</label>
		              <div class="col-sm-6">
		                <input type="text" id="end_date" name="end_date" class="form-control date_picker" placeholder="Pick a date..." required>
		              </div>
		            </div>

		            <?php

	            	}else{
	            	?>
	            	<div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="equipment">Equipment</label>
		              <div class="col-sm-6">
		                <input type="text" id="equipment" name="equipment" class="form-control" placeholder="Pick a date..." required value="<?php echo $holu_equipment; ?>" readonly>
		              </div>
		            </div>
	            	<?php
	            	}
		            ?>

		            <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="description">Description</label>
		              <div class="col-sm-6">
		                <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl" required></textarea>
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

	      case "delete_income_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Income</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_income.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_income"/>

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
	        <h4 class="modal-title" id="add_incomeTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal" role="form" action="list_income.php" method="GET">

	        	<div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="province">Province</label>
	            <div class="col-sm-6">
	              <select id="province" name="province" class="form-control" data-branch-target="branch" data-branch-value="0" onchange="get_branch_option(this.value, this.getAttribute('data-branch-value') || '0', this.getAttribute('data-branch-target') || 'branch', this); this.setAttribute('data-branch-value', '0');">
	              	<option selected value="">Select an option</option>
	              	<?php 
	              	  echo get_province_option(0);
	              	?>
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
	            <label class="col-sm-3 col-form-label" for="income_date">Income Date</label>
	            <div class="col-sm-3">
	              <input type="text" id="from_income_date" name="from_income_date" class="form-control date_picker" placeholder="From">
	            </div>
	            <div class="col-sm-3">
	              <input type="text" id="to_income_date" name="to_income_date" class="form-control date_picker" placeholder="To">
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
	            <div class="col-sm-6">
	              <select id="categories_id" name="categories_id" class="form-control" 
	              				onchange="get_sub_category_option('0', this.value, 'sub_categories_id2');">
	              	<option selected hidden value="">Select an option</option>
	              	<?php echo get_category_option("income", "0"); ?>
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
	            <label class="col-sm-3 col-form-label" for="income_customer_name">Customer Name</label>
	            <div class="col-sm-6">
	              <input type="text" id="income_customer_name" name="income_customer_name" class="form-control" placeholder="Type here...">
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="income_customer_id">Customer ID</label>
	            <div class="col-sm-6">
	              <input type="text" 
	              			id="income_customer_id" 
	              			name="income_customer_id" 
	              			class="form-control" 
	              			autocomplete="off" 
	              			placeholder="Type here...">
	            </div>
	          </div>

	          <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="income_currency">Currency</label>
              <div class="col-sm-6">
                <select id="income_currency" name="income_currency" class="form-control">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_currency_option('0'); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="income_amount">Amount</label>
              <div class="col-sm-6">
                <input type="text" 
                			 id="income_amount" 
                			 name="income_amount" 
                			 class="form-control" 
                			 placeholder="Type here..." 
                			 value=""
                			 autocomplete="off" />
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

	      case "filter_table_report_tms_income_request":

		    	
        ?>
      	<div class="modal-header">
	        <h4 class="modal-title" id="add_incomeTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal" role="form" action="report_tms_income_request.php" method="GET">

	        	

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="filter_customer_name">Customer Name</label>
	            <div class="col-sm-6">
	              <input type="text" id="filter_customer_name" name="filter_customer_name" class="form-control" placeholder="Type here...">
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

	      $income_attachment_sq = $db->prepare(
	      "SELECT *
	      FROM `attachments`
	      WHERE deleted='0'
	      AND type='Income Attachment'
	      AND reference_id=:data_id"
	      );

	      $income_attachment_sqx = $income_attachment_sq->execute([
	      	'data_id'=>$data_id
	      ]);

		    	
        ?>
      	<div class="modal-header">
	        <h4 class="modal-title" id="add_incomeTitle"><i class="far fa-file-image"></i> View Attachment</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <div class="row filterable-content">

	        	<?php

	        	if($income_attachment_sq->rowCount()>0){
	        		while($income_attachment_row = $income_attachment_sq->fetch()){
								holu_render_attachment_preview($income_attachment_row['path']);
	        		}
	        	}else{
								holu_render_no_attachment();
	        	}

	        	?>
        	</div>
	      </div>
        <?php
	      break;

	      case "view_full_info":

	      	
	      	$old_data_rows = [];
	      	$new_data_rows = [];

	      	$transaction_edition_sq = $db->prepare("SELECT old_data, new_data FROM `transaction_editions` WHERE reference_type='Income' AND reference_id=:data_id ORDER BY id DESC LIMIT 1");
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
                <a href="#additional_info" data-toggle="tab" aria-expanded="false" class="nav-link">
                  <span class="d-inline-block d-sm-none"><i class="fas fa-home"></i></span>
                  <span class="d-none d-sm-inline-block">Additional Info</span>   
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

				      <div class="tab-pane fade show" id="additional_info">
			        	<div class="item form-group"  >
				          <label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
				          <div class="col-md-12 col-sm-12 col-xs-12">
				          	
				            <div class="table-responsive">
		                  <table class="table table-bordered table-sm mb-0">
		                    <thead>
		                      <tr>
		                        <th colspan="2" class="text-center">Additional Info</th>
		                      </tr>
		                    </thead>
		                    <tbody>
		                    	<?php
						          		$additional_information_sq = $db->prepare("SELECT * FROM `additional_informations` WHERE reference_type='Income' AND reference_id=:data_id AND deleted='0'");
										    	$additional_information_sqx = $additional_information_sq->execute([
										    		'data_id'=>$data_id
										    	]);

										    	if($additional_information_sq->rowCount()>0){
										    		while($additional_information_row = $additional_information_sq->fetch()){


						          			?>
		                      		<tr>
				                        <th><?php echo $additional_information_row['key_info']; ?></th>
				                        <td><?php echo $additional_information_row['value_info']; ?></td>
				                      </tr>
				                      </tr>
		                      	<?php
		                      	}
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
				          		FROM `incomes`
				          		WHERE id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Edition' AS operation_type
				          		FROM `transaction_editions`
				          		WHERE reference_type='Income'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Deletion' AS operation_type
				          		FROM `transaction_deletions`
				          		WHERE reference_type='Income'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		markup_type AS operation_type
				          		FROM `markups`
				          		WHERE reference_type='Income'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Print' AS operation_type
				          		FROM `invoices`
				          		WHERE reference_type='Income'
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
	        <form class="form-horizontal parsley-form" role="form" action="controller_income.php" method="POST" enctype="multipart/form-data">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_attachment"/>

            <input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="income_attachment">Attachment</label>
              <div class="col-sm-6">
                <input type="file" id="income_attachment" name="income_attachment[]" class="form-control" placeholder="Type here..." multiple accept=".jpg, .jpeg, .png">
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

  			case "add_income":{

  				if(check_duplicate_income($_POST)==0){

  					$province = holu_escape($_POST['province']);
					$branch = holu_escape($_POST['branch']);
				    $sub_categories_id = holu_escape($_POST['sub_categories_id']);
				    $income_date = holu_escape($_POST['income_date']);
				    $currency = holu_escape($_POST['currency']);
				    $description = holu_escape($_POST['description']);

				    $income_amount = holu_escape($_POST['income_amount']);
		  			$income_percentage = holu_escape($_POST['income_percentage']);
				    $percentage = ((float)$income_percentage / 100) * (float)$income_amount;

				    $sub_cat_id = $db->prepare(
				    		"SELECT 
						    id
						    FROM `sub_categories`
						    WHERE sub_category_name LIKE ?
				      	LIMIT 1");

						$sub_cat_id->execute(['Currency fluctuations']);
						$sub_cat_id = $sub_cat_id->fetchColumn();

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
				    if(isset($_POST['equipment_price'])){
				    	array_push($key_infos, 'Equipment Price');
				    	array_push($value_infos, holu_escape($_POST['equipment_price']));
				    }
				    if(!empty($_POST['other_services'])){
				    	array_push($key_infos, 'Other Services');
				    	array_push($value_infos, holu_escape($_POST['other_services']));
				    }
				    if(!empty($_POST['vendor_name'])){
				    	array_push($key_infos, 'Vendor Name');
				    	array_push($value_infos, holu_escape($_POST['vendor_name']));
				    }
				    if(!empty($_POST['vendor_id'])){
				    	array_push($key_infos, 'Vendor ID');
				    	array_push($value_infos, holu_escape($_POST['vendor_id']));
				    }
				    if(!empty($_POST['employee_name'])){
				    	array_push($key_infos, 'Employee Name');
				    	array_push($value_infos, holu_escape($_POST['employee_name']));
				    }
				    if(!empty($_POST['employee_id'])){
				    	array_push($key_infos, 'Employee ID');
				    	array_push($value_infos, holu_escape($_POST['employee_id']));
				    }
				    $num_income_sq = $db->prepare(
				    	"SELECT 
				    	count(id) AS num_income
				    	FROM `incomes`
				    	WHERE province=:province AND branch=:branch
		      		LIMIT 1"
				    );
		
				    $num_income_sqx = $num_income_sq->execute([
				    	'province'=>$province,
				    	'branch'=>$branch
				    ]);
		
				    $num_income_row = $num_income_sq->fetch();
				    $num_income = $num_income_row['num_income'];
				    $check_number = generate_check_number('income', $province, $branch, $num_income+1);
		
				    $income_iq = $db->prepare("INSERT INTO `incomes` (
				    	province, 
				    	branch,
				    	sub_categories_id, 
				    	income_date, 
				    	income_amount, 
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
				    	:income_date, 
				    	:income_amount, 
				    	:currency, 
				    	:check_number, 
				    	:description, 
				    	:holu_date, 
				    	:holu_time,
				    	:holu_users_id
				    )");
		
				    $income_iqx = $income_iq->execute([
				    	'province'=>$province,
				    	'branch'=>$branch,
				    	'sub_categories_id'=>$sub_categories_id,
				    	'income_date'=>$income_date,
				    	'income_amount'=>$income_amount,
				    	'currency'=>$currency,
				    	'check_number'=>$check_number,
				    	'description'=>$description,
				    	'holu_date'=>$holu_date,
				    	'holu_time'=>$holu_time,
				    	'holu_users_id'=>$holu_users_id
				    ]);
		
				    $incomes_id = $db->lastInsertId();
		
				    for ($i=0; $i<sizeof($key_infos); $i++) {
		
				    	$key_info = holu_escape($key_infos[$i]);
				    	$value_info = holu_escape($value_infos[$i]);

				    	$additional_information_sq = $db->prepare(
				    		"SELECT id
				    		FROM `additional_informations`
				    		WHERE reference_type='Income'
				    		AND reference_id=:incomes_id
				    		AND key_info=:key_info
				    		AND deleted='0'"
				    	);

				    	$additional_information_sqx = $additional_information_sq->execute([
				    		'incomes_id'=>$incomes_id,
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
						    	'reference_type'=>'Income',
						    	'reference_id'=>$incomes_id,
						    	'key_info'=>$key_info,
						    	'value_info'=>$value_info,
						    	'holu_date'=>$holu_date,
						    	'holu_time'=>$holu_time,
						    	'holu_users_id'=>$holu_users_id
						    ]);
				    	}
				    	
				    }

				    $ai_sq = $db->query("SELECT * FROM additional_informations WHERE deleted='0' AND 
				    																	reference_type='Income' 
				    																	AND reference_id='$incomes_id'");
						if($ai_sq->rowCount()>0){
							$ai_array = [];
							while($ai_row = $ai_sq->fetch()){
								$ai_array[$ai_row['key_info']] = $ai_row['value_info'];
							}
							$ai_array = json_encode($ai_array, JSON_UNESCAPED_UNICODE);

							$income_uq = $db->query("UPDATE incomes SET additional_informations='$ai_array' 
													WHERE id='$incomes_id'");
						}

		
				    if(!empty(array_filter($_FILES['income_attachment']['name']))){
				    	
							foreach ($_FILES['income_attachment']['name'] as $key => $value) {
						    if($_FILES['income_attachment']['size'][$key]<10485760){
						    	
							    $target_dir = "../uploads/income_attachment/".holu_encode($incomes_id);
							    if(!file_exists($target_dir)){
										mkdir($target_dir, 0777, true);
									}
									$target_file = $target_dir."/".basename($_FILES['income_attachment']['name'][$key]);
									$file_upload = move_uploaded_file($_FILES['income_attachment']['tmp_name'][$key], $target_file);
									$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
									$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
										'type'=>'Income Attachment',
										'reference_id'=>$incomes_id,
										'path'=>$target_file,
										'insertion_date'=>$holu_date,
										'insertion_time'=>$holu_time,
										'users_id'=>$holu_users_id
									]);
						    }
						  }
						}

						// Insert the percentage

						$num_income_sq = $db->prepare(
							"SELECT 
							count(id) AS num_income
							FROM `incomes`
							WHERE province=:province AND branch=:branch
						LIMIT 1"
						);
			
						$num_income_sqx = $num_income_sq->execute([
							'province'=>$province,
							'branch'=>$branch
						]);
			
						$num_income_row = $num_income_sq->fetch();
						$num_income = $num_income_row['num_income'];
						$check_number = generate_check_number('income', $province, $branch, $num_income+1);	
						
						if(!empty($income_percentage) && isset($income_percentage)){
							
							$income_iq2 = $db->prepare("INSERT INTO `incomes` (
					    	province, 
					    	branch,
					    	sub_categories_id, 
					    	income_date, 
					    	income_amount, 
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
					    	:income_date, 
					    	:income_amount, 
					    	:currency, 
					    	:check_number, 
					    	:description, 
					    	:holu_date, 
					    	:holu_time,
					    	:holu_users_id
					    )");
			
					    $income_iqx2 = $income_iq->execute([
					    	'province'=>$province,
					    	'branch'=>$branch,
					    	'sub_categories_id'=>$sub_cat_id,
					    	'income_date'=>$income_date,
					    	'income_amount'=>$percentage,
					    	'currency'=>$currency,
					    	'check_number'=>$check_number,
					    	'description'=>$description,
					    	'holu_date'=>$holu_date,
					    	'holu_time'=>$holu_time,
					    	'holu_users_id'=>$holu_users_id
					    ]);
			
					    $incomes_id2 = $db->lastInsertId();

					    for ($i=0; $i<sizeof($key_infos); $i++) {
		
				    	$key_info = holu_escape($key_infos[$i]);
				    	$value_info = holu_escape($value_infos[$i]);

				    	$additional_information_sq = $db->prepare(
				    		"SELECT id
				    		FROM `additional_informations`
				    		WHERE reference_type='Income'
				    		AND reference_id=:incomes_id
				    		AND key_info=:key_info
				    		AND deleted='0'"
				    	);

				    	$additional_information_sqx = $additional_information_sq->execute([
				    		'incomes_id'=>$incomes_id2,
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
						    	'reference_type'=>'Income',
						    	'reference_id'=>$incomes_id2,
						    	'key_info'=>$key_info,
						    	'value_info'=>$value_info,
						    	'holu_date'=>$holu_date,
						    	'holu_time'=>$holu_time,
						    	'holu_users_id'=>$holu_users_id
						    ]);
				    	}
				    	
				    }

				    $ai_sq = $db->query("SELECT * FROM additional_informations WHERE deleted='0' AND 
				    																	reference_type='Income' 
				    																	AND reference_id='$incomes_id2'");
						if($ai_sq->rowCount()>0){
							$ai_array = [];
							while($ai_row = $ai_sq->fetch()){
								$ai_array[$ai_row['key_info']] = $ai_row['value_info'];
							}
							$ai_array = json_encode($ai_array, JSON_UNESCAPED_UNICODE);

							$income_uq = $db->query("UPDATE incomes SET additional_informations='$ai_array' 
													WHERE id='$incomes_id2'");
						}
							
						}
		
				    if($income_iqx){
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
  			
	  		}break;

  			case "edit_income":{
  			
	  			if(check_duplicate_income($_POST)==0){

	  				track_editions('edit_income', ['incomes_id'=>$_POST['data_id'], 'data_array'=>$_POST]);
	
		  			$data_id = holu_escape(holu_decode($_POST['data_id']));
				    $branch = holu_escape($_POST['branch']);
				    $sub_categories_id = holu_escape($_POST['sub_categories_id']);
		  			$income_amount = holu_escape($_POST['income_amount']);
				    $income_date = holu_escape($_POST['income_date']);
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
				    if(!empty($_POST['vendor_name'])){
				    	array_push($key_infos, 'Vendor Name');
				    	array_push($value_infos, holu_escape($_POST['vendor_name']));
				    }
				    if(!empty($_POST['vendor_id'])){
				    	array_push($key_infos, 'Vendor ID');
				    	array_push($value_infos, holu_escape($_POST['vendor_id']));
				    }
				    if(!empty($_POST['employee_name'])){
				    	array_push($key_infos, 'Employee Name');
				    	array_push($value_infos, holu_escape($_POST['employee_name']));
				    }
				    if(!empty($_POST['employee_id'])){
				    	array_push($key_infos, 'Employee ID');
				    	array_push($value_infos, holu_escape($_POST['employee_id']));
				    }
	
		  			$income_uq = $db->prepare(
		  				"UPDATE `incomes` SET 
			  				branch=:branch,
			  				sub_categories_id=:sub_categories_id, 
			  				income_amount=:income_amount, 
			  				income_date=:income_date, 
			  				currency=:currency, 
			  				description=:description 
			  			WHERE id=:data_id
			  			LIMIT 1"
		  			);
	
		  			$income_uqx = $income_uq->execute([
				    	'branch'=>$branch,
				    	'sub_categories_id'=>$sub_categories_id,
				    	'income_date'=>$income_date,
				    	'income_amount'=>$income_amount,
				    	'currency'=>$currency,
				    	'description'=>$description,
				    	'data_id'=>$data_id
		  			]);

		  			$additional_information_uq = $db->prepare(
		  				"UPDATE `additional_informations` SET
		  				deleted='1'
		  				WHERE reference_type='Income'
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
				    		WHERE reference_type='Income'
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
						    	'reference_type'=>'Income',
						    	'reference_id'=>$data_id,
						    	'key_info'=>$key_info,
						    	'value_info'=>$value_info,
						    	'holu_date'=>$holu_date,
						    	'holu_time'=>$holu_time,
						    	'holu_users_id'=>$holu_users_id
						    ]);
				    	}
				    }

				    $ai_sq = $db->query("SELECT * FROM additional_informations WHERE deleted='0' AND reference_type='Income' AND reference_id='$data_id'");
						if($ai_sq->rowCount()>0){
							$ai_array = [];
							while($ai_row = $ai_sq->fetch()){
								$ai_array[$ai_row['key_info']] = $ai_row['value_info'];
							}
							$ai_array = json_encode($ai_array, JSON_UNESCAPED_UNICODE);

							$income_uq = $db->query("UPDATE incomes SET additional_informations='$ai_array' WHERE id='$data_id'");
						}
	
		  			if(!empty(array_filter($_FILES['income_attachment']['name']))){
		  				
				    	$incomes_id = $data_id;
							foreach ($_FILES['income_attachment']['name'] as $key => $value) {
						    if($_FILES['income_attachment']['size'][$key]<10485760){
						    	
							    $target_dir = "../uploads/income_attachment/".holu_encode($incomes_id);
							    if(!file_exists($target_dir)){
										mkdir($target_dir, 0777, true);
									}
									$target_file = $target_dir."/".basename($_FILES['income_attachment']['name'][$key]);
									$file_upload = move_uploaded_file($_FILES['income_attachment']['tmp_name'][$key], $target_file);
									$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
									$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
										'type'=>'Income Attachment',
										'reference_id'=>$incomes_id,
										'path'=>$target_file,
										'insertion_date'=>$holu_date,
										'insertion_time'=>$holu_time,
										'users_id'=>$holu_users_id
									]);
						    }
						  }
						} 
	
		  			if($income_uqx){
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
	
	  		}break;

	  		case "approve_tms_income_request":{

  				

  				if(check_duplicate_income($_POST)==0){
  					$province = holu_escape($_POST['province']);
					$branch = holu_escape($_POST['branch']);
				    $sub_categories_id = holu_escape($_POST['sub_categories_id']);
				    $income_date = holu_escape($_POST['income_date']);
		  			$income_amount = holu_escape($_POST['income_amount']);
				    $currency = holu_escape($_POST['currency']);
				    $description = holu_escape($_POST['description']);

				    $tms_customers_id = holu_escape($_POST['tms_customers_id']);
				    $request_type = holu_escape($_POST['request_type']);
				    
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
				    if(!empty($_POST['vendor_name'])){
				    	array_push($key_infos, 'Vendor Name');
				    	array_push($value_infos, holu_escape($_POST['vendor_name']));
				    }
				    if(!empty($_POST['vendor_id'])){
				    	array_push($key_infos, 'Vendor ID');
				    	array_push($value_infos, holu_escape($_POST['vendor_id']));
				    }
				    if(!empty($_POST['employee_name'])){
				    	array_push($key_infos, 'Employee Name');
				    	array_push($value_infos, holu_escape($_POST['employee_name']));
				    }
				    if(!empty($_POST['employee_id'])){
				    	array_push($key_infos, 'Employee ID');
				    	array_push($value_infos, holu_escape($_POST['employee_id']));
				    }
				    $num_income_sq = $db->prepare(
				    	"SELECT 
				    	count(id) AS num_income
				    	FROM `incomes`
				    	WHERE province=:province AND branch=:branch
		      		LIMIT 1"
				    );
		
				    $num_income_sqx = $num_income_sq->execute([
				    	'province'=>$province,
				    	'branch'=>$branch
				    ]);
		
				    $num_income_row = $num_income_sq->fetch();
				    $num_income = $num_income_row['num_income'];
				    $check_number = generate_check_number('income', $province, $branch, $num_income+1);
		
				    $income_iq = $db->prepare("INSERT INTO `incomes` (
				    	province, 
				    	branch,
				    	sub_categories_id, 
				    	income_date, 
				    	income_amount, 
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
				    	:income_date, 
				    	:income_amount, 
				    	:currency, 
				    	:check_number, 
				    	:description, 
				    	:holu_date, 
				    	:holu_time,
				    	:holu_users_id
				    )");
		
				    $income_iqx = $income_iq->execute([
				    	'province'=>$province,
				    	'branch'=>$branch,
				    	'sub_categories_id'=>$sub_categories_id,
				    	'income_date'=>$income_date,
				    	'income_amount'=>$income_amount,
				    	'currency'=>$currency,
				    	'check_number'=>$check_number,
				    	'description'=>$description,
				    	'holu_date'=>$holu_date,
				    	'holu_time'=>$holu_time,
				    	'holu_users_id'=>$holu_users_id
				    ]);
		
				    $incomes_id = $db->lastInsertId();
		
				    for ($i=0; $i<sizeof($key_infos); $i++) {
		
				    	$key_info = holu_escape($key_infos[$i]);
				    	$value_info = holu_escape($value_infos[$i]);

				    	$additional_information_sq = $db->prepare(
				    		"SELECT id
				    		FROM `additional_informations`
				    		WHERE reference_type='Income'
				    		AND reference_id=:incomes_id
				    		AND key_info=:key_info
				    		AND deleted='0'"
				    	);

				    	$additional_information_sqx = $additional_information_sq->execute([
				    		'incomes_id'=>$incomes_id,
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
						    	'reference_type'=>'Income',
						    	'reference_id'=>$incomes_id,
						    	'key_info'=>$key_info,
						    	'value_info'=>$value_info,
						    	'holu_date'=>$holu_date,
						    	'holu_time'=>$holu_time,
						    	'holu_users_id'=>$holu_users_id
						    ]);
				    	}
				    	
				    }


				    $ai_sq = $db->query("SELECT * FROM additional_informations WHERE deleted='0' AND reference_type='Income' AND reference_id='$incomes_id'");
						if($ai_sq->rowCount()>0){
							$ai_array = [];
							while($ai_row = $ai_sq->fetch()){
								$ai_array[$ai_row['key_info']] = $ai_row['value_info'];
							}
							$ai_array = json_encode($ai_array, JSON_UNESCAPED_UNICODE);

							$income_uq = $db->query("UPDATE incomes SET additional_informations='$ai_array' WHERE id='$incomes_id'");
						}

		
				    if(!empty(array_filter($_FILES['income_attachment']['name']))){
				    	
							foreach ($_FILES['income_attachment']['name'] as $key => $value) {
						    if($_FILES['income_attachment']['size'][$key]<10485760){
						    	
							    $target_dir = "../uploads/income_attachment/".holu_encode($incomes_id);
							    if(!file_exists($target_dir)){
										mkdir($target_dir, 0777, true);
									}
									$target_file = $target_dir."/".basename($_FILES['income_attachment']['name'][$key]);
									$file_upload = move_uploaded_file($_FILES['income_attachment']['tmp_name'][$key], $target_file);
									$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
									$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
										'type'=>'Income Attachment',
										'reference_id'=>$incomes_id,
										'path'=>$target_file,
										'insertion_date'=>$holu_date,
										'insertion_time'=>$holu_time,
										'users_id'=>$holu_users_id
									]);
						    }
						  }
						}

						$income_request_approval_iq = $db->prepare("INSERT INTO `income_request_approvals` (
				    	tms_customers_id,
				    	incomes_id,
				    	request_type,
				    	insertion_date, 
				    	insertion_time, 
				    	users_id
				    ) VALUES (
				    	:tms_customers_id, 
				    	:incomes_id, 
				    	:request_type, 
				    	:holu_date, 
				    	:holu_time,
				    	:holu_users_id
				    )");
		
				    $income_request_approval_iqx = $income_request_approval_iq->execute([
				    	'tms_customers_id'=>$tms_customers_id,
				    	'incomes_id'=>$incomes_id,
				    	'request_type'=>$request_type,
				    	'holu_date'=>$holu_date,
				    	'holu_time'=>$holu_time,
				    	'holu_users_id'=>$holu_users_id
				    ]);
		
				    if($income_iqx){
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
  			
	  			
	
	  		}break;

  			case "delete_income":

  			track_deletions('delete_income', ['incomes_id'=>$_POST['data_id']]);
  			

  			$data_id = holu_escape(holu_decode($_POST['data_id']));

  			$income_dq = $db->prepare("UPDATE `incomes` SET 
  				deleted='1' 
  			WHERE id=:data_id
  			LIMIT 1");

  			$income_dqx = $income_dq->execute([
  				'data_id'=>$data_id
  			]);

  			if($income_dqx){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}

  			break;

	  		case 'add_attachment':{
	  			$data_id = holu_escape(holu_decode($_POST['data_id']));
	  			if(!empty(array_filter($_FILES['income_attachment']['name']))){
		  				
			    	$incomes_id = $data_id;
						foreach ($_FILES['income_attachment']['name'] as $key => $value) {
					    if($_FILES['income_attachment']['size'][$key]<10485760){
					    	
						    $target_dir = "../uploads/income_attachment/".holu_encode($incomes_id);
						    if(!file_exists($target_dir)){
									mkdir($target_dir, 0777, true);
								}
								$target_file = $target_dir."/".basename($_FILES['income_attachment']['name'][$key]);
								$file_upload = move_uploaded_file($_FILES['income_attachment']['tmp_name'][$key], $target_file);
								$file_insertion_entry_uq = $db->prepare("INSERT INTO `attachments` (type, reference_id, path, insertion_date, insertion_time, users_id) VALUES (:type, :reference_id, :path, :insertion_date, :insertion_time, :users_id)");
								$file_insertion_entry_uqx = $file_insertion_entry_uq->execute([
									'type'=>'Income Attachment',
									'reference_id'=>$incomes_id,
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
