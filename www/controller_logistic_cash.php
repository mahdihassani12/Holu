<?php

  include("../lib/_configuration.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape($_POST['data_id']);
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

		    case "add_logistic_cash_form":

        ?>

      	<div class="modal-header">
	        <h4 class="modal-title" id="add_logistic_cashTitle"><i class="fa fa-plus"></i> Add Logistic Cash</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal " role="form" action="controller_logistic_cash.php" method="POST">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

	        	<input type="hidden" name="flag_operation" id="flag_operation" value="add_logistic_cash"/>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="name">Name</label>
	            <div class="col-sm-6">
	              <input type="text" id="name" name="name" class="form-control" placeholder="Type here..." required>
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

	      case "edit_logistic_cash_form":

		    	$logistic_cash_sq = $db->query("SELECT * FROM `logistic_cashes` WHERE deleted='0' AND id='$data_id' LIMIT 1");

		    	if($logistic_cash_sq->rowCount()>0){
		    		$logistic_cash_row = $logistic_cash_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Logistic Cash</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_logistic_cash.php" method="POST">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_logistic_cash"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo $data_id; ?>"/>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="name">Name</label>
			            <div class="col-sm-6">
			              <input type="text" id="name" name="name" class="form-control" placeholder="Type here..." required value="<?php echo $logistic_cash_row['name']; ?>">
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Description</label>
			            <div class="col-sm-6">
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..."><?php echo $logistic_cash_row['description']; ?></textarea>
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

	      case "delete_logistic_cash_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Logistic Cash</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_logistic_cash.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_logistic_cash"/>

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

  			case "add_logistic_cash":

		    $name = holu_escape($_POST['name']);
		    $description = holu_escape($_POST['description']);
		    $logistic_cash_iq = $db->query("INSERT INTO `logistic_cashes` ( name, description, insertion_date, insertion_time, users_id) VALUES ('$name','$description','$holu_date','$holu_time','$holu_users_id')");

		    if($logistic_cash_iq){
		      header("location:list_logistic_cash.php?success");
		      exit();
		    }else{
		      header("location:list_logistic_cash.php?error");
		      exit();
		    }


  			break;

  			case "edit_logistic_cash":

  			$data_id = holu_escape($_POST['data_id']);
  			$name = holu_escape($_POST['name']);
  			$description = holu_escape($_POST['description']);

  			$logistic_cash_uq = $db->query("UPDATE `logistic_cashes` SET name='$name', description='$description' WHERE id='$data_id' LIMIT 1");

  			if($logistic_cash_uq){
  				header("location:list_logistic_cash.php?success");
  				exit();
  			}else{
  				header("location:list_logistic_cash.php?error");
  				exit();
  			}


  			break;

  			case "delete_logistic_cash":

  			$data_id = holu_escape($_POST['data_id']);

  			$logistic_cash_dq = $db->query("UPDATE `logistic_cashes` SET deleted='1' WHERE id='$data_id' LIMIT 1");

  			if($logistic_cash_dq){
  				header("location:list_logistic_cash.php?success");
  				exit();
  			}else{
  				header("location:list_logistic_cash.php?error");
  				exit();
  			}


  			break;



  			default:

  		}
  	}
  }
?>

