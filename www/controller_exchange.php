<?php

  include("../lib/_configuration.php");

  if(isset($_POST['flag_request'])){
  	
  	$flag_request	= holu_escape($_POST['flag_request']);

  	if($flag_request=="modal"){

  		$modal 		= holu_escape($_POST['modal']);
  		$data_id 	= holu_escape(holu_decode($_POST['data_id']));
  		$source 	= holu_escape($_POST['source']);

  		switch ($modal) {

  			case "add_exchange_form":

        ?>

        <div class="modal-header">
	        <h4 class="modal-title"><i class="fa fa-plus"></i> Add Exchange</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal parsley-form" role="form" action="controller_exchange.php" method="POST">

	        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

            <input type="hidden" name="flag_operation" id="flag_operation" value="add_exchange"/>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="province">Province</label>
              <div class="col-sm-6">
                <select id="province" name="province" class="form-control" required data-branch-target="branch" data-branch-value="0">
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
              <label class="col-sm-3 col-form-label" for="exchange_date">Exchange Date</label>
              <div class="col-sm-6">
                <input type="text" id="exchange_date" name="exchange_date" class="form-control date_picker" placeholder="Pick a date..." required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="exchange_type">Exchange Type</label>
              <div class="col-sm-6">
                <select id="exchange_type" name="exchange_type" class="form-control" onchange="set_currency();" required>
                  <option selected hidden value="">Select an option</option>
                  <option value="AFN to USD">AFN to USD</option>
                  <option value="USD to AFN">USD to AFN</option>
                  <option value="AFN to IRT">AFN to IRT</option>
                  <option value="IRT to AFN">IRT to AFN</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="from_amount">From Amount</label>
              <div class="col-sm-6">
                <input type="number" step="0.01" id="from_amount" name="from_amount" class="form-control" placeholder="Type here..." required>
              </div>
            </div>

            <input type="hidden" id="from_currency" name="from_currency" />

            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="to_amount">To Amount</label>
              <div class="col-sm-6">
                <input type="number" step="0.01" id="to_amount" name="to_amount" class="form-control" placeholder="Type here..." required>
              </div>
            </div>

            <input type="hidden" id="to_currency" name="to_currency" />

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
        <?php
	      	
	      break;

	      case "edit_exchange_form":

		    	$exchange_sq = $db->prepare(
		    		"SELECT * 
		    		FROM `exchanges` 
		    		WHERE deleted='0' 
		    		AND id=:data_id 
		    		LIMIT 1"
		    	);

		    	$exchange_sqx = $exchange_sq->execute([
		    		'data_id'=>$data_id
		    	]);

		    	if($exchange_sq->rowCount()>0){
		    		$exchange_row = $exchange_sq->fetch();

		        ?>

		        <div class="modal-header">
			        <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Exchange</h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form class="form-horizontal parsley-form" role="form" action="controller_exchange.php" method="POST">

			        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

			        	<input type="hidden" name="flag_operation" id="flag_operation" value="edit_exchange"/>

			        	<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>"/>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="branch">Branch</label>
                  <div class="col-sm-6">
                    <select id="branch" name="branch" class="form-control" required>
                      <?php echo get_branch_option($exchange_row['province'], $exchange_row['branch']); ?>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="exchange_date">Exchange Date</label>
                  <div class="col-sm-6">
                    <input type="text" id="exchange_date" name="exchange_date" class="form-control date_picker" placeholder="Pick a date..." required value="<?php echo $exchange_row['exchange_date'];?>">
                  </div>
                </div>

                <div class="form-group row">
		              <label class="col-sm-3 col-form-label" for="exchange_type">Exchange Type</label>
		              <div class="col-sm-6">
		                <select id="exchange_type" name="exchange_type" class="form-control" onchange="set_currency();" required>
		                  <option selected hidden value="">Select an option</option>
		                  <option <?php echo ($exchange_row['from_currency']=="AFN" AND $exchange_row['to_currency']=="USD")?"selected":""; ?> value="AFN to USD">AFN to USD</option>
		                  <option <?php echo ($exchange_row['from_currency']=="USD" AND $exchange_row['to_currency']=="AFN")?"selected":""; ?> value="USD to AFN">USD to AFN</option>
		                  <option <?php echo ($exchange_row['from_currency']=="AFN" AND $exchange_row['to_currency']=="IRT")?"selected":""; ?> value="AFN to IRT">AFN to IRT</option>
		                  <option <?php echo ($exchange_row['from_currency']=="IRT" AND $exchange_row['to_currency']=="AFN")?"selected":""; ?> value="IRT to AFN">IRT to AFN</option>
		                </select>
		              </div>
		            </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="from_amount">From Amount</label>
                  <div class="col-sm-6">
                    <input type="number" step="0.01" id="from_amount" name="from_amount" class="form-control" placeholder="Type here..." required value="<?php echo $exchange_row['from_amount'];?>">
                  </div>
                </div>

                <input type="hidden" id="from_currency" name="from_currency" value="<?php echo $exchange_row['from_currency']; ?>"/>
                

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label" for="to_amount">To Amount</label>
                  <div class="col-sm-6">
                    <input type="number" step="0.01" id="to_amount" name="to_amount" class="form-control" placeholder="Type here..." required value="<?php echo $exchange_row['to_amount'];?>">
                  </div>
                </div>

                <input type="hidden" id="to_currency" name="to_currency" value="<?php echo $exchange_row['to_currency']; ?>"/>

			          <div class="form-group row">
			            <label class="col-sm-3 col-form-label" for="description">Description</label>
			            <div class="col-sm-6">
			              <textarea id="description" name="description" class="form-control" placeholder="Type here..." dir="rtl"><?php echo $exchange_row['description']; ?></textarea>
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

	      case "delete_exchange_form":

		    	
	        ?>

	        <div class="modal-header">
		        <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Exchange</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <form class="form-horizontal parsley-form" role="form" action="controller_exchange.php" method="POST">

		        	<input type="hidden" name="flag_request" id="flag_request" value="operation"/>

		        	<input type="hidden" name="flag_operation" id="flag_operation" value="delete_exchange"/>

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
	        <h4 class="modal-title" id="add_exchangeTitle"><i class="fa fa-filter"></i> Filter the Table</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form class="form-horizontal" role="form" action="list_exchange.php" method="GET">

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
	            <label class="col-sm-3 col-form-label" for="exchange_date">Exchange Date</label>
	            <div class="col-sm-3">
	              <input type="text" id="from_exchange_date" name="from_exchange_date" class="form-control date_picker" placeholder="From">
	            </div>
	            <div class="col-sm-3">
	              <input type="text" id="to_exchange_date" name="to_exchange_date" class="form-control date_picker" placeholder="To">
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
						          		$exchange_sq = $db->prepare("SELECT * FROM `exchanges` WHERE id=:data_id LIMIT 1");
										    	$exchange_sqx = $exchange_sq->execute([
										    		'data_id'=>$data_id
										    	]);

										    	if($exchange_sq->rowCount()>0){
										    		$exchange_row = $exchange_sq->fetch();
						          			?>
	                      		<tr>
			                        <th>Province</th>
			                        <td><?php echo $exchange_row['province']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Branch</th>
			                        <td><?php echo $exchange_row['branch']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Date</th>
			                        <td><?php echo $exchange_row['exchange_date']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>From Amount</th>
			                        <td><?php echo $exchange_row['from_amount']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>From Currency</th>
			                        <td><?php echo $exchange_row['from_currency']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>To Amount</th>
			                        <td><?php echo $exchange_row['to_amount']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>To Currency</th>
			                        <td><?php echo $exchange_row['to_currency']; ?></td>
			                      </tr>
			                      <tr>
			                        <th>Description</th>
			                        <td><?php echo $exchange_row['description']; ?></td>
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
				          		FROM `exchanges`
				          		WHERE id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Edition' AS operation_type
				          		FROM `transaction_editions`
				          		WHERE reference_type='Exchange'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Deletion' AS operation_type
				          		FROM `transaction_deletions`
				          		WHERE reference_type='Exchange'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		markup_type AS operation_type
				          		FROM `markups`
				          		WHERE reference_type='Exchange'
				          		AND reference_id='$data_id'
				          		UNION
				          		SELECT
				          		id AS operation_id,
				          		insertion_date AS operation_date,
				          		insertion_time AS operation_time,
				          		users_id AS operation_users_id,
				          		'Print' AS operation_type
				          		FROM `invoices`
				          		WHERE reference_type='Exchange'
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

  			case "add_exchange":

  			if(check_duplicate_exchange($_POST)==0){

	  			$province = holu_escape($_POST['province']);
			    $branch = holu_escape($_POST['branch']);
			    $exchange_date = holu_escape($_POST['exchange_date']);
	  			$from_amount = holu_escape($_POST['from_amount']);
			    $from_currency = holu_escape($_POST['from_currency']);
			    $to_amount = holu_escape($_POST['to_amount']);
			    $to_currency = holu_escape($_POST['to_currency']);
			    $description = holu_escape($_POST['description']);

			    $exchange_iq = $db->prepare("INSERT INTO `exchanges` (
			    	province, 
			    	branch,
			    	exchange_date, 
			    	from_amount, 
			    	from_currency, 
			    	to_amount, 
			    	to_currency, 
			    	description, 
			    	insertion_date, 
			    	insertion_time, 
			    	users_id
			    ) VALUES (
				    :province, 
				    :branch,
				    :exchange_date, 
				    :from_amount, 
				    :from_currency, 
				    :to_amount, 
				    :to_currency, 
				    :description, 
				    :holu_date, 
				    :holu_time,
				    :holu_users_id
				  )");

			    $exchange_iqx = $exchange_iq->execute([
			    	'province'=>$province,
			    	'branch'=>$branch,
			    	'exchange_date'=>$exchange_date,
			    	'from_amount'=>$from_amount,
			    	'from_currency'=>$from_currency,
			    	'to_amount'=>$to_amount,
			    	'to_currency'=>$to_currency,
			    	'description'=>$description,
			    	'holu_date'=>$holu_date,
			    	'holu_time'=>$holu_time,
			    	'holu_users_id'=>$holu_users_id
			    ]);

			    if($exchange_iqx){
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

  			case "edit_exchange":

  			if(check_duplicate_exchange($_POST)==0){

	  			track_editions('edit_exchange', ['exchanges_id'=>$_POST['data_id'], 'data_array'=>$_POST]);

	  			$data_id        = holu_escape(holu_decode($post['data_id'] ?? ''));
				$branch         = holu_escape($post['branch'] ?? '');
				$exchange_date  = holu_escape($post['exchange_date'] ?? '');
				$from_amount    = holu_escape($post['from_amount'] ?? '');
				$from_currency  = holu_escape($post['from_currency'] ?? '');
				$to_amount      = holu_escape($post['to_amount'] ?? '');
				$to_currency    = holu_escape($post['to_currency'] ?? '');
				$check_number   = holu_escape($post['check_number'] ?? '');
				$description    = holu_escape($post['description'] ?? '');

	  			$exchange_uq = $db->prepare("UPDATE `exchanges` SET 
	  				branch=:branch,
	  				exchange_date=:exchange_date, 
	  				from_amount=:from_amount, 
	  				from_currency=:from_currency, 
	  				to_amount=:to_amount, 
	  				to_currency=:to_currency, 
	  				description=:description 
	  			WHERE id=:data_id 
	  			LIMIT 1");

	  			$exchange_uqx = $exchange_uq->execute([
			    	'branch'=>$branch,
			    	'exchange_date'=>$exchange_date,
			    	'from_amount'=>$from_amount,
			    	'from_currency'=>$from_currency,
			    	'to_amount'=>$to_amount,
			    	'to_currency'=>$to_currency,
			    	'description'=>$description,
			    	'data_id'=>$data_id
	  			]);

	  			if($exchange_uqx){
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

  			case "delete_exchange":

  			track_deletions('delete_exchange', ['exchanges_id'=>$_POST['data_id']]);

  			$data_id = holu_escape(holu_decode($_POST['data_id']));

  			$exchange_dq = $db->prepare("UPDATE `exchanges` SET 
  				deleted='1' 
  			WHERE id=:data_id 
  			LIMIT 1");

  			$exchange_dqx = $exchange_dq->execute([
  				'data_id'=>$data_id
  			]);

  			if($exchange_dqx){
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
