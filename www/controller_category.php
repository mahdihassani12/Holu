<?php

  include("../lib/_configuration.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape($_POST['data_id']);
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

		    case "add_category_form":

		    	
        ?>

      	<div class="modal-header">
	        <h4 class="modal-title" id="add_categoryTitle"><i class="fa fa-plus"></i> Add Category</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal " role="form" action="controller_category.php" method="POST">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

	        	<input type="hidden" name="flag_operation" id="flag_operation" value="add_category"/>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="category_type">Category Type</label>
	            <div class="col-sm-6">
	              <select id="category_type" name="category_type" class="form-control" required>
	              	<option selected hidden value="">Select an option</option>
	              	<option value="Income">Income</option>
	              	<option value="Expense">Expense</option>
	              	<option value="Purchase">Purchase</option>
	              </select>
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="category_name">Category Name</label>
	            <div class="col-sm-6">
	              <input type="text" id="category_name" name="category_name" class="form-control" placeholder="Type here..." required>
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

	      case "edit_category_form":

		    	$category_sq = $db->query("SELECT * FROM `categories` WHERE deleted='0' AND id='$data_id' LIMIT 1");

		    	if($category_sq->rowCount()>0){
		    		$category_row = $category_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Category</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_category.php" method="POST">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_category"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo $data_id; ?>"/>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="category_type">Category Type</label>
			            <div class="col-sm-6">
			              <select id="category_type" name="category_type" class="form-control" required>
			              	<option selected hidden value="">Select an option</option>
			              	<option <?php echo $category_row['category_type']=="Income"?"selected":""; ?> value="Income">Income</option>
			              	<option <?php echo $category_row['category_type']=="Expense"?"selected":""; ?> value="Expense">Expense</option>
			              	<option <?php echo $category_row['category_type']=="Purchase"?"selected":""; ?> value="Purchase">Purchase</option>
			              </select>
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="category_name">Category Name</label>
			            <div class="col-sm-6">
			              <input type="text" id="category_name" name="category_name" class="form-control" placeholder="Type here..." required value="<?php echo $category_row['category_name']; ?>">
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Description</label>
			            <div class="col-sm-6">
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..."><?php echo $category_row['description']; ?></textarea>
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

	      case "delete_category_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Category</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_category.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_category"/>

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

  			case "add_category":

  			$category_type = holu_escape($_POST['category_type']);
		    $category_name = holu_escape($_POST['category_name']);
		    $description = holu_escape($_POST['description']);
		    $category_iq = $db->query("INSERT INTO `categories` (category_type, category_name, description, insertion_date, insertion_time, users_id) VALUES ('$category_type','$category_name','$description','$holu_date','$holu_time','$holu_users_id')");

		    if($category_iq){
		      header("location:list_category.php?success");
		      exit();
		    }else{
		      header("location:list_category.php?error");
		      exit();
		    }


  			break;

  			case "edit_category":

  			$data_id = holu_escape($_POST['data_id']);
  			$category_type = holu_escape($_POST['category_type']);
  			$category_name = holu_escape($_POST['category_name']);
  			$description = holu_escape($_POST['description']);

  			$category_uq = $db->query("UPDATE `categories` SET category_type='$category_type', category_name='$category_name', description='$description', users_id='$holu_users_id' WHERE id='$data_id' LIMIT 1");

  			if($category_uq){
  				header("location:list_category.php?success");
  				exit();
  			}else{
  				header("location:list_category.php?error");
  				exit();
  			}


  			break;

  			case "delete_category":

  			$data_id = holu_escape($_POST['data_id']);

  			$category_dq = $db->query("UPDATE `categories` SET deleted='1' WHERE id='$data_id' LIMIT 1");

  			if($category_dq){
  				header("location:list_category.php?success");
  				exit();
  			}else{
  				header("location:list_category.php?error");
  				exit();
  			}


  			break;



  			default:

  		}
  	}
  }
?>

