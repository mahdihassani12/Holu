<?php

  include("../lib/_configuration.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape($_POST['data_id']);
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

		    case "add_cash_reservation_form":

        ?>

      	<div class="modal-header">
	        <h4 class="modal-title" id="add_cash_reservationTitle"><i class="fa fa-filter"></i> Add Cash Reservation</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal " role="form" action="controller_cash_reservation.php" method="POST">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

	        	<input type="hidden" name="flag_operation" id="flag_operation" value="add_cash_reservation"/>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="logistic_cashes_id">Logistic Cash</label>
	            <div class="col-sm-6">
	              <select id="logistic_cashes_id" name="logistic_cashes_id" class="form-control" required>
	              	<?php echo get_logistic_cash_option("0"); ?>
	              </select>
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="reservation_date">Reservation Date</label>
	            <div class="col-sm-6">
	              <input type="text" id="reservation_date" name="reservation_date" class="form-control date_picker" placeholder="Type here..." required>
	            </div>
	          </div>

	          <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="reservation_amount">Reservation Amount</label>
	            <div class="col-sm-6">
	              <input type="number" id="reservation_amount" name="reservation_amount" class="form-control" placeholder="Type here..." required>
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

	      case "edit_cash_reservation_form":

		    	$cash_reservation_sq = $db->query("SELECT * FROM `cash_reservations` WHERE deleted='0' AND id='$data_id' LIMIT 1");

		    	if($cash_reservation_sq->rowCount()>0){
		    		$cash_reservation_row = $cash_reservation_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Cash Reservation</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_cash_reservation.php" method="POST">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_cash_reservation"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo $data_id; ?>"/>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="logistic_cashes_id">Logistic Cash</label>
			            <div class="col-sm-6">
			              <select id="logistic_cashes_id" name="logistic_cashes_id" class="form-control" required>
			              	<?php echo get_logistic_cash_option($cash_reservation_row['logistic_cashes_id']); ?>
			              </select>
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="reservation_date">Reservation Date</label>
			            <div class="col-sm-6">
			              <input type="text" id="reservation_date" name="reservation_date" class="form-control date_picker" placeholder="Type here..." required value="<?php echo $cash_reservation_row['reservation_date']; ?>">
			            </div>
			          </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="reservation_amount">Reservation Amount</label>
			            <div class="col-sm-6">
			              <input type="text" id="reservation_amount" name="reservation_amount" class="form-control" placeholder="Type here..." required value="<?php echo $cash_reservation_row['reservation_amount']; ?>">
			            </div>
			          </div>

			          <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="currency">Currency</label>
		              <div class="col-sm-6">
		                <select id="currency" name="currency" class="form-control" required>
		                  <option selected hidden value="">Select an option</option>
		                  <?php echo get_currency_option($cash_reservation_row['currency']); ?>
		                </select>
		              </div>
		            </div>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Description</label>
			            <div class="col-sm-6">
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..."><?php echo $cash_reservation_row['description']; ?></textarea>
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

	      case "filter_table":
		    	
        ?>
      	<div class="modal-header">
	        <h4 class="modal-title" id="add_incomeTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal" role="form" action="list_cash_reservation.php" method="GET">

	        	<div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="logistic_cashes_id">Logistic Cash</label>
	            <div class="col-sm-6">
	              <select id="logistic_cashes_id" name="logistic_cashes_id" class="form-control" >
	              	<?php echo get_logistic_cash_option("0"); ?>
	              </select>
	            </div>
	          </div>

	        	<!-- <div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="province">Province</label>
	            <div class="col-sm-6">
	              <select id="province" name="province" class="form-control">
	              	<option selected hidden value="">Select an option</option>
	              	<?php //echo get_province_option('0'); ?>
	              </select>
	            </div>
	          </div> -->

	        	<div class="form-group row">
	            <label class="col-sm-3 col-form-label" for="reservation_date">Reservation Date</label>
	            <div class="col-sm-3">
	              <input type="text" id="from_reservation_date" name="from_reservation_date" class="form-control date_picker" placeholder="From">
	            </div>
	            <div class="col-sm-3">
	              <input type="text" id="to_reservation_date" name="to_reservation_date" class="form-control date_picker" placeholder="To">
	            </div>
	          </div>

	          <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="currency">Currency</label>
              <div class="col-sm-6">
                <select id="currency" name="currency" class="form-control">
                  <option selected hidden value="">Select an option</option>
                  <?php echo get_currency_option('0'); ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="amount">Amount</label>
              <div class="col-sm-6">
                <input type="text" id="amount" name="amount" class="form-control" placeholder="Type here..." value="">
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

	      	case "delete_cash_reservation_form":

	      ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Cash Reservation</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_cash_reservation.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_cash_reservation"/>

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

	      case "approve_cash_reservation_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-circle"></i> Approve Cash Reservation</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_cash_reservation.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="approve_cash_reservation"/>

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

  			case "add_cash_reservation":

  			$logistic_cashes_id = holu_escape($_POST['logistic_cashes_id']);
		    $reservation_date = holu_escape($_POST['reservation_date']);
		    $reservation_amount = holu_escape($_POST['reservation_amount']);
		    $currency = holu_escape($_POST['currency']);
		    $description = holu_escape($_POST['description']);

		    $cash_reservation_iq = $db->query("INSERT INTO `cash_reservations` (logistic_cashes_id, reservation_date, reservation_amount, currency, description, insertion_date, insertion_time, users_id) VALUES ('$logistic_cashes_id','$reservation_date','$reservation_amount','$currency','$description','$holu_date','$holu_time','$holu_users_id')");

		    

		    if($cash_reservation_iq){
		      header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}


  			break;

  			case "edit_cash_reservation":

  			$data_id = holu_escape($_POST['data_id']);
  			$logistic_cashes_id = holu_escape($_POST['logistic_cashes_id']);
  			$reservation_date = holu_escape($_POST['reservation_date']);
  			$reservation_amount = holu_escape($_POST['reservation_amount']);
  			$currency = holu_escape($_POST['currency']);
  			$description = holu_escape($_POST['description']);

  			$cash_reservation_uq = $db->query("UPDATE `cash_reservations` SET logistic_cashes_id='$logistic_cashes_id', reservation_date='$reservation_date', reservation_amount='$reservation_amount', currency='$currency', description='$description' WHERE id='$data_id' LIMIT 1");

  			if($cash_reservation_uq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}


  			break;

  			case "approve_cash_reservation":

  			$data_id = holu_escape($_POST['data_id']);

  			$cash_reservation_uq = $db->query("UPDATE `cash_reservations` SET is_approved='1', approve_date='$holu_date', approve_time='$holu_time', approved_by='$holu_users_id' WHERE id='$data_id' LIMIT 1");

  			if($cash_reservation_uq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}


  			break;

  			case "delete_cash_reservation":

  			$data_id = holu_escape($_POST['data_id']);

  			$cash_reservation_dq = $db->query("UPDATE `cash_reservations` SET deleted='1' WHERE id='$data_id' LIMIT 1");

  			if($cash_reservation_dq){
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."success");
  				exit();
  			}else{
  				header("location:".set_referer($_SERVER['HTTP_REFERER'])."error");
  				exit();
  			}


  			break;



  			default:

  		}
  	}
  }
?>

