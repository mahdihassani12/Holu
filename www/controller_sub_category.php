<?php

  include("../lib/_configuration.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape($_POST['data_id']);
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

		    case "add_sub_category_form":

		    	
        ?>

      	<div class="modal-header">
	        <h4 class="modal-title" id="add_sub_categoryTitle"><i class="fa fa-plus"></i> Add Sub Category</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal " role="form" action="controller_sub_category.php" method="POST">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

	        	<input type="hidden" name="flag_operation" id="flag_operation" value="add_sub_category"/>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
	            <div class="col-sm-6">
	              <select id="categories_id" name="categories_id" class="form-control" required>
	              	<?php echo get_category_option("0", "0"); ?>
	              </select>
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="sub_category_name">Sub Category Name</label>
	            <div class="col-sm-6">
	              <input type="text" id="sub_category_name" name="sub_category_name" class="form-control" placeholder="Type here..." required>
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="description">Additional Information</label>
	            <div class="col-sm-6">
	              <div style="border: 1px dashed #00b8a5 !important; padding: 10px !important;">
	              	
	              	<?php
	              		echo get_additional_information_items(array());
	              	?>

	              </div>
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="description">Description</label>
	            <div class="col-sm-6">
	              <textarea id="description" name="description" class="form-control" placeholder="Type here..."></textarea>
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

	      case "edit_sub_category_form":

		    	$sub_category_sq = $db->query("SELECT * FROM `sub_categories` WHERE deleted='0' AND id='$data_id' LIMIT 1");

		    	if($sub_category_sq->rowCount()>0){
		    		$sub_category_row = $sub_category_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Sub Category</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_sub_category.php" method="POST">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_sub_category"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo $data_id; ?>"/>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="categories_id">Category</label>
			            <div class="col-sm-6">
			              <select id="categories_id" name="categories_id" class="form-control" required>
			              	<?php echo get_category_option("0", $sub_category_row['categories_id']); ?>
			              </select>
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="sub_category_name">Sub Category Name</label>
			            <div class="col-sm-6">
			              <input type="text" id="sub_category_name" name="sub_category_name" class="form-control" placeholder="Type here..." required value="<?php echo $sub_category_row['sub_category_name']; ?>">
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Additional Information</label>
			            <div class="col-sm-6">
			              <div style="border: 1px dashed #00b8a5 !important; padding: 10px !important;">
			              	
			              	<?php
			              		$additinal_information_items_sq = $db->query(
			              			"SELECT * FROM sub_category_aiis WHERE deleted='0' AND sub_categories_id='$data_id'"
			              		);

			              		$additional_information_items = [];
			              		if($additinal_information_items_sq->rowCount()>0){
			              			while($additinal_information_items_row = $additinal_information_items_sq->fetch()){
			              				array_push($additional_information_items, $additinal_information_items_row['additional_information_items_id']);
			              			}
			              		}
			              		echo get_additional_information_items($additional_information_items);
			              	?>

			              </div>
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Description</label>
			            <div class="col-sm-6">
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..."><?php echo $sub_category_row['description']; ?></textarea>
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

	      case "delete_sub_category_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Sub Category</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_sub_category.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_sub_category"/>

		        	<input type="hidden" name="data_id" id="data_id" value="<?php echo $data_id; ?>"/>

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
			}
  	}else if($flag_request=="operation"){
  		$flag_operation = holu_escape($_POST['flag_operation']);

  		switch ($flag_operation){

  			case "add_sub_category":

  			$categories_id = holu_escape($_POST['categories_id']);
		    $sub_category_name = holu_escape($_POST['sub_category_name']);
		    $description = holu_escape($_POST['description']);
		    $sub_category_iq = $db->query("INSERT INTO `sub_categories` (categories_id, sub_category_name, description, insertion_date, insertion_time, users_id) VALUES ('$categories_id','$sub_category_name','$description','$holu_date','$holu_time','$holu_users_id')");

		    $sub_categories_id = $db->lastInsertId();

		    if(!empty($_POST['additional_information_items'])){
				
					foreach($_POST['additional_information_items'] as $additional_information_item){

						$sub_category_aii_iq = $db->query("INSERT INTO `sub_category_aiis` (sub_categories_id, additional_information_items_id, insertion_date, insertion_time, users_id) VALUES ('$sub_categories_id','$additional_information_item','$holu_date','$holu_time','$holu_users_id')");

					}

				}

		    if($sub_category_iq){
		      header("location:list_sub_category.php?success");
		      exit();
		    }else{
		      header("location:list_sub_category.php?error");
		      exit();
		    }


  			break;

  			case "edit_sub_category":

  			$data_id = holu_escape($_POST['data_id']);
  			$categories_id = holu_escape($_POST['categories_id']);
  			$sub_category_name = holu_escape($_POST['sub_category_name']);
  			$description = holu_escape($_POST['description']);

  			$sub_category_uq = $db->query("UPDATE `sub_categories` SET categories_id='$categories_id', sub_category_name='$sub_category_name', description='$description' WHERE id='$data_id' LIMIT 1");

  			$sub_category_aii_uq = $db->query("UPDATE `sub_category_aiis` SET deleted='1' WHERE sub_categories_id='$data_id'");

  			if(!empty($_POST['additional_information_items'])){
				
					foreach($_POST['additional_information_items'] as $additional_information_item){

						$sub_category_aii_iq = $db->query("INSERT INTO `sub_category_aiis` (sub_categories_id, additional_information_items_id, insertion_date, insertion_time, users_id) VALUES ('$data_id','$additional_information_item','$holu_date','$holu_time','$holu_users_id')");

					}

				}

  			if($sub_category_uq){
  				header("location:list_sub_category.php?success");
  				exit();
  			}else{
  				header("location:list_sub_category.php?error");
  				exit();
  			}


  			break;

  			case "delete_sub_category":

  			$data_id = holu_escape($_POST['data_id']);

  			$sub_category_dq = $db->query("UPDATE `sub_categories` SET deleted='1' WHERE id='$data_id' LIMIT 1");

  			if($sub_category_dq){
  				header("location:list_sub_category.php?success");
  				exit();
  			}else{
  				header("location:list_sub_category.php?error");
  				exit();
  			}


  			break;



  			default:

  		}
  	}
  }
?>

