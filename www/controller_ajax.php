<?php

	include("../lib/_configuration.php");

	if (isset($_POST['operation']) AND !empty($_POST['operation'])) {
		$operation = holu_escape($_POST['operation']);

		switch( $operation ) {

			case "get_category_option": {
				$category_type = holu_escape($_POST['category_type']);
				$categories_id = holu_escape($_POST['categories_id']);
				echo get_category_option($category_type, $categories_id);
			}break;

			case "get_sub_category_option": {
				$categories_id = holu_escape($_POST['categories_id']);
				$sub_categories_id = holu_escape($_POST['sub_categories_id']);
				echo get_sub_category_option($sub_categories_id, $categories_id);
			}break;

			case "markup_item": {
				$rtap = holu_escape($_POST['rtap']);
				$reference_type = holu_escape($_POST['reference_type']);
				$reference_id = holu_escape($_POST['reference_id']);
				$markup_type = holu_escape($_POST['markup_type']);
				$flag = "0";
				$markup_sq = $db->prepare("SELECT * FROM `markups` 
					WHERE reference_type=:reference_type
					AND reference_id=:reference_id
					AND markup_type=:markup_type
				");
				$markup_sqx = $markup_sq->execute([
					'reference_type'=>$reference_type,
					'reference_id'=>$reference_id,
					'markup_type'=>$markup_type
				]);

				if($markup_sq->rowCount()>0){
					$markup_row = $markup_sq->fetch();
					if($markup_row['deleted']=='0'){
						if($markup_row['users_id']==$holu_users_id){
							$markup_dq = $db->prepare("UPDATE `markups` 
							SET deleted='1'
							WHERE reference_type=:reference_type
							AND reference_id=:reference_id
							AND markup_type=:markup_type
							AND deleted='0'
							LIMIT 1
							");
							$markup_dqx = $markup_dq->execute([
								'reference_type'=>$reference_type,
								'reference_id'=>$reference_id,
								'markup_type'=>$markup_type
							]);
							if($markup_dqx){
								$flag = "1";
							}
						}
					}else{
						$markup_uq = $db->prepare("UPDATE `markups` 
						SET deleted='0', users_id=:holu_users_id
						WHERE reference_type=:reference_type
						AND reference_id=:reference_id
						AND markup_type=:markup_type
						LIMIT 1
						");
						$markup_uqx = $markup_uq->execute([
							'holu_users_id'=>$holu_users_id,
							'reference_type'=>$reference_type,
							'reference_id'=>$reference_id,
							'markup_type'=>$markup_type
						]);
						if($markup_uqx){
							$flag = "2";
						}
					}
				}else{
					$markup_iq = $db->prepare("INSERT INTO `markups` (
						reference_type,
						reference_id,
						markup_type,
						insertion_date, 
			    	insertion_time, 
			    	users_id
					) VALUES (
						:reference_type,
						:reference_id,
						:markup_type,
						:holu_date, 
			    	:holu_time, 
			    	:holu_users_id
					)");
					$markup_iqx = $markup_iq->execute([
						'reference_type'=>$reference_type,
						'reference_id'=>$reference_id,
			    	'markup_type'=>$markup_type,
			    	'holu_date'=>$holu_date,
			    	'holu_time'=>$holu_time,
			    	'holu_users_id'=>$holu_users_id
					]);
					if($markup_iqx){
						$flag = "3";
					}
				}

				switch ($reference_type) {
					case 'Income':{

						$income_uq = $db->query("UPDATE incomes SET tms_markup='0', qb_markup='0', sib_markup='0', ad_markup='0' WHERE id='$reference_id'");

						$markup_sq = $db->query("SELECT * FROM markups WHERE deleted='0' AND reference_type='Income' AND reference_id='$reference_id'");

						if($markup_sq->rowCount()>0){
							$markup_array = [];
							while($markup_row = $markup_sq->fetch()){
								switch ($markup_row['markup_type']) {
									case 'TMS Markup':{
										$income_uq = $db->query("UPDATE incomes SET tms_markup='1' WHERE id='$reference_id'");
									}break;

									case 'QB Markup':{
										$income_uq = $db->query("UPDATE incomes SET qb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'SIB Markup':{
										$income_uq = $db->query("UPDATE incomes SET sib_markup='1' WHERE id='$reference_id'");
									}break;

									case 'Ad Markup':{
										$income_uq = $db->query("UPDATE incomes SET ad_markup='1' WHERE id='$reference_id'");
									}break;
									
									default:{

									}break;
								}
							}
						}

					}break;

					case 'Expense':{

						$expense_uq = $db->query("UPDATE expenses SET tms_markup='0', qb_markup='0', sib_markup='0', ad_markup='0' WHERE id='$reference_id'");

						$markup_sq = $db->query("SELECT * FROM markups WHERE deleted='0' AND reference_type='Expense' AND reference_id='$reference_id'");

						if($markup_sq->rowCount()>0){
							$markup_array = [];
							while($markup_row = $markup_sq->fetch()){
								switch ($markup_row['markup_type']) {
									case 'TMS Markup':{
										$expense_uq = $db->query("UPDATE expenses SET tms_markup='1' WHERE id='$reference_id'");
									}break;

									case 'QB Markup':{
										$expense_uq = $db->query("UPDATE expenses SET qb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'SIB Markup':{
										$expense_uq = $db->query("UPDATE expenses SET sib_markup='1' WHERE id='$reference_id'");
									}break;

									case 'Ad Markup':{
										$expense_uq = $db->query("UPDATE expenses SET ad_markup='1' WHERE id='$reference_id'");
									}break;
									
									default:{

									}break;
								}
							}
						}

					}break;

					case 'Exchange':{

						$exchange_uq = $db->query("UPDATE exchanges SET tms_markup='0', qb_markup='0', sib_markup='0', ad_markup='0' WHERE id='$reference_id'");

						$markup_sq = $db->query("SELECT * FROM markups WHERE deleted='0' AND reference_type='Exchange' AND reference_id='$reference_id'");

						if($markup_sq->rowCount()>0){
							$markup_array = [];
							while($markup_row = $markup_sq->fetch()){
								switch ($markup_row['markup_type']) {
									case 'TMS Markup':{
										$exchange_uq = $db->query("UPDATE exchanges SET tms_markup='1' WHERE id='$reference_id'");
									}break;

									case 'QB Markup':{
										$exchange_uq = $db->query("UPDATE exchanges SET qb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'SIB Markup':{
										$exchange_uq = $db->query("UPDATE exchanges SET sib_markup='1' WHERE id='$reference_id'");
									}break;

									case 'Ad Markup':{
										$exchange_uq = $db->query("UPDATE exchanges SET ad_markup='1' WHERE id='$reference_id'");
									}break;
									
									default:{

									}break;
								}
							}
						}

					}break;

					case 'Transaction_Edition':{

						$transaction_edition_uq = $db->query("UPDATE transaction_editions SET tms_markup='0', qb_markup='0', sib_markup='0', ad_markup='0' WHERE id='$reference_id'");

						$markup_sq = $db->query("SELECT * FROM markups WHERE deleted='0' AND reference_type='Transaction_Edition' AND reference_id='$reference_id'");

						if($markup_sq->rowCount()>0){
							$markup_array = [];
							while($markup_row = $markup_sq->fetch()){
								switch ($markup_row['markup_type']) {
									case 'TMS Markup':{
										$transaction_edition_uq = $db->query("UPDATE transaction_editions SET tms_markup='1' WHERE id='$reference_id'");
									}break;

									case 'QB Markup':{
										$transaction_edition_uq = $db->query("UPDATE transaction_editions SET qb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'SIB Markup':{
										$transaction_edition_uq = $db->query("UPDATE transaction_editions SET sib_markup='1' WHERE id='$reference_id'");
									}break;

									case 'Ad Markup':{
										$transaction_edition_uq = $db->query("UPDATE transaction_editions SET ad_markup='1' WHERE id='$reference_id'");
									}break;
									
									default:{

									}break;
								}
							}
						}

					}break;

					case 'Transaction_Deletion':{

						$transaction_deletion_uq = $db->query("UPDATE transaction_deletions SET tms_markup='0', qb_markup='0', sib_markup='0', ad_markup='0' WHERE id='$reference_id'");

						$markup_sq = $db->query("SELECT * FROM markups WHERE deleted='0' AND reference_type='Transaction_Deletion' AND reference_id='$reference_id'");

						if($markup_sq->rowCount()>0){
							$markup_array = [];
							while($markup_row = $markup_sq->fetch()){
								switch ($markup_row['markup_type']) {
									case 'TMS Markup':{
										$transaction_deletion_uq = $db->query("UPDATE transaction_deletions SET tms_markup='1' WHERE id='$reference_id'");
									}break;

									case 'QB Markup':{
										$transaction_deletion_uq = $db->query("UPDATE transaction_deletions SET qb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'SIB Markup':{
										$transaction_deletion_uq = $db->query("UPDATE transaction_deletions SET sib_markup='1' WHERE id='$reference_id'");
									}break;

									case 'Ad Markup':{
										$transaction_deletion_uq = $db->query("UPDATE transaction_deletions SET ad_markup='1' WHERE id='$reference_id'");
									}break;
									
									default:{

									}break;
								}
							}
						}

					}break;

					case 'Purchase':{

						$purchase_uq = $db->query("UPDATE purchases SET tms_markup='0', qb_markup='0', sib_markup='0', ad_markup='0' WHERE id='$reference_id'");

						$markup_sq = $db->query("SELECT * FROM markups WHERE deleted='0' AND reference_type='Purchase' AND reference_id='$reference_id'");

						if($markup_sq->rowCount()>0){
							$markup_array = [];
							while($markup_row = $markup_sq->fetch()){
								switch ($markup_row['markup_type']) {
									case 'TMS Markup':{
										$purchase_uq = $db->query("UPDATE purchases SET tms_markup='1' WHERE id='$reference_id'");
									}break;

									case 'QB Markup':{
										$purchase_uq = $db->query("UPDATE purchases SET qb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'SIB Markup':{
										$purchase_uq = $db->query("UPDATE purchases SET sib_markup='1' WHERE id='$reference_id'");
									}break;

									case 'Ad Markup':{
										$purchase_uq = $db->query("UPDATE purchases SET ad_markup='1' WHERE id='$reference_id'");
									}break;
									
									default:{

									}break;
								}
							}
						}

					}break;

					case 'Transfer':{

						$transfer_uq = $db->query("UPDATE transfers SET tms_markup='0', qb_markup='0', rqb_markup='0', sib_markup='0', ad_markup='0' WHERE id='$reference_id'");

						$markup_sq = $db->query("SELECT * FROM markups WHERE deleted='0' AND reference_type='Transfer' AND reference_id='$reference_id'");

						if($markup_sq->rowCount()>0){
							$markup_array = [];
							while($markup_row = $markup_sq->fetch()){
								switch ($markup_row['markup_type']) {
									case 'TMS Markup':{
										$transfer_uq = $db->query("UPDATE transfers SET tms_markup='1' WHERE id='$reference_id'");
									}break;

									case 'QB Markup':{
										$transfer_uq = $db->query("UPDATE transfers SET qb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'RQB Markup':{
										$transfer_uq = $db->query("UPDATE transfers SET rqb_markup='1' WHERE id='$reference_id'");
									}break;

									case 'SIB Markup':{
										$transfer_uq = $db->query("UPDATE transfers SET sib_markup='1' WHERE id='$reference_id'");
									}break;

									case 'Ad Markup':{
										$transfer_uq = $db->query("UPDATE transfers SET ad_markup='1' WHERE id='$reference_id'");
									}break;
									
									default:{

									}break;
								}
							}
						}

					}break;

					
					default:{

					}break;
				}

				echo set_markups($rtap, $reference_type, $reference_id);
			}break;

			case "edit_sib_number": {
				$incomes_id = holu_escape($_POST['incomes_id']);
				$operation_type = holu_escape($_POST['operation_type']);
				if($operation_type=="edit"){
					$result = '
						<input type="text" class="form-control" id="sib_number'.$incomes_id.'" name="sib_number'.$incomes_id.'" value="'.get_col('incomes', 'sib_number', 'id', $incomes_id).'"/>
						<span onclick="edit_sib_number(\''.$incomes_id.'\', \'save\');" class="badge badge-success" style="cursor:pointer;">
							<i class="fas fa-save"></i>
						</span>
					';
				}else if($operation_type=="save"){
					$sib_number = holu_escape($_POST['sib_number']);

					if(get_col('incomes', 'sib_number', 'sib_number', $sib_number)==""){
						$income_uq = $db->prepare(
							"UPDATE `incomes` 
							SET sib_number=:sib_number
							WHERE id=:incomes_id
							LIMIT 1"
						);

						$income_uqx = $income_uq->execute([
							'sib_number'=>$sib_number,
							'incomes_id'=>$incomes_id
						]);
					}

					

					$sib_number = get_col('incomes', 'sib_number', 'id', $incomes_id);
                              
          $result = '
          <p>'.$sib_number.'</p>
          <span onclick="edit_sib_number(\''.$incomes_id.'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
          ';
				}
				echo $result;
			}break;

			case "edit_accounting_note": {
				$incomes_id = holu_escape($_POST['incomes_id']);
				$operation_type = holu_escape($_POST['operation_type']);
				if($operation_type=="edit"){
					$result = '
						<input type="text" class="form-control" id="accounting_note'.$incomes_id.'" name="accounting_note'.$incomes_id.'" value="'.get_col('incomes', 'accounting_note', 'id', $incomes_id).'"/>
						<span onclick="edit_accounting_note(\''.$incomes_id.'\', \'save\');" class="badge badge-success" style="cursor:pointer;">
							<i class="fas fa-save"></i>
						</span>
					';
				}else if($operation_type=="save"){
					$accounting_note = holu_escape($_POST['accounting_note']);

					
					$income_uq = $db->prepare(
						"UPDATE `incomes` 
						SET accounting_note=:accounting_note
						WHERE id=:incomes_id
						LIMIT 1"
					);

					$income_uqx = $income_uq->execute([
						'accounting_note'=>$accounting_note,
						'incomes_id'=>$incomes_id
					]);
					

					

					$accounting_note = get_col('incomes', 'accounting_note', 'id', $incomes_id);
                              
          $result = '
          <p>'.$accounting_note.'</p>
          <span onclick="edit_accounting_note(\''.$incomes_id.'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
          ';
				}
				echo $result;
			}break;

			case "edit_check_number": {
				$expenses_id = holu_escape($_POST['expenses_id']);
				$operation_type = holu_escape($_POST['operation_type']);
				if($operation_type=="edit"){
					$result = '
						<input type="text" class="form-control" id="check_number'.$expenses_id.'" name="check_number'.$expenses_id.'" value="'.get_col('expenses', 'check_number', 'id', $expenses_id).'"/>
						<span onclick="edit_check_number(\''.$expenses_id.'\', \'save\');" class="badge badge-success" style="cursor:pointer;">
							<i class="fas fa-save"></i>
						</span>
					';
				}else if($operation_type=="save"){
					$check_number = holu_escape($_POST['check_number']);

					if(get_col('expenses', 'check_number', 'check_number', $check_number)==""){
						$expense_uq = $db->prepare(
							"UPDATE `expenses` 
							SET check_number=:check_number
							WHERE id=:expenses_id
							LIMIT 1"
						);

						$expense_uqx = $expense_uq->execute([
							'check_number'=>$check_number,
							'expenses_id'=>$expenses_id
						]);
					}

					

					$check_number = get_col('expenses', 'check_number', 'id', $expenses_id);
                              
          $result = '
          <p>'.$check_number.'</p>
          <span onclick="edit_check_number(\''.$expenses_id.'\', \'edit\');" class="badge badge-warning" style="cursor:pointer;"><i class="fas fa-edit"></i></span>
          ';
				}
				echo $result;
			}break;

			case "add_row": {
				$selector = holu_escape($_POST['selector']);
				$counter = holu_escape($_POST['counter']);
				$value = holu_escape($_POST['value']);
				echo add_row($selector, $counter, $value);
			}break;

			case "configure_input_field": {
				$selector = holu_escape($_POST['selector']);
				$counter = holu_escape($_POST['counter']);
				$key_info = holu_escape($_POST['key_info']);
				
				$input_field = '';

				switch($key_info) {
			    case 'Customer Name':{
			      $input_field = '
			      	<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." autocomplete="off" onkeyup="suggest_data(this, \'additional_information_customer_name\');">
			      ';
			    }break;
			    case 'Customer ID':{
			      $input_field = '
			      	<input type="number" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." autocomplete="off">
			      ';
			    }break;
			    case 'Package':{
			      $input_field = '
			      	<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." autocomplete="off" onkeyup="suggest_data(this, \'additional_information_package\');">
			      ';
			    }break;
			    case 'Start Date':{
			      $input_field = '
			      	<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control date_picker" placeholder="Type here..." autocompelete="off">
			      ';
			    }break;
			    case 'End Date':{
			      $input_field = '
			      	<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control date_picker" placeholder="Type here..." autocompelete="off">
			      ';
			    }break;
			    case 'Equipment':{
			    	$input_field = '
			      	<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." autocomplete="off" onkeyup="suggest_data(this, \'additional_information_equipment\');">
			      ';
			    }break;
			    case 'Other Services':{
			    	$input_field = '
			      	<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." autocomplete="off" onkeyup="suggest_data(this, \'additional_information_other_services\');">
			      ';
			    }break;
			    default:{
			    	$input_field = '
			      	<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here...">
			      ';
			    }break;
			  }

				echo $input_field;
			}break;

			case "get_income_for_accouting": {
				$province = holu_escape($_POST['province']);
				echo get_income_for_accounting($province) ;
			}break;
			

			case "update_invoice": {
				$invoices_id = holu_escape($_POST['invoices_id']);
				
				$invoice_uq = $db->prepare(
					"UPDATE `invoices` 
					SET is_printed='1'
					WHERE id=:invoices_id
					LIMIT 1"
				);

				$invoice_uqx = $invoice_uq->execute([
					'invoices_id'=>$invoices_id
				]);

				if($invoice_uqx){
					echo "1";
				}else{
					echo "0";
				}
				
			}break;

			case "suggest_data": {
				$input = holu_escape($_POST['input']);
				$field_id = holu_escape($_POST['field_id']);
				$type = holu_escape($_POST['type']);

				$result = '';

				switch ($type) {
					case 'additional_information_customer_name':{
						$data_sq = $db->prepare(
							"SELECT DISTINCT(value_info) FROM (SELECT * FROM additional_informations ORDER BY id DESC LIMIT 20000) AS additional_informations
							WHERE key_info='Customer Name'
							AND value_info LIKE :input
							LIMIT 5"
						);
						$data_sqx = $data_sq->execute([
							'input'=>'%'.$input.'%'
						]);

						if($data_sq->rowCount()>0 AND $input!=""){
							$result .= '
							<div class="holu_auto_suggest_container" id="holu_auto_suggest_container">
		      			<ul>
							';
							while ($data_row = $data_sq->fetch()) {
								$result .= '
									<li onclick="select_data(\''.$field_id.'\', \''.$data_row['value_info'].'\');">'.$data_row['value_info'].'</li>
								';
							}
							$result .= '
								</ul>
		      		</div>
							';
						}
					}break;

					case 'additional_information_customer_id':{
						$data_sq = $db->prepare(
							"SELECT DISTINCT(value_info) FROM (SELECT * FROM additional_informations ORDER BY id DESC LIMIT 20000) AS additional_informations 
							WHERE key_info='Customer ID'
							AND value_info LIKE :input
							LIMIT 5"
						);
						$data_sqx = $data_sq->execute([
							'input'=>'%'.$input.'%'
						]);

						if($data_sq->rowCount()>0 AND $input!=""){
							$result .= '
							<div class="holu_auto_suggest_container" id="holu_auto_suggest_container">
		      			<ul>
							';
							while ($data_row = $data_sq->fetch()) {
								$result .= '
									<li onclick="select_data(\''.$field_id.'\', \''.$data_row['value_info'].'\');">'.$data_row['value_info'].'</li>
								';
							}
							$result .= '
								</ul>
		      		</div>
							';
						}
					}break;

					case 'additional_information_package':{
						$data_sq = $db->prepare(
							"SELECT DISTINCT(value_info) FROM (SELECT * FROM additional_informations ORDER BY id DESC LIMIT 20000) AS additional_informations 
							WHERE key_info='Package'
							AND value_info LIKE :input
							LIMIT 5"
						);
						$data_sqx = $data_sq->execute([
							'input'=>'%'.$input.'%'
						]);

						if($data_sq->rowCount()>0 AND $input!=""){
							$result .= '
							<div class="holu_auto_suggest_container" id="holu_auto_suggest_container">
		      			<ul>
							';
							while ($data_row = $data_sq->fetch()) {
								$result .= '
									<li onclick="select_data(\''.$field_id.'\', \''.$data_row['value_info'].'\');">'.$data_row['value_info'].'</li>
								';
							}
							$result .= '
								</ul>
		      		</div>
							';
						}
					}break;

					case 'additional_information_equipment':{
						$data_sq = $db->prepare(
							"SELECT DISTINCT(value_info) FROM (SELECT * FROM additional_informations ORDER BY id DESC LIMIT 20000) AS additional_informations 
							WHERE key_info='Equipment'
							AND value_info LIKE :input
							LIMIT 5"
						);
						$data_sqx = $data_sq->execute([
							'input'=>'%'.$input.'%'
						]);

						if($data_sq->rowCount()>0 AND $input!=""){
							$result .= '
							<div class="holu_auto_suggest_container" id="holu_auto_suggest_container">
		      			<ul>
							';
							while ($data_row = $data_sq->fetch()) {
								$result .= '
									<li onclick="select_data(\''.$field_id.'\', \''.$data_row['value_info'].'\');">'.$data_row['value_info'].'</li>
								';
							}
							$result .= '
								</ul>
		      		</div>
							';
						}
					}break;

					case 'additional_information_other_services':{
						$data_sq = $db->prepare(
							"SELECT DISTINCT(value_info) FROM (SELECT * FROM additional_informations ORDER BY id DESC LIMIT 20000) AS additional_informations 
							WHERE key_info='Other Services'
							AND value_info LIKE :input
							LIMIT 5"
						);
						$data_sqx = $data_sq->execute([
							'input'=>'%'.$input.'%'
						]);

						if($data_sq->rowCount()>0 AND $input!=""){
							$result .= '
							<div class="holu_auto_suggest_container" id="holu_auto_suggest_container">
		      			<ul>
							';
							while ($data_row = $data_sq->fetch()) {
								$result .= '
									<li onclick="select_data(\''.$field_id.'\', \''.$data_row['value_info'].'\');">'.$data_row['value_info'].'</li>
								';
							}
							$result .= '
								</ul>
		      		</div>
							';
						}
					}break;
					
					default:{

					}break;
				}

				
				
				echo $result;
				
			}break;

			case "load_data_for_report_component": {
				$result = '';
				$information_level = holu_escape($_POST['information_level']);
				$selector = holu_escape($_POST['selector']);
				
				$income_filtering_data = "";
				$expense_filtering_data = "";
				$exchange_filtering_data = "";
				$purchase_filtering_data = "";
				$transfer_filtering_data = "";

				$closing_income_filtering_data = "";
			  $closing_expense_filtering_data = "";
			  $closing_exchange_filtering_data = "";
			  $closing_purchase_filtering_data = "";
			  $closing_transfer_filtering_data = "";

				$province = "0";
				$from_date = "";
				$to_date = "";

				if(isset($_POST['province']) AND !empty($_POST['province'])){
				  $province = $_POST['province'];
				  $income_filtering_data .= " AND province='".$province."' ";
				  $expense_filtering_data .= " AND province='".$province."' ";
				  $exchange_filtering_data .= " AND province='".$province."' ";
				  $purchase_filtering_data .= " AND province='".$province."' ";
				  $transfer_filtering_data .= " AND (transfers.from_province='".$province."' OR to_province='".$province."') ";

				  $closing_income_filtering_data .= " AND incomes.province='".$province."' ";
			    $closing_expense_filtering_data .= " AND expenses.province='".$province."' ";
			    $closing_exchange_filtering_data .= " AND exchanges.province='".$province."' ";
			    $closing_purchase_filtering_data .= " AND purchases.province='".$province."' ";
			    $closing_transfer_filtering_data .= " AND (transfers.from_province='".$province."' OR to_province='".$province."') ";
				}
				if(isset($_POST['from_date']) AND !empty($_POST['from_date'])){
				  $from_date = $_POST['from_date'];
				  $income_filtering_data .= " AND income_date>='".$from_date."' ";
				  $expense_filtering_data .= " AND expense_date>='".$from_date."' ";
				  $exchange_filtering_data .= " AND exchange_date>='".$from_date."' ";
				  $purchase_filtering_data .= " AND purchase_date>='".$from_date."' ";
				  $transfer_filtering_data .= " AND transfer_date>='".$from_date."' ";
				}
				if(isset($_POST['to_date']) AND !empty($_POST['to_date'])){
				  $to_date = $_POST['to_date'];
				  $income_filtering_data .= " AND income_date<='".$to_date."' ";
				  $expense_filtering_data .= " AND expense_date<='".$to_date."' ";
				  $exchange_filtering_data .= " AND exchange_date<='".$to_date."' ";
				  $purchase_filtering_data .= " AND purchase_date<='".$to_date."' ";
				  $transfer_filtering_data .= " AND transfer_date<='".$to_date."' ";

				  $closing_income_filtering_data .= " AND incomes.income_date<='".$to_date."' ";
			    $closing_expense_filtering_data .= " AND expenses.expense_date<='".$to_date."' ";
			    $closing_exchange_filtering_data .= " AND exchanges.exchange_date<='".$to_date."' ";
			    $closing_exchange_filtering_data .= " AND exchanges.exchange_date<='".$to_date."' ";
			    $closing_purchase_filtering_data .= " AND purchases.purchase_date<='".$to_date."' ";
			    $closing_transfer_filtering_data .= " AND transfers.transfer_date<='".$to_date."' ";
				}

				switch ($information_level) {

					case 'Balance':{
						$total_income_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) as total_afn_income,
							SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) as total_usd_income,
							SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) as total_irt_income
	            FROM `incomes` 
	            WHERE deleted='0' 
	            AND province IN ($accessed_provinces) 
	            AND sub_categories_id IN ($accessed_sub_categories_income)
	            $income_filtering_data"
            );
            $total_income_row = $total_income_sq->fetch();

            $total_expense_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) as total_afn_expense,
							SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) as total_usd_expense,
							SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) as total_irt_expense
	            FROM `expenses` 
	            WHERE deleted='0' 
	            AND province IN ($accessed_provinces) 
	            AND sub_categories_id IN ($accessed_sub_categories_expense)
	            $expense_filtering_data"
            );
            $total_expense_row = $total_expense_sq->fetch();

            $total_exchange_sq = $db->query(
            	"SELECT 
            	SUM(CASE WHEN from_currency='AFN' AND to_currency='USD' THEN from_amount ELSE 0 END) as total_afn_from_amount,
            	SUM(CASE WHEN from_currency='AFN' AND to_currency='IRT' THEN from_amount ELSE 0 END) as total_afn_from_amount2,
            	SUM(CASE WHEN to_currency='USD' AND from_currency='AFN' THEN to_amount ELSE 0 END) as total_usd_to_amount,
            	SUM(CASE WHEN to_currency='IRT' AND from_currency='AFN' THEN to_amount ELSE 0 END) as total_irt_to_amount,
            	SUM(CASE WHEN from_currency='USD' AND to_currency='AFN' THEN from_amount ELSE 0 END) as total_usd_from_amount,
            	SUM(CASE WHEN from_currency='IRT' AND to_currency='AFN' THEN from_amount ELSE 0 END) as total_irt_from_amount,
            	SUM(CASE WHEN to_currency='AFN' AND from_currency='USD' THEN to_amount ELSE 0 END) as total_afn_to_amount,
            	SUM(CASE WHEN to_currency='AFN' AND from_currency='IRT' THEN to_amount ELSE 0 END) as total_afn_to_amount2
              FROM `exchanges` 
              WHERE deleted='0'
              AND province IN ($accessed_provinces) 
              $accessed_sub_categories_exchange
              $exchange_filtering_data"
             );
            $total_exchange_row = $total_exchange_sq->fetch();

            $total_purchase_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN purchase_amount ELSE 0 END) as total_afn_purchase,
							SUM(CASE WHEN currency='USD' THEN purchase_amount ELSE 0 END) as total_usd_purchase,
							SUM(CASE WHEN currency='IRT' THEN purchase_amount ELSE 0 END) as total_irt_purchase
	            FROM `purchases` 
	            WHERE purchases.deleted='0' 
				      AND purchases.is_approved='1'
    					AND purchases.is_included='1'
				      AND purchases.province IN ($accessed_provinces) 
				      $purchase_filtering_data
				      AND purchases.sub_categories_id IN ($accessed_sub_categories_purchase)
				      AND logistic_cashes_id IN ($accessed_logistic_cashes)"
            );
            $total_purchase_row = $total_purchase_sq->fetch();

            $total_transfer_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_afn_transfer_out,
							SUM(CASE WHEN currency='AFN' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_afn_transfer_in,
							SUM(CASE WHEN currency='USD' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_usd_transfer_out,
							SUM(CASE WHEN currency='USD' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_usd_transfer_in,
							SUM(CASE WHEN currency='IRT' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_irt_transfer_out,
							SUM(CASE WHEN currency='IRT' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_irt_transfer_in
	            FROM `transfers` 
	            WHERE transfers.deleted='0' 
	            AND transfers.is_approved='1'
				      AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
				      $transfer_filtering_data
				      $accessed_sub_categories_transfer"
            );
            $total_transfer_row = $total_transfer_sq->fetch();



            $closing_total_income_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) as total_afn_income,
							SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) as total_usd_income,
							SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) as total_irt_income
	            FROM `incomes` 
	            WHERE deleted='0' 
	            AND province IN ($accessed_provinces) 
	            AND sub_categories_id IN ($accessed_sub_categories_income)
	            $closing_income_filtering_data"
            );
            $closing_total_income_row = $closing_total_income_sq->fetch();

            $closing_total_expense_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) as total_afn_expense,
							SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) as total_usd_expense,
							SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) as total_irt_expense
	            FROM `expenses` 
	            WHERE deleted='0' 
	            AND province IN ($accessed_provinces) 
	            AND sub_categories_id IN ($accessed_sub_categories_expense)
	            $closing_expense_filtering_data"
            );
            $closing_total_expense_row = $closing_total_expense_sq->fetch();

            $closing_total_exchange_sq = $db->query(
            	"SELECT 
            	SUM(CASE WHEN from_currency='AFN' AND to_currency='USD' THEN from_amount ELSE 0 END) as total_afn_from_amount,
            	SUM(CASE WHEN from_currency='AFN' AND to_currency='IRT' THEN from_amount ELSE 0 END) as total_afn_from_amount2,
            	SUM(CASE WHEN to_currency='USD' AND from_currency='AFN' THEN to_amount ELSE 0 END) as total_usd_to_amount,
            	SUM(CASE WHEN to_currency='IRT' AND from_currency='AFN' THEN to_amount ELSE 0 END) as total_irt_to_amount,
            	SUM(CASE WHEN from_currency='USD' AND to_currency='AFN' THEN from_amount ELSE 0 END) as total_usd_from_amount,
            	SUM(CASE WHEN from_currency='IRT' AND to_currency='AFN' THEN from_amount ELSE 0 END) as total_irt_from_amount,
            	SUM(CASE WHEN to_currency='AFN' AND from_currency='USD' THEN to_amount ELSE 0 END) as total_afn_to_amount,
            	SUM(CASE WHEN to_currency='AFN' AND from_currency='IRT' THEN to_amount ELSE 0 END) as total_afn_to_amount2
              FROM `exchanges` 
              WHERE deleted='0'
              AND province IN ($accessed_provinces) 
              $accessed_sub_categories_exchange
              $closing_exchange_filtering_data"
             );
            $closing_total_exchange_row = $closing_total_exchange_sq->fetch();

            $closing_total_purchase_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN purchase_amount ELSE 0 END) as total_afn_purchase,
							SUM(CASE WHEN currency='USD' THEN purchase_amount ELSE 0 END) as total_usd_purchase,
							SUM(CASE WHEN currency='IRT' THEN purchase_amount ELSE 0 END) as total_irt_purchase
	            FROM `purchases` 
	            WHERE purchases.deleted='0' 
				      AND purchases.is_approved='1'
    					AND purchases.is_included='1'
				      AND purchases.province IN ($accessed_provinces) 
				      $closing_purchase_filtering_data
				      AND purchases.sub_categories_id IN ($accessed_sub_categories_purchase)
				      AND logistic_cashes_id IN ($accessed_logistic_cashes)"
            );
            $closing_total_purchase_row = $closing_total_purchase_sq->fetch();

            $closing_total_transfer_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_afn_transfer_out,
							SUM(CASE WHEN currency='AFN' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_afn_transfer_in,
							SUM(CASE WHEN currency='USD' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_usd_transfer_out,
							SUM(CASE WHEN currency='USD' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_usd_transfer_in,
							SUM(CASE WHEN currency='IRT' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_irt_transfer_out,
							SUM(CASE WHEN currency='IRT' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_irt_transfer_in
	            FROM `transfers` 
	            WHERE deleted='0' 
	            AND is_approved='1'
	            AND (from_province IN ($accessed_provinces) OR to_province IN ($accessed_provinces))
	            $accessed_sub_categories_transfer
	            $closing_transfer_filtering_data"
            );
            $closing_total_transfer_row = $closing_total_transfer_sq->fetch();

            $result = '
          	<li>
              <b>Closing Balance: </b>
              <span class="badge badge-secondary">'.(
              	$closing_total_income_row['total_afn_income']
              	-$closing_total_expense_row['total_afn_expense']
              	-$closing_total_purchase_row['total_afn_purchase']
              	-$closing_total_exchange_row['total_afn_from_amount']
              	-$closing_total_exchange_row['total_afn_from_amount2']
              	+$closing_total_exchange_row['total_afn_to_amount']
              	+$closing_total_exchange_row['total_afn_to_amount2']
              	-$closing_total_transfer_row['total_afn_transfer_out']
              	+$closing_total_transfer_row['total_afn_transfer_in']
              ).' AFN</span> - <span class="badge badge-secondary">'.(
              	$closing_total_income_row['total_usd_income']
              	-$closing_total_expense_row['total_usd_expense']
              	-$closing_total_purchase_row['total_usd_purchase']
              	-$closing_total_exchange_row['total_usd_from_amount']
              	+$closing_total_exchange_row['total_usd_to_amount']
              	-$closing_total_transfer_row['total_usd_transfer_out']
              	+$closing_total_transfer_row['total_usd_transfer_in']
              ).' USD</span> - <span class="badge badge-secondary">'.(
              	$closing_total_income_row['total_irt_income']
              	-$closing_total_expense_row['total_irt_expense']
              	-$closing_total_purchase_row['total_irt_purchase']
              	-$closing_total_exchange_row['total_irt_from_amount']
              	+$closing_total_exchange_row['total_irt_to_amount']
              	-$closing_total_transfer_row['total_irt_transfer_out']
              	+$closing_total_transfer_row['total_irt_transfer_in']
              ).' IRT</span>
              <ul class="tree_view"></ul>
            </li>

            <li>
              <b onclick="load_data_for_report_component(\'Transaction\', \'\');">Balance: </b>
              <span class="badge badge-secondary">'.(
              	$total_income_row['total_afn_income']
              	-$total_expense_row['total_afn_expense']
              	-$total_purchase_row['total_afn_purchase']
              	-$total_exchange_row['total_afn_from_amount']
              	-$total_exchange_row['total_afn_from_amount2']
              	+$total_exchange_row['total_afn_to_amount']
              	+$total_exchange_row['total_afn_to_amount2']
              	-$total_transfer_row['total_afn_transfer_out']
              	+$total_transfer_row['total_afn_transfer_in']
              ).' AFN</span> - <span class="badge badge-secondary">'.(
              	$total_income_row['total_usd_income']
              	-$total_expense_row['total_usd_expense']
              	-$total_purchase_row['total_usd_purchase']
              	-$total_exchange_row['total_usd_from_amount']
              	+$total_exchange_row['total_usd_to_amount']
              	-$total_transfer_row['total_usd_transfer_out']
              	+$total_transfer_row['total_usd_transfer_in']
              ).' USD</span> - <span class="badge badge-secondary">'.(
              	$total_income_row['total_irt_income']
              	-$total_expense_row['total_irt_expense']
              	-$total_purchase_row['total_irt_purchase']
              	-$total_exchange_row['total_irt_from_amount']
              	+$total_exchange_row['total_irt_to_amount']
              	-$total_transfer_row['total_irt_transfer_out']
              	+$total_transfer_row['total_irt_transfer_in']
              ).' IRT</span>
              <ul class="tree_view" id="TransactionContainer"></ul>
            </li>
            ';
            echo $result;
					}break;

					case 'Transaction':{
						$total_income_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) as total_afn_income,
							SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) as total_usd_income,
							SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) as total_irt_income
	            FROM `incomes` 
	            WHERE deleted='0' 
	            AND province IN ($accessed_provinces) 
	            AND sub_categories_id IN ($accessed_sub_categories_income)
	            $income_filtering_data"
            );
            $total_income_row = $total_income_sq->fetch();

            $total_expense_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) as total_afn_expense,
							SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) as total_usd_expense,
							SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) as total_irt_expense
	            FROM `expenses` 
	            WHERE deleted='0' 
	            AND province IN ($accessed_provinces) 
	            AND sub_categories_id IN ($accessed_sub_categories_expense)
	            $expense_filtering_data"
            );
            $total_expense_row = $total_expense_sq->fetch();

            $total_exchange_sq = $db->query(
            	"SELECT 
            	SUM(CASE WHEN from_currency='AFN' AND to_currency='USD' THEN from_amount ELSE 0 END) as total_afn_from_amount,
            	SUM(CASE WHEN from_currency='AFN' AND to_currency='IRT' THEN from_amount ELSE 0 END) as total_afn_from_amount2,
            	SUM(CASE WHEN to_currency='USD' AND from_currency='AFN' THEN to_amount ELSE 0 END) as total_usd_to_amount,
            	SUM(CASE WHEN to_currency='IRT' AND from_currency='AFN' THEN to_amount ELSE 0 END) as total_irt_to_amount,
            	SUM(CASE WHEN from_currency='USD' AND to_currency='AFN' THEN from_amount ELSE 0 END) as total_usd_from_amount,
            	SUM(CASE WHEN from_currency='IRT' AND to_currency='AFN' THEN from_amount ELSE 0 END) as total_irt_from_amount,
            	SUM(CASE WHEN to_currency='AFN' AND from_currency='USD' THEN to_amount ELSE 0 END) as total_afn_to_amount,
            	SUM(CASE WHEN to_currency='AFN' AND from_currency='IRT' THEN to_amount ELSE 0 END) as total_afn_to_amount2
              FROM `exchanges` 
              WHERE deleted='0'
              AND province IN ($accessed_provinces) 
              $accessed_sub_categories_exchange
              $exchange_filtering_data"
             );
            $total_exchange_row = $total_exchange_sq->fetch();

            $total_purchase_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' THEN purchase_amount ELSE 0 END) as total_afn_purchase,
							SUM(CASE WHEN currency='USD' THEN purchase_amount ELSE 0 END) as total_usd_purchase,
							SUM(CASE WHEN currency='IRT' THEN purchase_amount ELSE 0 END) as total_irt_purchase
	            FROM `purchases` 
	            WHERE purchases.deleted='0' 
				      AND purchases.is_approved='1'
    					AND purchases.is_included='1'
				      AND purchases.province IN ($accessed_provinces) 
				      $purchase_filtering_data
				      AND purchases.sub_categories_id IN ($accessed_sub_categories_purchase)
				      AND logistic_cashes_id IN ($accessed_logistic_cashes)"
            );
            $total_purchase_row = $total_purchase_sq->fetch();

            $total_transfer_sq = $db->query(
							"SELECT 
							SUM(CASE WHEN currency='AFN' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_afn_transfer_out,
							SUM(CASE WHEN currency='AFN' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_afn_transfer_in,
							SUM(CASE WHEN currency='USD' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_usd_transfer_out,
							SUM(CASE WHEN currency='USD' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_usd_transfer_in,
							SUM(CASE WHEN currency='IRT' AND from_province='$province' THEN transfer_amount ELSE 0 END) as total_irt_transfer_out,
							SUM(CASE WHEN currency='IRT' AND to_province='$province' THEN transfer_amount ELSE 0 END) as total_irt_transfer_in
	            FROM `transfers` 
	            WHERE transfers.deleted='0' 
	            AND transfers.is_approved='1'
				      AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
				      $transfer_filtering_data
				      $accessed_sub_categories_transfer"
            );
            $total_transfer_row = $total_transfer_sq->fetch();

            $result = '
          	<li>
              <b onclick="load_data_for_report_component(\'Income\', \'\');">Income: </b>
              <span class="badge badge-success">'.$total_income_row['total_afn_income'].' AFN</span> - <span class="badge badge-success">'.$total_income_row['total_usd_income'].' USD</span> - <span class="badge badge-success">'.$total_income_row['total_irt_income'].' IRT</span>
              <ul class="tree_view" id="IncomeContainer"></ul>
            </li>
            <li>
              <b onclick="load_data_for_report_component(\'Expense\', \'\');">Expense: </b>
              <span class="badge badge-danger">'.$total_expense_row['total_afn_expense'].' AFN</span> - <span class="badge badge-danger">'.$total_expense_row['total_usd_expense'].' USD</span> - <span class="badge badge-danger">'.$total_expense_row['total_irt_expense'].' IRT</span>
              <ul class="tree_view" id="ExpenseContainer"></ul>
            </li>
            <li>
              <b onclick="load_data_for_report_component(\'Exchange\', \'\');">Exchange: </b>
              <span class="badge badge-warning">'.$total_exchange_row['total_afn_from_amount'].' AFN to '.$total_exchange_row['total_usd_to_amount'].' USD</span> - <span class="badge badge-warning">'.$total_exchange_row['total_usd_from_amount'].' USD to '.$total_exchange_row['total_afn_to_amount'].' AFN</span> - <span class="badge badge-warning">'.$total_exchange_row['total_afn_from_amount2'].' AFN to '.$total_exchange_row['total_irt_to_amount'].' IRT</span> - <span class="badge badge-warning">'.$total_exchange_row['total_irt_from_amount'].' IRT to '.$total_exchange_row['total_afn_to_amount2'].' AFN</span>
              <ul class="tree_view" id="ExchangeContainer"></ul>
            </li>
            <li>
              <b onclick="load_data_for_report_component(\'Purchase\', \'\');">Purchase: </b>
              <span class="badge badge-danger">'.$total_purchase_row['total_afn_purchase'].' AFN</span> - <span class="badge badge-danger">'.$total_purchase_row['total_usd_purchase'].' USD</span> - <span class="badge badge-danger">'.$total_purchase_row['total_irt_purchase'].' IRT</span>
              <ul class="tree_view" id="PurchaseContainer"></ul>
            </li>
            <li>
              <b onclick="load_data_for_report_component(\'TransferIn\', \'\');">Transfer In: </b>
              <span class="badge badge-success">'.$total_transfer_row['total_afn_transfer_in'].' AFN</span> - <span class="badge badge-success">'.$total_transfer_row['total_usd_transfer_in'].' USD</span> - <span class="badge badge-success">'.$total_transfer_row['total_irt_transfer_in'].' IRT</span>
              <ul class="tree_view" id="TransferInContainer"></ul>
            </li>
            <li>
              <b onclick="load_data_for_report_component(\'TransferOut\', \'\');">Transfer Out: </b>
              <span class="badge badge-danger">'.$total_transfer_row['total_afn_transfer_out'].' AFN</span> - <span class="badge badge-danger">'.$total_transfer_row['total_usd_transfer_out'].' USD</span> - <span class="badge badge-danger">'.$total_transfer_row['total_irt_transfer_out'].' IRT</span>
              <ul class="tree_view" id="TransferOutContainer"></ul>
            </li>
            ';
            echo $result;
					}break;

					case 'Income':{
						$category_sq = $db->query(
							"SELECT 
							id, category_name
	            FROM `categories` 
	            WHERE deleted='0'
	            AND category_type='Income'"
            );
            while($category_row = $category_sq->fetch()){
            	if(check_access('sub_category_accessibility/income/'.$category_row['id'].'/')==1){

            		$categories_id = $category_row['id'];
            		$total_category_sq = $db->query(
									"SELECT 
									SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) as total_afn_category,
									SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) as total_usd_category,
									SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) as total_irt_category
			            FROM `incomes` 
			            WHERE deleted='0' 
			            AND sub_categories_id IN (
			            	SELECT id FROM sub_categories WHERE deleted='0' AND categories_id='$categories_id'
			            )
			            AND province IN ($accessed_provinces) 
			            AND sub_categories_id IN ($accessed_sub_categories_income)
			            $income_filtering_data"
		            );
		            $total_category_row = $total_category_sq->fetch();

		            $result .= '
	            	<li>
		              <b onclick="load_data_for_report_component(\'IncomeCategory\', \''.$categories_id.'\');">'.$category_row['category_name'].': </b>
		              <span class="badge badge-success">'.$total_category_row['total_afn_category'].' AFN</span> - <span class="badge badge-success">'.$total_category_row['total_usd_category'].' USD</span> - <span class="badge badge-success">'.$total_category_row['total_irt_category'].' IRT</span>
		              <ul class="tree_view" id="IncomeCategory'.$categories_id.'Container"></ul>
		            </li>
	            	';
            	}
            }
            echo $result;
					}break;

					case 'Expense':{
						$category_sq = $db->query(
							"SELECT 
							id, category_name
	            FROM `categories` 
	            WHERE deleted='0'
	            AND category_type='Expense'"
            );
            while($category_row = $category_sq->fetch()){
            	if(check_access('sub_category_accessibility/expense/'.$category_row['id'].'/')==1){

            		$categories_id = $category_row['id'];
            		$total_category_sq = $db->query(
									"SELECT 
									SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) as total_afn_category,
									SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) as total_usd_category,
									SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) as total_irt_category
			            FROM `expenses` 
			            WHERE deleted='0' 
			            AND sub_categories_id IN (
			            	SELECT id FROM sub_categories WHERE deleted='0' AND categories_id='$categories_id'
			            )
			            AND province IN ($accessed_provinces) 
			            AND sub_categories_id IN ($accessed_sub_categories_expense)
			            $expense_filtering_data"
		            );
		            $total_category_row = $total_category_sq->fetch();

		            $result .= '
	            	<li>
		              <b onclick="load_data_for_report_component(\'ExpenseCategory\', \''.$categories_id.'\');">'.$category_row['category_name'].': </b>
		              <span class="badge badge-danger">'.$total_category_row['total_afn_category'].' AFN</span> - <span class="badge badge-danger">'.$total_category_row['total_usd_category'].' USD</span> - <span class="badge badge-danger">'.$total_category_row['total_irt_category'].' IRT</span>
		              <ul class="tree_view" id="ExpenseCategory'.$categories_id.'Container"></ul>
		            </li>
	            	';
            	}
            }
            echo $result;
					}break;

					case 'IncomeCategory':{
						$sub_category_sq = $db->query(
							"SELECT 
							id, sub_category_name
	            FROM `sub_categories` 
	            WHERE deleted='0'
	            AND categories_id='$selector'"
            );
            while($sub_category_row = $sub_category_sq->fetch()){
            	if(check_access('sub_category_accessibility/income/'.$selector.'/'.$sub_category_row['id'].'/')==1){

            		$sub_categories_id = $sub_category_row['id'];
            		$total_sub_category_sq = $db->query(
									"SELECT 
									SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) as total_afn_sub_category,
									SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) as total_usd_sub_category,
									SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) as total_irt_sub_category
			            FROM `incomes` 
			            WHERE deleted='0' 
			            AND sub_categories_id = '$sub_categories_id'
			            AND province IN ($accessed_provinces) 
			            AND sub_categories_id IN ($accessed_sub_categories_income)
			            $income_filtering_data"
		            );
		            $total_sub_category_row = $total_sub_category_sq->fetch();

		            $result .= '
	            	<li>
		              <b onclick="load_data_for_report_component(\'IncomeSubCategory\', \''.$sub_categories_id.'\');">'.$sub_category_row['sub_category_name'].': </b>
		              <span class="badge badge-success">'.$total_sub_category_row['total_afn_sub_category'].' AFN</span> - <span class="badge badge-success">'.$total_sub_category_row['total_usd_sub_category'].' USD</span> - <span class="badge badge-success">'.$total_sub_category_row['total_irt_sub_category'].' IRT</span>
		              <ul class="tree_view" id="IncomeSubCategory'.$sub_categories_id.'Container"></ul>
		            </li>
	            	';
            	}
            }
            echo $result;
					}break;

					case 'ExpenseCategory':{
						$sub_category_sq = $db->query(
							"SELECT 
							id, sub_category_name
	            FROM `sub_categories` 
	            WHERE deleted='0'
	            AND categories_id='$selector'"
            );
            while($sub_category_row = $sub_category_sq->fetch()){
            	if(check_access('sub_category_accessibility/expense/'.$selector.'/'.$sub_category_row['id'].'/')==1){

            		$sub_categories_id = $sub_category_row['id'];
            		$total_sub_category_sq = $db->query(
									"SELECT 
									SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) as total_afn_sub_category,
									SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) as total_usd_sub_category,
									SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) as total_irt_sub_category
			            FROM `expenses` 
			            WHERE deleted='0' 
			            AND sub_categories_id = '$sub_categories_id'
			            AND province IN ($accessed_provinces) 
			            AND sub_categories_id IN ($accessed_sub_categories_expense)
			            $expense_filtering_data"
		            );
		            $total_sub_category_row = $total_sub_category_sq->fetch();

		            $result .= '
	            	<li>
		              <b onclick="load_data_for_report_component(\'ExpenseSubCategory\', \''.$sub_categories_id.'\');">'.$sub_category_row['sub_category_name'].': </b>
		              <span class="badge badge-danger">'.$total_sub_category_row['total_afn_sub_category'].' AFN</span> - <span class="badge badge-danger">'.$total_sub_category_row['total_usd_sub_category'].' USD</span> - <span class="badge badge-danger">'.$total_sub_category_row['total_irt_sub_category'].' IRT</span>
		              <ul class="tree_view" id="ExpenseSubCategory'.$sub_categories_id.'Container"></ul>
		            </li>
	            	';
            	}
            }
            echo $result;
					}break;

					case 'IncomeSubCategory':{
						$income_sq = $db->query(
							"SELECT 
							id, income_amount, currency, description
	            FROM `incomes` 
	            WHERE deleted='0'
	            AND sub_categories_id='$selector'
	            AND province IN ($accessed_provinces) 
			        AND sub_categories_id IN ($accessed_sub_categories_income)
			        $income_filtering_data"
            );
            while($income_row = $income_sq->fetch()){

	            $result .= '
            	<li>
	              <b onclick="load_data_for_report_component(\'IncomeAttribute\', \''.$income_row['id'].'\');" lang="fa" dir="rtl">'.$income_row['description'].': </b>
	              <span class="badge badge-success">'.$income_row['income_amount'].' '.$income_row['currency'].'</span>
	              <ul class="tree_view" id="IncomeAttribute'.$income_row['id'].'Container"></ul>
	            </li>
            	';
            	
            }
            echo $result;
					}break;

					case 'ExpenseSubCategory':{
						$expense_sq = $db->query(
							"SELECT 
							id, expense_amount, currency, description
	            FROM `expenses` 
	            WHERE deleted='0'
	            AND sub_categories_id='$selector'
	            AND province IN ($accessed_provinces) 
			        AND sub_categories_id IN ($accessed_sub_categories_expense)
			        $expense_filtering_data"
            );
            while($expense_row = $expense_sq->fetch()){

	            $result .= '
            	<li>
	              <b onclick="load_data_for_report_component(\'ExpenseAttribute\', \''.$expense_row['id'].'\');" lang="fa" dir="rtl">'.$expense_row['description'].': </b>
	              <span class="badge badge-danger">'.$expense_row['expense_amount'].' '.$expense_row['currency'].'</span>
	              <ul class="tree_view" id="ExpenseAttribute'.$expense_row['id'].'Container"></ul>
	            </li>
            	';
            	
            }
            echo $result;
					}break;

					case 'Exchange':{
						$exchange_sq = $db->query(
							"SELECT 
							id, from_amount, to_amount, from_currency, to_currency, description
	            FROM `exchanges` 
              WHERE deleted='0'
              AND province IN ($accessed_provinces) 
              $accessed_sub_categories_exchange
              $exchange_filtering_data"
            );
            while($exchange_row = $exchange_sq->fetch()){

	            $result .= '
            	<li>
	              <b onclick="load_data_for_report_component(\'ExchangeAttribute\', \''.$exchange_row['id'].'\');" lang="fa" dir="rtl">'.$exchange_row['description'].': </b>
	              <span class="badge badge-warning">'.$exchange_row['from_amount'].' '.$exchange_row['from_currency'].' to '.$exchange_row['to_amount'].' '.$exchange_row['to_currency'].'</span>
	              <ul class="tree_view" id="ExchangeAttribute'.$exchange_row['id'].'Container"></ul>
	            </li>
            	';
            	
            }
            echo $result;
					}break;

					case 'Purchase':{
						$category_sq = $db->query(
							"SELECT 
							id, category_name
	            FROM `categories` 
	            WHERE deleted='0'
	            AND category_type='Purchase'"
            );
            while($category_row = $category_sq->fetch()){
            	if(check_access('sub_category_accessibility/purchase/'.$category_row['id'].'/')==1){

            		$categories_id = $category_row['id'];
            		$total_category_sq = $db->query(
									"SELECT 
									SUM(CASE WHEN currency='AFN' THEN purchase_amount ELSE 0 END) as total_afn_category,
									SUM(CASE WHEN currency='USD' THEN purchase_amount ELSE 0 END) as total_usd_category,
									SUM(CASE WHEN currency='IRT' THEN purchase_amount ELSE 0 END) as total_irt_category
			            FROM `purchases` 
			            WHERE deleted='0' 
			            AND is_approved='1'
    							AND is_included='1'
			            AND sub_categories_id IN (
			            	SELECT id FROM sub_categories WHERE deleted='0' AND categories_id='$categories_id'
			            )
			            AND province IN ($accessed_provinces) 
			            AND sub_categories_id IN ($accessed_sub_categories_purchase)
			            AND logistic_cashes_id IN ($accessed_logistic_cashes)
			            $purchase_filtering_data"
		            );
		            $total_category_row = $total_category_sq->fetch();

		            $result .= '
	            	<li>
		              <b onclick="load_data_for_report_component(\'PurchaseCategory\', \''.$categories_id.'\');">'.$category_row['category_name'].': </b>
		              <span class="badge badge-danger">'.$total_category_row['total_afn_category'].' AFN</span> - <span class="badge badge-danger">'.$total_category_row['total_usd_category'].' USD</span> - <span class="badge badge-danger">'.$total_category_row['total_irt_category'].' IRT</span>
		              <ul class="tree_view" id="PurchaseCategory'.$categories_id.'Container"></ul>
		            </li>
	            	';
            	}
            }
            echo $result;
					}break;

					case 'PurchaseCategory':{
						$sub_category_sq = $db->query(
							"SELECT 
							id, sub_category_name
	            FROM `sub_categories` 
	            WHERE deleted='0'
	            AND categories_id='$selector'"
            );
            while($sub_category_row = $sub_category_sq->fetch()){
            	if(check_access('sub_category_accessibility/purchase/'.$selector.'/'.$sub_category_row['id'].'/')==1){

            		$sub_categories_id = $sub_category_row['id'];
            		$total_sub_category_sq = $db->query(
									"SELECT 
									SUM(CASE WHEN currency='AFN' THEN purchase_amount ELSE 0 END) as total_afn_sub_category,
									SUM(CASE WHEN currency='USD' THEN purchase_amount ELSE 0 END) as total_usd_sub_category,
									SUM(CASE WHEN currency='IRT' THEN purchase_amount ELSE 0 END) as total_irt_sub_category
			            FROM `purchases` 
			            WHERE deleted='0' 
			            AND is_approved='1'
    							AND is_included='1'
			            AND sub_categories_id = '$sub_categories_id'
			            AND province IN ($accessed_provinces) 
			            AND sub_categories_id IN ($accessed_sub_categories_purchase)
			            AND logistic_cashes_id IN ($accessed_logistic_cashes)
			            $purchase_filtering_data"
		            );
		            $total_sub_category_row = $total_sub_category_sq->fetch();

		            $result .= '
	            	<li>
		              <b onclick="load_data_for_report_component(\'PurchaseSubCategory\', \''.$sub_categories_id.'\');">'.$sub_category_row['sub_category_name'].': </b>
		              <span class="badge badge-danger">'.$total_sub_category_row['total_afn_sub_category'].' AFN</span> - <span class="badge badge-danger">'.$total_sub_category_row['total_usd_sub_category'].' USD</span> - <span class="badge badge-danger">'.$total_sub_category_row['total_irt_sub_category'].' IRT</span>
		              <ul class="tree_view" id="PurchaseSubCategory'.$sub_categories_id.'Container"></ul>
		            </li>
	            	';
            	}
            }
            echo $result;
					}break;

					case 'PurchaseSubCategory':{
						$purchase_sq = $db->query(
							"SELECT 
							id, purchase_amount, currency, description
	            FROM `purchases` 
	            WHERE deleted='0'
	            AND is_approved='1'
    					AND is_included='1'
	            AND sub_categories_id='$selector'
	            AND province IN ($accessed_provinces) 
			        AND sub_categories_id IN ($accessed_sub_categories_purchase)
			        AND logistic_cashes_id IN ($accessed_logistic_cashes)
			        $purchase_filtering_data"
            );
            while($purchase_row = $purchase_sq->fetch()){

	            $result .= '
            	<li>
	              <b onclick="load_data_for_report_component(\'PurchaseAttribute\', \''.$purchase_row['id'].'\');" lang="fa" dir="rtl">'.$purchase_row['description'].': </b>
	              <span class="badge badge-danger">'.$purchase_row['purchase_amount'].' '.$purchase_row['currency'].'</span>
	              <ul class="tree_view" id="PurchaseAttribute'.$purchase_row['id'].'Container"></ul>
	            </li>
            	';
            	
            }
            echo $result;
					}break;

					case 'TransferIn':{
						$transfer_sq = $db->query(
							"SELECT 
							id, transfer_amount, currency, description
	            FROM `transfers` 
              WHERE transfers.deleted='0' 
              AND transfers.is_approved='1'
				      AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
				      AND to_province='$province'
				      $transfer_filtering_data
				      $accessed_sub_categories_transfer"
            );
            while($transfer_row = $transfer_sq->fetch()){

	            $result .= '
            	<li>
	              <b onclick="load_data_for_report_component(\'TransferInAttribute\', \''.$transfer_row['id'].'\');" lang="fa" dir="rtl">'.$transfer_row['description'].': </b>
	              <span class="badge badge-success">'.$transfer_row['transfer_amount'].' '.$transfer_row['currency'].'</span>
	              <ul class="tree_view" id="TransferInAttribute'.$transfer_row['id'].'Container"></ul>
	            </li>
            	';
            	
            }
            echo $result;
					}break;

					case 'TransferOut':{
						$transfer_sq = $db->query(
							"SELECT 
							id, transfer_amount, currency, description
	            FROM `transfers` 
              WHERE transfers.deleted='0' 
              AND transfers.is_approved='1'
				      AND (transfers.from_province IN ($accessed_provinces) OR transfers.to_province IN ($accessed_provinces))
				      AND from_province='$province'
				      $transfer_filtering_data
				      $accessed_sub_categories_transfer"
            );
            while($transfer_row = $transfer_sq->fetch()){

	            $result .= '
            	<li>
	              <b onclick="load_data_for_report_component(\'TransferOutAttribute\', \''.$transfer_row['id'].'\');" lang="fa" dir="rtl">'.$transfer_row['description'].': </b>
	              <span class="badge badge-danger">'.$transfer_row['transfer_amount'].' '.$transfer_row['currency'].'</span>
	              <ul class="tree_view" id="TransferOutAttribute'.$transfer_row['id'].'Container"></ul>
	            </li>
            	';
            	
            }
            echo $result;
					}break;



					case 'IncomeAttribute':{



						$income_attribute_sq = $db->query(
							"SELECT * 
              FROM `incomes` 
              WHERE id='$selector'
              LIMIT 1");
            $income_attribute_row = $income_attribute_sq->fetch();

            $additional_informations = '';
						$additional_information_sq = $db->prepare("SELECT key_info, value_info FROM `additional_informations` WHERE deleted='0' AND reference_type='Income' AND reference_id=:reference_id");
            $additional_information_sqx = $additional_information_sq->execute([
              'reference_id'=>$income_attribute_row['id']
            ]);
            if($additional_information_sq->rowCount()>0){
              while($additional_information_row = $additional_information_sq->fetch()){
                $additional_informations .= '
                  <li>
                    '.$additional_information_row['key_info'].': <span class="badge badge-success">'.$additional_information_row['value_info'].'
                  </li>
                ';
              }
            }

            $result .= '
          	<li>
              Province: <span class="badge badge-success">'.$income_attribute_row['province'].'
            </li>
            <li>
              Category: <span class="badge badge-success">'.get_col('sub_categories', 'sub_category_name', 'id', $income_attribute_row['sub_categories_id']).'
            </li>
            <li>
              Sub Category: <span class="badge badge-success">'.get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $income_attribute_row['sub_categories_id'])).'
            </li>
            <li>
              Date: <span class="badge badge-success">'.$income_attribute_row['income_date'].'
            </li>
            <li>
              Amount: <span class="badge badge-success">'.$income_attribute_row['income_amount'].'
            </li>
            <li>
              Currency: <span class="badge badge-success">'.$income_attribute_row['currency'].'
            </li>
            <li>
              Check Number: <span class="badge badge-success">'.$income_attribute_row['check_number'].'
            </li>
            <li>
              SIB Number: <span class="badge badge-success">'.$income_attribute_row['sib_number'].'
            </li>
            <li>
              Description: <span class="badge badge-success">'.$income_attribute_row['description'].'
            </li>
            '.$additional_informations.'
          	';
            	
            
            echo $result;
					}break;

					case 'ExpenseAttribute':{

						$expense_attribute_sq = $db->query(
							"SELECT * 
              FROM `expenses` 
              WHERE id='$selector'
              LIMIT 1");
            $expense_attribute_row = $expense_attribute_sq->fetch();

            

            $result .= '
          	<li>
              Province: <span class="badge badge-danger">'.$expense_attribute_row['province'].'
            </li>
            <li>
              Category: <span class="badge badge-danger">'.get_col('sub_categories', 'sub_category_name', 'id', $expense_attribute_row['sub_categories_id']).'
            </li>
            <li>
              Sub Category: <span class="badge badge-danger">'.get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $expense_attribute_row['sub_categories_id'])).'
            </li>
            <li>
              Date: <span class="badge badge-danger">'.$expense_attribute_row['expense_date'].'
            </li>
            <li>
              Amount: <span class="badge badge-danger">'.$expense_attribute_row['expense_amount'].'
            </li>
            <li>
              Currency: <span class="badge badge-danger">'.$expense_attribute_row['currency'].'
            </li>
            <li>
              Check Number: <span class="badge badge-danger">'.$expense_attribute_row['check_number'].'
            </li>
            <li>
              Description: <span class="badge badge-danger">'.$expense_attribute_row['description'].'
            </li>
          	';
            	
            
            echo $result;
					}break;

					case 'ExchangeAttribute':{

						$exchange_attribute_sq = $db->query(
							"SELECT * 
              FROM `exchanges` 
              WHERE id='$selector'
              LIMIT 1");
            $exchange_attribute_row = $exchange_attribute_sq->fetch();

            

            $result .= '
          	<li>
              Province: <span class="badge badge-warning">'.$exchange_attribute_row['province'].'
            </li>
            <li>
              Date: <span class="badge badge-warning">'.$exchange_attribute_row['exchange_date'].'
            </li>
            <li>
              From Amount: <span class="badge badge-warning">'.$exchange_attribute_row['from_amount'].'
            </li>
            <li>
              From Currency: <span class="badge badge-warning">'.$exchange_attribute_row['from_currency'].'
            </li>
            <li>
              To Amount: <span class="badge badge-warning">'.$exchange_attribute_row['to_amount'].'
            </li>
            <li>
              To Currency: <span class="badge badge-warning">'.$exchange_attribute_row['to_currency'].'
            </li>
            <li>
              Description: <span class="badge badge-warning">'.$exchange_attribute_row['description'].'
            </li>
          	';
            	
            
            echo $result;
					}break;

					

					case 'PurchaseAttribute':{

						$purchase_attribute_sq = $db->query(
							"SELECT * 
              FROM `purchases` 
              WHERE id='$selector'
              LIMIT 1");
            $purchase_attribute_row = $purchase_attribute_sq->fetch();

            

            $result .= '
            <li>
              Logistic Cash: <span class="badge badge-danger">'.get_col('logistic_cashes', 'name', 'id', $purchase_attribute_row['logistic_cashes_id']).'
            </li>
          	<li>
              Province: <span class="badge badge-danger">'.$purchase_attribute_row['province'].'
            </li>
            <li>
              Category: <span class="badge badge-danger">'.get_col('sub_categories', 'sub_category_name', 'id', $purchase_attribute_row['sub_categories_id']).'
            </li>
            <li>
              Sub Category: <span class="badge badge-danger">'.get_col('categories', 'category_name', 'id', get_col('sub_categories', 'categories_id', 'id', $purchase_attribute_row['sub_categories_id'])).'
            </li>
            <li>
              Date: <span class="badge badge-danger">'.$purchase_attribute_row['purchase_date'].'
            </li>
            <li>
              Amount: <span class="badge badge-danger">'.$purchase_attribute_row['purchase_amount'].'
            </li>
            <li>
              Currency: <span class="badge badge-danger">'.$purchase_attribute_row['currency'].'
            </li>
            <li>
              Check Number: <span class="badge badge-danger">'.$purchase_attribute_row['check_number'].'
            </li>
            <li>
              Description: <span class="badge badge-danger">'.$purchase_attribute_row['description'].'
            </li>
          	';
            	
            
            echo $result;
					}break;

					case 'TransferInAttribute':{

						$transfer_attribute_sq = $db->query(
							"SELECT * 
              FROM `transfers` 
              WHERE id='$selector'
              LIMIT 1");
            $transfer_attribute_row = $transfer_attribute_sq->fetch();

            

            $result .= '
            <li>
              From Province: <span class="badge badge-success">'.$transfer_attribute_row['from_province'].'
            </li>
            <li>
              To Province: <span class="badge badge-success">'.$transfer_attribute_row['to_province'].'
            </li>
            <li>
              Date: <span class="badge badge-success">'.$transfer_attribute_row['transfer_date'].'
            </li>
            <li>
              Amount: <span class="badge badge-success">'.$transfer_attribute_row['transfer_amount'].'
            </li>
            <li>
              Currency: <span class="badge badge-success">'.$transfer_attribute_row['currency'].'
            </li>
            <li>
              Description: <span class="badge badge-success">'.$transfer_attribute_row['description'].'
            </li>
          	';
            	
            
            echo $result;
					}break;

					case 'TransferOutAttribute':{

						$transfer_attribute_sq = $db->query(
							"SELECT * 
              FROM `transfers` 
              WHERE id='$selector'
              LIMIT 1");
            $transfer_attribute_row = $transfer_attribute_sq->fetch();

            

            $result .= '
            <li>
              From Province: <span class="badge badge-danger">'.$transfer_attribute_row['from_province'].'
            </li>
            <li>
              To Province: <span class="badge badge-danger">'.$transfer_attribute_row['to_province'].'
            </li>
            <li>
              Date: <span class="badge badge-danger">'.$transfer_attribute_row['transfer_date'].'
            </li>
            <li>
              Amount: <span class="badge badge-danger">'.$transfer_attribute_row['transfer_amount'].'
            </li>
            <li>
              Currency: <span class="badge badge-danger">'.$transfer_attribute_row['currency'].'
            </li>
            <li>
              Description: <span class="badge badge-danger">'.$transfer_attribute_row['description'].'
            </li>
          	';
            	
            
            echo $result;
					}break;
					
					default:{

					}break;
				}
				
			}break;

			case "get_sub_cat_conf": {

				$sub_categories_id = holu_escape($_POST['sub_categories_id']);

				$result = '';

				$sub_category_aii_sq = $db->query(
					"SELECT *
					FROM sub_category_aiis
					WHERE deleted='0'
					AND sub_categories_id='$sub_categories_id'"
				);

				if($sub_category_aii_sq->rowCount()>0){
					while($sub_category_aii_row = $sub_category_aii_sq->fetch()){

						$result .= get_ai_input('id', $sub_category_aii_row['additional_information_items_id'], '');


						
					}
				}

				echo $result;

			}break;

			case "get_dashboard_closing_balance": {
				$result = array();
				foreach ($holu_provinces as $holu_province) :
          if (check_access('province_accessibility/' . $holu_province . '/') != 1)
            continue;
          else

          
          
          $accessed_province = $holu_province;

          $closing_income_sq = $db->query(
            "SELECT 
            SUM(CASE WHEN currency='AFN' THEN income_amount ELSE 0 END) AS closing_income_afn,
            SUM(CASE WHEN currency='USD' THEN income_amount ELSE 0 END) AS closing_income_usd,
            SUM(CASE WHEN currency='IRT' THEN income_amount ELSE 0 END) AS closing_income_irt
            FROM `incomes`
            WHERE incomes.deleted='0' 
            AND incomes.province IN ('$accessed_province') 
            AND incomes.sub_categories_id IN ($accessed_sub_categories_income)"
          );
          $closing_income_row = $closing_income_sq->fetch();

          $closing_expense_sq = $db->query(
            "SELECT 
            SUM(CASE WHEN currency='AFN' THEN expense_amount ELSE 0 END) AS closing_expense_afn,
            SUM(CASE WHEN currency='USD' THEN expense_amount ELSE 0 END) AS closing_expense_usd,
            SUM(CASE WHEN currency='IRT' THEN expense_amount ELSE 0 END) AS closing_expense_irt
            FROM `expenses`
            WHERE expenses.deleted='0' 
            AND expenses.province IN ('$accessed_province') 
            AND expenses.sub_categories_id IN ($accessed_sub_categories_expense)"
          );
          $closing_expense_row = $closing_expense_sq->fetch();

          $closing_exchange_sq = $db->query(
            "SELECT 
              SUM(CASE WHEN from_currency='AFN' AND to_currency='USD' THEN from_amount ELSE 0 END) AS closing_from_afn,
              SUM(CASE WHEN from_currency='AFN' AND to_currency='IRT' THEN from_amount ELSE 0 END) AS closing_from_afn2,
              SUM(CASE WHEN from_currency='USD' AND to_currency='AFN' THEN from_amount ELSE 0 END) AS closing_from_usd,
              SUM(CASE WHEN from_currency='IRT' AND to_currency='AFN' THEN from_amount ELSE 0 END) AS closing_from_irt,
              SUM(CASE WHEN to_currency='AFN' AND from_currency='USD' THEN to_amount ELSE 0 END) AS closing_to_afn,
              SUM(CASE WHEN to_currency='AFN' AND from_currency='IRT' THEN to_amount ELSE 0 END) AS closing_to_afn2,
              SUM(CASE WHEN to_currency='USD' AND from_currency='AFN' THEN to_amount ELSE 0 END) AS closing_to_usd,
              SUM(CASE WHEN to_currency='IRT' AND from_currency='AFN' THEN to_amount ELSE 0 END) AS closing_to_irt
            FROM `exchanges`
            WHERE exchanges.deleted='0' 
            AND exchanges.province IN ('$accessed_province') 
            $accessed_sub_categories_exchange"
          );
          $closing_exchange_row = $closing_exchange_sq->fetch();

          $closing_purchase_sq = $db->query(
            "SELECT 
              SUM(CASE WHEN currency='AFN' THEN purchase_amount ELSE 0 END) AS closing_purchase_afn,
              SUM(CASE WHEN currency='USD' THEN purchase_amount ELSE 0 END) AS closing_purchase_usd,
              SUM(CASE WHEN currency='IRT' THEN purchase_amount ELSE 0 END) AS closing_purchase_irt
            FROM `purchases`
            WHERE purchases.deleted='0' 
            AND purchases.is_approved='1'
            AND purchases.is_included='1'
            AND purchases.province IN ('$accessed_province') 
            AND purchases.sub_categories_id IN ($accessed_sub_categories_purchase)"
          );
          $closing_purchase_row = $closing_purchase_sq->fetch();

          $closing_transfer_sq = $db->query(
            "SELECT 
              SUM(CASE WHEN currency='AFN' AND from_province='$accessed_province' THEN transfer_amount ELSE 0 END) AS closing_transfer_out_afn,
              SUM(CASE WHEN currency='AFN' AND to_province='$accessed_province' THEN transfer_amount ELSE 0 END) AS closing_transfer_in_afn,
              SUM(CASE WHEN currency='USD' AND from_province='$accessed_province' THEN transfer_amount ELSE 0 END) AS closing_transfer_out_usd,
              SUM(CASE WHEN currency='USD' AND to_province='$accessed_province' THEN transfer_amount ELSE 0 END) AS closing_transfer_in_usd,
              SUM(CASE WHEN currency='IRT' AND from_province='$accessed_province' THEN transfer_amount ELSE 0 END) AS closing_transfer_out_irt,
              SUM(CASE WHEN currency='IRT' AND to_province='$accessed_province' THEN transfer_amount ELSE 0 END) AS closing_transfer_in_irt
            FROM `transfers`
            WHERE transfers.deleted='0' 
            AND transfers.is_approved='1'
            AND (transfers.from_province IN ('$accessed_province') OR transfers.to_province IN ('$accessed_province'))
            $accessed_sub_categories_transfer"
          );
          $closing_transfer_row = $closing_transfer_sq->fetch();

          $closing_afn = $closing_income_row['closing_income_afn']
            - $closing_expense_row['closing_expense_afn']
            - $closing_purchase_row['closing_purchase_afn']
            + $closing_exchange_row['closing_to_afn']
            + $closing_exchange_row['closing_to_afn2']
            - $closing_exchange_row['closing_from_afn']
            - $closing_exchange_row['closing_from_afn2']
            - $closing_transfer_row['closing_transfer_out_afn']
            + $closing_transfer_row['closing_transfer_in_afn'];

          $closing_usd = $closing_income_row['closing_income_usd']
            - $closing_expense_row['closing_expense_usd']
            - $closing_purchase_row['closing_purchase_usd']
            + $closing_exchange_row['closing_to_usd']
            - $closing_exchange_row['closing_from_usd']
            - $closing_transfer_row['closing_transfer_out_usd']
            + $closing_transfer_row['closing_transfer_in_usd'];

          $closing_irt = $closing_income_row['closing_income_irt']
            - $closing_expense_row['closing_expense_irt']
            - $closing_purchase_row['closing_purchase_irt']
            + $closing_exchange_row['closing_to_irt']
            - $closing_exchange_row['closing_from_irt']
            - $closing_transfer_row['closing_transfer_out_irt']
            + $closing_transfer_row['closing_transfer_in_irt'];


          $result['closing_balance_AFN_'.$accessed_province] = number_format($closing_afn, 2);
          $result['closing_balance_USD_'.$accessed_province] = number_format($closing_usd, 2);
          $result['closing_balance_IRT_'.$accessed_province] = number_format($closing_irt, 2);
        
        endforeach;

        echo json_encode($result);

			}break;

			case 'get_dashboard_highest_expenses_field':{

				$result = '';

				$result = '
					<div class="form-group row">
            <div class="col-sm-12">
              <select id="highest_expense_province" name="highest_expense_province" class="form-control" required onchange="get_dashboard_highest_expenses_table();">
                <option selected hidden value="">Select an option</option>
                '.get_province_option($holu_provinces[0]).'
              </select>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-12">
              <select id="highest_expense_currency" name="highest_expense_currency" class="form-control" required onchange="get_dashboard_highest_expenses_table();">
                <option selected hidden value="">Select an option</option>
                '.get_currency_option($holu_currencies[0]).'
              </select>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-12">
              <input type="text" id="highest_expense_expense_date" name="highest_expense_expense_date" class="form-control date_picker" placeholder="Pick a date..." required value="'.date("Y-m-d").'" onchange="get_dashboard_highest_expenses_table();">
            </div>
          </div>
				';

				echo $result;
			}break;

			case 'get_dashboard_highest_expenses_table':{

				$result = '';

				$filtering_data = '';

				if (isset($_POST['highest_expense_province']) AND !empty($_POST['highest_expense_province'])) {
					$province = holu_escape($_POST['highest_expense_province']);
					$filtering_data .= " AND expenses.province='".$province."' ";
				}

				if (isset($_POST['highest_expense_currency']) AND !empty($_POST['highest_expense_currency'])) {
					$currency = holu_escape($_POST['highest_expense_currency']);
					$filtering_data .= " AND expenses.currency='".$currency."' ";
				}

				if (isset($_POST['highest_expense_expense_date']) AND !empty($_POST['highest_expense_expense_date'])) {
					$expense_date = holu_escape($_POST['highest_expense_expense_date']);
					$filtering_data .= " AND expenses.expense_date='".$expense_date."' ";
				}

				$result .= '
					
          <table class="table table-bordered table-sm mb-0">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Description</th>
                <th>Province</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Check Number</th>
              </tr>
            </thead>
            <tbody>';

              $expense_sq = $db->query("SELECT * FROM `expenses` WHERE deleted='0' $filtering_data AND province IN ($accessed_provinces) AND sub_categories_id IN ($accessed_sub_categories_expense) ORDER BY expense_amount DESC limit 10");

              if($expense_sq->rowCount()>0){
                $holu_count = 1;
                while($expense_row = $expense_sq->fetch()){

                  $result .= '
                  <tr>
                    <th class="text-center">'.$holu_count++.'</th>
                    <td class="text-right"><span lang="fa" dir="rtl">'.$expense_row['description'].'</span></td>
                    <td>'.$expense_row['province'].'</td>
                    <td>'.$expense_row['expense_amount'].'</td>
                    <td>'.$expense_row['currency'].'</td>
                    <td>'.$expense_row['check_number'].'</td>
                  </tr>
                  ';
                }
              }else
              {
                $result .= '
                <tr>
                  <th class="text-center" colspan="100">No data to show</th>
                </tr>
                ';
              }
              
              $result .= '
            </tbody>
          </table>
				';

				echo $result;
			}break;

			case 'get_dashboard_income_in_province':{
				$data = '';
				$background_color = '';
				$labels = '';
				foreach ($holu_provinces as $holu_province) :
          if (check_access('province_accessibility/' . $holu_province . '/') != 1)
            continue;
          else

          	$income_sq = $db->query("SELECT count(id) AS num_income FROM `incomes` WHERE deleted='0' AND province='$holu_province' AND province IN ($accessed_provinces) AND sub_categories_id IN ($accessed_sub_categories_income)");

          	if($income_sq->rowCount()>0){
              $income_row = $income_sq->fetch();
              $data .= $income_row['num_income'].',';
            }else{
            	$data .= '0,';
            }

          	
          	$background_color .= '\''.get_random_color().'\',';
          	$labels .= '"'.$holu_province.'",';
        
        endforeach;

				$result = '
		    <script>
		      $("#canvas_income_in_province_donut").remove(); 
		      $("#canvas_income_in_province_donut_spinner").remove(); 
		      $("#container_income_in_province_donut").append("<canvas id=\'canvas_income_in_province_donut\'><canvas>");

		      var ctx_income_in_province_donut = document.getElementById(\'canvas_income_in_province_donut\').getContext(\'2d\');
		      var income_in_province_donut = new Chart(ctx_income_in_province_donut, {
		        type: "doughnut",
		        data: {
		            datasets: [{
		                data: [
		                  '.$data.'
		                ],
		                backgroundColor: [
		                  '.$background_color.'
		                ],
		                label: "Dataset 1"
		            }],
		            labels: [
		              '.$labels.'
		            ]
		        },
		        options: {
		            responsive: true,
		            legend: {
		                position: "right",
		            },
		            maintainAspectRatio: false,
		            animation: {
		                animateScale: true,
		                animateRotate: true
		            }
		        }
		      });
		    </script>
		    ';

		    echo $result;
			}break;

			case 'get_dashboard_expense_in_province':{
				$data = '';
				$background_color = '';
				$labels = '';
				foreach ($holu_provinces as $holu_province) :
          if (check_access('province_accessibility/' . $holu_province . '/') != 1)
            continue;
          else

          	$expense_sq = $db->query("SELECT count(id) AS num_expense FROM `expenses` WHERE deleted='0' AND province='$holu_province' AND province IN ($accessed_provinces) AND sub_categories_id IN ($accessed_sub_categories_expense)");

          	if($expense_sq->rowCount()>0){
              $expense_row = $expense_sq->fetch();
              $data .= $expense_row['num_expense'].',';
            }else{
            	$data .= '0,';
            }

          	
          	$background_color .= '\''.get_random_color().'\',';
          	$labels .= '"'.$holu_province.'",';
        
        endforeach;

				$result = '
		    <script>
		      $("#canvas_expense_in_province_donut").remove(); 
		      $("#canvas_expense_in_province_donut_spinner").remove(); 
		      $("#container_expense_in_province_donut").append("<canvas id=\'canvas_expense_in_province_donut\'><canvas>");

		      var ctx_expense_in_province_donut = document.getElementById(\'canvas_expense_in_province_donut\').getContext(\'2d\');
		      var expense_in_province_donut = new Chart(ctx_expense_in_province_donut, {
		        type: "doughnut",
		        data: {
		            datasets: [{
		                data: [
		                  '.$data.'
		                ],
		                backgroundColor: [
		                  '.$background_color.'
		                ],
		                label: "Dataset 1"
		            }],
		            labels: [
		              '.$labels.'
		            ]
		        },
		        options: {
		            responsive: true,
		            legend: {
		                position: "right",
		            },
		            maintainAspectRatio: false,
		            animation: {
		                animateScale: true,
		                animateRotate: true
		            }
		        }
		      });
		    </script>
		    ';

		    echo $result;
			}break;

			case 'get_dashboard_purchase_in_province':{
				$data = '';
				$background_color = '';
				$labels = '';
				foreach ($holu_provinces as $holu_province) :
          if (check_access('province_accessibility/' . $holu_province . '/') != 1)
            continue;
          else

          	$purchase_sq = $db->query("SELECT count(id) AS num_purchase FROM purchases WHERE deleted='0' AND province='$holu_province' AND province IN ($accessed_provinces) AND sub_categories_id IN ($accessed_sub_categories_purchase)");

          	if($purchase_sq->rowCount()>0){
              $purchase_row = $purchase_sq->fetch();
              $data .= $purchase_row['num_purchase'].',';
            }else{
            	$data .= '0,';
            }

          	
          	$background_color .= '\''.get_random_color().'\',';
          	$labels .= '"'.$holu_province.'",';
        
        endforeach;

				$result = '
		    <script>
		      $("#canvas_purchase_in_province_donut").remove(); 
		      $("#canvas_purchase_in_province_donut_spinner").remove(); 
		      $("#container_purchase_in_province_donut").append("<canvas id=\'canvas_purchase_in_province_donut\'><canvas>");

		      var ctx_purchase_in_province_donut = document.getElementById(\'canvas_purchase_in_province_donut\').getContext(\'2d\');
		      var purchase_in_province_donut = new Chart(ctx_purchase_in_province_donut, {
		        type: "doughnut",
		        data: {
		            datasets: [{
		                data: [
		                  '.$data.'
		                ],
		                backgroundColor: [
		                  '.$background_color.'
		                ],
		                label: "Dataset 1"
		            }],
		            labels: [
		              '.$labels.'
		            ]
		        },
		        options: {
		            responsive: true,
		            legend: {
		                position: "right",
		            },
		            maintainAspectRatio: false,
		            animation: {
		                animateScale: true,
		                animateRotate: true
		            }
		        }
		      });
		    </script>
		    ';

		    echo $result;
			}break;

			case 'get_dashboard_exchange_in_province':{
				$data = '';
				$background_color = '';
				$labels = '';
				foreach ($holu_provinces as $holu_province) :
          if (check_access('province_accessibility/' . $holu_province . '/') != 1)
            continue;
          else

          	$exchange_sq = $db->query("SELECT count(id) AS num_exchange FROM exchanges WHERE deleted='0' AND province='$holu_province' AND province IN ($accessed_provinces)");

          	if($exchange_sq->rowCount()>0){
              $exchange_row = $exchange_sq->fetch();
              $data .= $exchange_row['num_exchange'].',';
            }else{
            	$data .= '0,';
            }

          	
          	$background_color .= '\''.get_random_color().'\',';
          	$labels .= '"'.$holu_province.'",';
        
        endforeach;

				$result = '
		    <script>
		      $("#canvas_exchange_in_province_donut").remove(); 
		      $("#canvas_exchange_in_province_donut_spinner").remove(); 
		      $("#container_exchange_in_province_donut").append("<canvas id=\'canvas_exchange_in_province_donut\'><canvas>");

		      var ctx_exchange_in_province_donut = document.getElementById(\'canvas_exchange_in_province_donut\').getContext(\'2d\');
		      var exchange_in_province_donut = new Chart(ctx_exchange_in_province_donut, {
		        type: "doughnut",
		        data: {
		            datasets: [{
		                data: [
		                  '.$data.'
		                ],
		                backgroundColor: [
		                  '.$background_color.'
		                ],
		                label: "Dataset 1"
		            }],
		            labels: [
		              '.$labels.'
		            ]
		        },
		        options: {
		            responsive: true,
		            legend: {
		                position: "right",
		            },
		            maintainAspectRatio: false,
		            animation: {
		                animateScale: true,
		                animateRotate: true
		            }
		        }
		      });
		    </script>
		    ';

		    echo $result;
			}break;

			case 'get_dashboard_transferin_in_province':{
				$data = '';
				$background_color = '';
				$labels = '';
				foreach ($holu_provinces as $holu_province) :
          if (check_access('province_accessibility/' . $holu_province . '/') != 1)
            continue;
          else

          	$transferin_sq = $db->query("SELECT count(id) AS num_transferin FROM transfers WHERE deleted='0' AND to_province='$holu_province' AND to_province IN ($accessed_provinces)");

          	if($transferin_sq->rowCount()>0){
              $transferin_row = $transferin_sq->fetch();
              $data .= $transferin_row['num_transferin'].',';
            }else{
            	$data .= '0,';
            }

          	
          	$background_color .= '\''.get_random_color().'\',';
          	$labels .= '"'.$holu_province.'",';
        
        endforeach;

				$result = '
		    <script>
		      $("#canvas_transferin_in_province_donut").remove(); 
		      $("#canvas_transferin_in_province_donut_spinner").remove(); 
		      $("#container_transferin_in_province_donut").append("<canvas id=\'canvas_transferin_in_province_donut\'><canvas>");

		      var ctx_transferin_in_province_donut = document.getElementById(\'canvas_transferin_in_province_donut\').getContext(\'2d\');
		      var transferin_in_province_donut = new Chart(ctx_transferin_in_province_donut, {
		        type: "doughnut",
		        data: {
		            datasets: [{
		                data: [
		                  '.$data.'
		                ],
		                backgroundColor: [
		                  '.$background_color.'
		                ],
		                label: "Dataset 1"
		            }],
		            labels: [
		              '.$labels.'
		            ]
		        },
		        options: {
		            responsive: true,
		            legend: {
		                position: "right",
		            },
		            maintainAspectRatio: false,
		            animation: {
		                animateScale: true,
		                animateRotate: true
		            }
		        }
		      });
		    </script>
		    ';

		    echo $result;
			}break;

			case 'get_dashboard_transferout_in_province':{
				$data = '';
				$background_color = '';
				$labels = '';
				foreach ($holu_provinces as $holu_province) :
          if (check_access('province_accessibility/' . $holu_province . '/') != 1)
            continue;
          else

          	$transferout_sq = $db->query("SELECT count(id) AS num_transferout FROM transfers WHERE deleted='0' AND from_province='$holu_province' AND from_province IN ($accessed_provinces)");

          	if($transferout_sq->rowCount()>0){
              $transferout_row = $transferout_sq->fetch();
              $data .= $transferout_row['num_transferout'].',';
            }else{
            	$data .= '0,';
            }

          	
          	$background_color .= '\''.get_random_color().'\',';
          	$labels .= '"'.$holu_province.'",';
        
        endforeach;

				$result = '
		    <script>
		      $("#canvas_transferout_in_province_donut").remove(); 
		      $("#canvas_transferout_in_province_donut_spinner").remove(); 
		      $("#container_transferout_in_province_donut").append("<canvas id=\'canvas_transferout_in_province_donut\'><canvas>");

		      var ctx_transferout_in_province_donut = document.getElementById(\'canvas_transferout_in_province_donut\').getContext(\'2d\');
		      var transferout_in_province_donut = new Chart(ctx_transferout_in_province_donut, {
		        type: "doughnut",
		        data: {
		            datasets: [{
		                data: [
		                  '.$data.'
		                ],
		                backgroundColor: [
		                  '.$background_color.'
		                ],
		                label: "Dataset 1"
		            }],
		            labels: [
		              '.$labels.'
		            ]
		        },
		        options: {
		            responsive: true,
		            legend: {
		                position: "right",
		            },
		            maintainAspectRatio: false,
		            animation: {
		                animateScale: true,
		                animateRotate: true
		            }
		        }
		      });
		    </script>
		    ';

		    echo $result;
			}break;

			case 'get_dashboard_monthly_income_field':{

				$result = '';

				$result = '

					<div class="row">
            <div class="col-lg-3" style="height: 250px !important;">
							<div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_income_province" name="monthly_income_province" class="form-control" required onchange="get_dashboard_monthly_income_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_province_option($holu_provinces[0]).'
		              </select>
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_income_currency" name="monthly_income_currency" class="form-control" required onchange="get_dashboard_monthly_income_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_currency_option($holu_currencies[0]).'
		              </select>
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_income_year" name="monthly_income_year" class="form-control" required onchange="get_dashboard_monthly_income_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_year_options(date("Y")).'
		              </select>
		            </div>
		          </div>
		          
		        </div>
		        <div class="col-9" id="container_monthly_income_line">
			        <canvas id="canvas_monthly_income_line" ></canvas>
			      </div>
		      </div>
				';

				echo $result;
			}break;

			case 'get_dashboard_monthly_income_line': {

    $month_labels = [];
    $total_incomes = [];

    // ✅ Sanitize inputs
    $province = holu_escape($_POST['monthly_income_province'] ?? '');
    $currency = holu_escape($_POST['monthly_income_currency'] ?? '');

    $year = isset($_POST['monthly_income_year']) ? (int) $_POST['monthly_income_year'] : 0;
    if ($year < 1) {
        $year = (int) date('Y');
    }

    $months = [
        ['January', 1],
        ['February', 2],
        ['March', 3],
        ['April', 4],
        ['May', 5],
        ['June', 6],
        ['July', 7],
        ['August', 8],
        ['September', 9],
        ['October', 10],
        ['November', 11],
        ['December', 12],
    ];

    foreach ($months as $month) {

        $monthName = $month[0];
        $monthNumber = (int) $month[1];

        // ✅ Safe date calculation
        $days = cal_days_in_month(CAL_GREGORIAN, $monthNumber, $year);

        $start_date = sprintf('%04d-%02d-01', $year, $monthNumber);
        $end_date   = sprintf('%04d-%02d-%02d', $year, $monthNumber, $days);

        // ✅ Query
        $income_sq = $db->query("
            SELECT SUM(income_amount) AS total_income
            FROM incomes
            WHERE deleted = '0'
            AND province = '$province'
            AND currency = '$currency'
            AND income_date BETWEEN '$start_date' AND '$end_date'
            AND province IN ($accessed_provinces)
            AND sub_categories_id IN ($accessed_sub_categories_income)
        ");

        $total = 0;

        if ($income_sq && $income_sq->rowCount() > 0) {
            $row = $income_sq->fetch();
            $total = $row['total_income'] ?? 0;
        }

        $month_labels[] = $monthName;
        $total_incomes[] = (float) $total;
    }

    // ✅ Convert to JS format safely
    $month_labels_js = '"' . implode('","', $month_labels) . '"';
    $total_incomes_js = implode(',', $total_incomes);

    // ✅ Output
    echo '
    <script>
        $("#canvas_monthly_income_line").remove(); 
        $("#container_monthly_income_line").append("<canvas id=\'canvas_monthly_income_line\'></canvas>");

        var ctx = document.getElementById("canvas_monthly_income_line").getContext("2d");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: [' . $month_labels_js . '],
                datasets: [{
                    label: "Total Income",
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.blue,
                    borderWidth: 3,
                    fill: false,
                    data: [' . $total_incomes_js . ']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "top"
                    }
                }
            }
        });
    </script>';
}
break;

			case 'get_dashboard_monthly_expense_field':{

				$result = '';

				$result = '

					<div class="row">
            <div class="col-lg-3" style="height: 250px !important;">
							<div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_expense_province" name="monthly_expense_province" class="form-control" required onchange="get_dashboard_monthly_expense_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_province_option($holu_provinces[0]).'
		              </select>
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_expense_currency" name="monthly_expense_currency" class="form-control" required onchange="get_dashboard_monthly_expense_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_currency_option($holu_currencies[0]).'
		              </select>
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_expense_year" name="monthly_expense_year" class="form-control" required onchange="get_dashboard_monthly_expense_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_year_options(date("Y")).'
		              </select>
		            </div>
		          </div>
		          
		        </div>
		        <div class="col-9" id="container_monthly_expense_line">
			        <canvas id="canvas_monthly_expense_line" ></canvas>
			      </div>
		      </div>
				';

				echo $result;
			}break;

			case 'get_dashboard_monthly_expense_line': {

    $month_labels = [];
    $total_expenses = [];

    // ✅ Sanitize inputs
    $province = holu_escape($_POST['monthly_expense_province'] ?? '');
    $currency = holu_escape($_POST['monthly_expense_currency'] ?? '');

    $year = isset($_POST['monthly_expense_year']) ? (int) $_POST['monthly_expense_year'] : 0;
    if ($year < 1) {
        $year = (int) date('Y');
    }

    $months = [
        ['January', 1],
        ['February', 2],
        ['March', 3],
        ['April', 4],
        ['May', 5],
        ['June', 6],
        ['July', 7],
        ['August', 8],
        ['September', 9],
        ['October', 10],
        ['November', 11],
        ['December', 12],
    ];

    foreach ($months as $month) {

        $monthName = $month[0];
        $monthNumber = (int) $month[1];

        // ✅ Safe date handling
        $days = cal_days_in_month(CAL_GREGORIAN, $monthNumber, $year);

        $start_date = sprintf('%04d-%02d-01', $year, $monthNumber);
        $end_date   = sprintf('%04d-%02d-%02d', $year, $monthNumber, $days);

        // ✅ Query
        $expense_sq = $db->query("
            SELECT SUM(expense_amount) AS total_expense
            FROM expenses
            WHERE deleted = '0'
            AND province = '$province'
            AND currency = '$currency'
            AND expense_date BETWEEN '$start_date' AND '$end_date'
            AND province IN ($accessed_provinces)
            AND sub_categories_id IN ($accessed_sub_categories_expense)
        ");

        $total = 0;

        if ($expense_sq && $expense_sq->rowCount() > 0) {
            $row = $expense_sq->fetch();
            $total = $row['total_expense'] ?? 0;
        }

        $month_labels[] = $monthName;
        $total_expenses[] = (float) $total;
    }

    // ✅ Convert to JS
    $month_labels_js = '"' . implode('","', $month_labels) . '"';
    $total_expenses_js = implode(',', $total_expenses);

    // ✅ Output chart
    echo '
    <script>
        $("#canvas_monthly_expense_line").remove(); 
        $("#container_monthly_expense_line").append("<canvas id=\'canvas_monthly_expense_line\'></canvas>");

        var ctx = document.getElementById("canvas_monthly_expense_line").getContext("2d");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: [' . $month_labels_js . '],
                datasets: [{
                    label: "Total Expense",
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    borderWidth: 3,
                    fill: false,
                    data: [' . $total_expenses_js . ']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "top"
                    }
                }
            }
        });
    </script>';
}
break;

			case 'get_dashboard_monthly_purchase_field':{

				$result = '';

				$result = '

					<div class="row">
            <div class="col-lg-3" style="height: 250px !important;">
							<div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_purchase_province" name="monthly_purchase_province" class="form-control" required onchange="get_dashboard_monthly_purchase_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_province_option($holu_provinces[0]).'
		              </select>
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_purchase_currency" name="monthly_purchase_currency" class="form-control" required onchange="get_dashboard_monthly_purchase_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_currency_option($holu_currencies[0]).'
		              </select>
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-sm-12">
		              <select id="monthly_purchase_year" name="monthly_purchase_year" class="form-control" required onchange="get_dashboard_monthly_purchase_line();">
		                <option selected hidden value="">Select an option</option>
		                '.get_year_options(date("Y")).'
		              </select>
		            </div>
		          </div>
		          
		        </div>
		        <div class="col-9" id="container_monthly_purchase_line">
			        <canvas id="canvas_monthly_purchase_line" ></canvas>
			      </div>
		      </div>
				';

				echo $result;
			}break;

			case 'get_dashboard_monthly_purchase_line': {

    $month_labels = [];
    $total_purchases = [];

    // ✅ Sanitize inputs
    $province = holu_escape($_POST['monthly_purchase_province'] ?? '');
    $currency = holu_escape($_POST['monthly_purchase_currency'] ?? '');

    $year = isset($_POST['monthly_purchase_year']) ? (int) $_POST['monthly_purchase_year'] : 0;
    if ($year < 1) {
        $year = (int) date('Y');
    }

    $months = [
        ['January', 1],
        ['February', 2],
        ['March', 3],
        ['April', 4],
        ['May', 5],
        ['June', 6],
        ['July', 7],
        ['August', 8],
        ['September', 9],
        ['October', 10],
        ['November', 11],
        ['December', 12],
    ];

    foreach ($months as $month) {

        $monthName = $month[0];
        $monthNumber = (int) $month[1];

        // ✅ Safe date
        $days = cal_days_in_month(CAL_GREGORIAN, $monthNumber, $year);

        $start_date = sprintf('%04d-%02d-01', $year, $monthNumber);
        $end_date   = sprintf('%04d-%02d-%02d', $year, $monthNumber, $days);

        // ✅ Query
        $purchase_sq = $db->query("
            SELECT SUM(purchase_amount) AS total_purchase
            FROM purchases
            WHERE deleted = '0'
            AND province = '$province'
            AND currency = '$currency'
            AND purchase_date BETWEEN '$start_date' AND '$end_date'
            AND province IN ($accessed_provinces)
            AND sub_categories_id IN ($accessed_sub_categories_purchase)
        ");

        $total = 0;

        if ($purchase_sq && $purchase_sq->rowCount() > 0) {
            $row = $purchase_sq->fetch();
            $total = $row['total_purchase'] ?? 0;
        }

        $month_labels[] = $monthName;
        $total_purchases[] = (float) $total;
    }

    // ✅ Convert to JS
    $month_labels_js = '"' . implode('","', $month_labels) . '"';
    $total_purchases_js = implode(',', $total_purchases);

    // ✅ Output
    echo '
    <script>
        $("#canvas_monthly_purchase_line").remove(); 
        $("#container_monthly_purchase_line").append("<canvas id=\'canvas_monthly_purchase_line\'></canvas>");

        var ctx = document.getElementById("canvas_monthly_purchase_line").getContext("2d");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: [' . $month_labels_js . '],
                datasets: [{
                    label: "Total Purchase",
                    backgroundColor: window.chartColors.orange,
                    borderColor: window.chartColors.orange,
                    borderWidth: 3,
                    fill: false,
                    data: [' . $total_purchases_js . ']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "top"
                    }
                }
            }
        });
    </script>';
}
break;

			default:{

			}break;

		}
	}

?>