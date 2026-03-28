<?php

include("../lib/_configuration.php");

if (isset($_POST['flag_request'])) {

	$flag_request	= holu_escape($_POST['flag_request']);

	if ($flag_request == "modal") {

		$modal 		= holu_escape($_POST['modal']);
		$data_id 	= holu_escape(holu_decode($_POST['data_id']));
		$source 	= holu_escape($_POST['source']);

		switch ($modal) {
			case "add_user_form":

?>
		<div class="modal-header">
			<h4 class="modal-title" id="add_userTitle"><i class="fa fa-plus"></i> Add User</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>

		<div class="modal-body">
			<form class="form-horizontal" role="form" action="controller_user.php" method="POST">

				<input type="hidden" name="flag_request" id="flag_request" value="operation" />
				<input type="hidden" name="flag_operation" id="flag_operation" value="add_user" />

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="first_name">First Name</label>
					<div class="col-sm-6">
						<input type="text" 
							   id="first_name" 
							   name="first_name" 
							   class="form-control" 
							   required />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="last_name">Last Name</label>
					<div class="col-sm-6">
						<input type="text" 
							   id="last_name" 
							   name="last_name" 
							   class="form-control" 
							   required />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="username">Username</label>
					<div class="col-sm-6">
						<input type="text" 
								id="username" 
								name="username" 
								class="form-control" 
								required />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="email">Email</label>
					<div class="col-sm-6">
						<input type="email" 
								id="email" 
								name="email" 
								class="form-control" 
								required />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="password">Password</label>
					<div class="col-sm-6">
						<input type="password" i
								d="password" 
								name="password" 
								class="form-control" 
								required 
								pattern=".{8,}" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="confirm_password">
						Confirm Password
					</label>
					<div class="col-sm-6">
						<input type="password" 
							   id="confirm_password"
							   name="confirm_password" 
							   class="form-control" 
							   data-parsley-equalto="password" 
							   required 
							   pattern=".{8,}" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="submit"></label>
					<div class="col-sm-6">
						<button type="submit" 
								id="submit" 
								name="submit" 
								class="btn success_btn waves-effect waves-light mr-1">
								<i class="fa fa-save"></i> Register
						</button>
        				<button type="reset" 
        						class="btn btn-secondary waves-effect waves-light">
        						<i class="fa fa-eraser"></i> Reset
        				</button>
					</div>
				</div>

			</form>
		</div>
		<?php
		break;

			case "edit_user_form";
				$user_sq = $db->query("SELECT * FROM `users` WHERE deleted='0' AND id='$data_id' LIMIT 1");

				if ($user_sq->rowCount() > 0) {
					$user_row = $user_sq->fetch();

				?>

				<div class="modal-header">
					<h4 class="modal-title"><i class="fas fa-edit"></i> Edit User</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-horizontal parsley-form" role="form" action="controller_user.php" method="POST">

						<input type="hidden" name="flag_request" id="flag_request" value="operation" />

						<input type="hidden" name="flag_operation" id="flag_operation" value="edit_user" />

						<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>" />

						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="first_name">First Name</label>
							<div class="col-sm-6">
								<input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo $user_row['first_name']; ?>" required>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="last_name">Last Name</label>
							<div class="col-sm-6">
								<input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo $user_row['last_name']; ?>" required>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="username">Username</label>
							<div class="col-sm-6">
								<input type="text" id="username" name="username" class="form-control" value="<?php echo $user_row['username']; ?>" required>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="email">Email</label>
							<div class="col-sm-6">
								<input type="text" id="email" name="email" class="form-control" value="<?php echo $user_row['email']; ?>" required>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="password">New Password</label>
							<div class="col-sm-6">
								<input type="password" id="password" name="password" class="form-control" pattern=".{8,}">
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="confirm_password">Confirm New Password</label>
							<div class="col-sm-6">
								<input type="password" id="confirm_password" name="confirm_password" class="form-control" data-parsley-equalto="password" pattern=".{8,}">
							</div>
						</div>



						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="submit"></label>
							<div class="col-sm-6">
								<button type="submit" id="submit" name="submit" 
										class="btn success_btn waves-effect waves-light mr-1">
										<i class="fa fa-save"></i> Register</button>
            					<button type="reset" class="btn btn-secondary waves-effect waves-light">
            							<i class="fa fa-eraser"></i> Reset</button>
							</div>
						</div>

					</form>
				</div>
			<?php
			}
			break;

			case "delete_user_form":

				?>

				<div class="modal-header">
					<h4 class="modal-title"><i class="fas fa-trash"></i> Delete User</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-horizontal parsley-form" role="form" action="controller_user.php" method="POST">

						<input type="hidden" name="flag_request" id="flag_request" value="operation" />

						<input type="hidden" name="flag_operation" id="flag_operation" value="delete_user" />

						<input type="hidden" name="data_id" id="data_id" value="<?php echo holu_encode($data_id); ?>" />

						<div class="form-group row">
							<label class="col-sm-1 col-form-label" for="submit"></label>
							<div class="col-sm-11">
								<h3>This item will be permanently deleted. Do you continue?</h3>
							</div>
						</div>


						<div class="form-group row">
							<label class="col-sm-1 col-form-label" for="submit"></label>
							<div class="col-sm-10">
								<button type="submit" 
										id="submit" 
										name="submit" 
										class="btn btn-success waves-effect waves-light mr-1">
										<i class="fa fa-check-circle"></i> Yes
								</button>
		           				<button type="reset" 
		           						class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
		           						<i class="fa fa-times-circle"></i> No
		           				</button>
							</div>
						</div>

					</form>
				</div>
			<?php

			break;

			case "set_accessibility_form":

			$access_point_array = "";
			$accessibility_sq = $db->prepare("SELECT access_point FROM `accessibilities` WHERE
					 			system_users_id=:data_id 
					 			AND is_accessed='1' 
					 			AND access_point 
					 			LIKE 'system_accessibility/%' 
					 			AND deleted=0");


			$accessibility_sqx = $accessibility_sq->execute([
				'data_id' => $data_id
			]);

			if ($accessibility_sq->rowCount() > 0) {
				while ($accessibility_row = $accessibility_sq->fetch()) {
					$access_point_array .= '"' . $accessibility_row['access_point'] . '",';
				}
				$access_point_array = rtrim($access_point_array, ",");
			}

			$province_access_point_array = "";
			$province_accessibility_sq = $db->prepare("SELECT access_point FROM `accessibilities` WHERE 
										 system_users_id=:data_id 
										 AND is_accessed='1' 
										 AND access_point 
										 LIKE 'province_accessibility/%'");
			$province_accessibility_sqx = $province_accessibility_sq->execute([
				'data_id' => $data_id
			]);
			

			if ($province_accessibility_sq->rowCount() > 0) {
				while ($province_accessibility_row = $province_accessibility_sq->fetch()) {
					$province_access_point_array .= '"' . $province_accessibility_row['access_point'] . '",';
				}
				$province_access_point_array = rtrim($province_access_point_array, ",");
			}

			$sub_category_access_point_array = "";
			$sub_category_accessibility_sq = $db->prepare("SELECT access_point FROM `accessibilities` WHERE system_users_id=:data_id 
				AND is_accessed='1' 
				AND access_point 
				LIKE 'sub_category_accessibility/%'");
			$sub_category_accessibility_sqx = $sub_category_accessibility_sq->execute([
				'data_id' => $data_id
			]);

			if ($sub_category_accessibility_sq->rowCount() > 0) {
				while ($sub_category_accessibility_row = $sub_category_accessibility_sq->fetch()) {
					$sub_category_access_point_array .= '"' . $sub_category_accessibility_row['access_point'] . '",';
				}
				$sub_category_access_point_array = rtrim($sub_category_access_point_array, ",");
			}

			?>

			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fas fa-universal-access"></i> 
					Set Accessibility
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form class="form-horizontal parsley-form" 
					  role="form" 
					  action="controller_user.php" 
					  method="POST">

					<input type="hidden" 
							name="flag_request" 
							id="flag_request" 
							value="operation" />

					<input type="hidden" 
							name="flag_operation" 
							id="flag_operation" 
							value="set_accessibilty" />

					<input type="hidden" 
						 	name="data_id" 
							id="data_id" 
							value="<?php echo holu_encode($data_id); ?>" />

					<ul class="nav nav-pills navtab-bg nav-justified">
						<li class="nav-item">
							<a href="#system_accessibility" data-toggle="tab" aria-expanded="true" class="nav-link active">
								<span class="d-inline-block d-sm-none"><i class="fas fa-home"></i></span>
								<span class="d-none d-sm-inline-block">System Accessibility</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="#province_accessibility" data-toggle="tab" aria-expanded="false" class="nav-link">
								<span class="d-inline-block d-sm-none"><i class="far fa-user"></i></span>
								<span class="d-none d-sm-inline-block">Province Accessibility</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="#sub_category_accessibility" data-toggle="tab" aria-expanded="false" class="nav-link">
								<span class="d-inline-block d-sm-none"><i class="far fa-user"></i></span>
								<span class="d-none d-sm-inline-block">Sub Category Accessibility</span>
							</a>
						</li>
					</ul>

					<div class="tab-content">

						<div class="tab-pane fade show active" id="system_accessibility">
							<div class="item form-group">
								<label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="col-md-12 col-sm-12 col-xs-12" style="border: 2px dashed #00b8a5; padding-left: 20px; padding-top: 3%; padding-bottom: 3%; font-weight:bold;" id="accessibility_container">

									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade show" id="province_accessibility">
							<div class="item form-group">
								<label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="col-md-12 col-sm-12 col-xs-12" style="border: 2px dashed #00b8a5; padding-left: 20px; padding-top: 3%; padding-bottom: 3%; font-weight:bold;" id="province_accessibility_container">

									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade show" id="sub_category_accessibility">
							<div class="item form-group">
								<label class="control-label col-md-2 col-sm-2 col-xs-8"></label>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="col-md-12 col-sm-12 col-xs-12" style="border: 2px dashed #00b8a5; padding-left: 20px; padding-top: 3%; padding-bottom: 3%; font-weight:bold;" id="sub_category_accessibility_container">

									</div>
								</div>
							</div>
						</div>

					</div>
				</form>
			</div>

			<script type="text/javascript">
				var data_id = $("#data_id").val();
				var flag_request = $("#flag_request").val();
				var flag_operation = $("#flag_operation").val();

				var accessibility_access_points = <?php echo print_access_points($holu_portions); ?>;

				var customer_info_tree = new Tree('#accessibility_container', {
					data: [{
						id: 'system_accessibility',
						text: 'System Accessibility',
						children: accessibility_access_points
					}],
					closeDepth: 0,
					loaded: function() {
						this.values = [<?php echo $access_point_array; ?>];
					},
					onChange: function() {
						var access_points = this.values;
						jQuery.ajax({
							url: "controller_user.php",
							method: "POST",
							data: {
								data_id: data_id,
								flag_request: flag_request,
								flag_operation: flag_operation,
								access_points: access_points,
								access_path: "system_accessibility/"
							},
							beforeSend:function(){
								
							},
							success: function(data) {

							}
						});
					}
				});

				var accessibility_access_provinces = 
				<?php echo print_access_provinces($holu_provinces); ?>;

				var province_tree = new Tree('#province_accessibility_container', {
					data: [{
						id: 'province_accessibility',
						text: 'Province Accessibility',
						children: accessibility_access_provinces
					}],
					closeDepth: 0,
					loaded: function() {
						this.values = [<?php echo $province_access_point_array; ?>];
					},
					onChange: function() {
						var access_points = this.values;
						jQuery.ajax({
							url: "controller_user.php",
							method: "POST",
							data: {
								data_id: data_id,
								flag_request: flag_request,
								flag_operation: flag_operation,
								access_points: access_points,
								access_path: "province_accessibility/"
							},
							beforeSend:function(){
								
							},
							success: function(data) {

							}
						});
					}
				});

				var accessibility_access_sub_categories = <?php echo print_access_sub_categories(); ?>;

				var sub_category_tree = new Tree('#sub_category_accessibility_container', {
					data: [{
						id: 'sub_category_accessibility',
						text: 'Sub Category Accessibility',
						children: accessibility_access_sub_categories
					}],
					closeDepth: 0,
					loaded: function() {
						this.values = [<?php echo $sub_category_access_point_array; ?>];
					},
					onChange: function() {
						var access_points = this.values;
						$.ajax({
							url: "controller_user.php",
							method: "POST",
							data: {
								data_id: data_id,
								flag_request: flag_request,
								flag_operation: flag_operation,
								access_points: access_points,
								access_path: "sub_category_accessibility/"
							},
							success: function(data) {

							}
						});
					}
				});

			</script>
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
	} else if ($flag_request == "operation") {
		$flag_operation = holu_escape($_POST['flag_operation']);

		switch ($flag_operation) {

			case "add_user":

				$first_name = holu_escape($_POST['first_name']);
				$last_name = holu_escape($_POST['last_name']);
				$username = holu_escape($_POST['username']);
				$email = holu_escape($_POST['email']);
				$password = md5(holu_escape($_POST['password']));
				$confirm_password = md5(holu_escape($_POST['confirm_password']));

				if (
					!empty($first_name)
					and !empty($last_name)
					and !empty($username)
					and !empty($email)
					and !empty($password)
					and !empty($confirm_password)
					and $password == $confirm_password
				) {

					$user_sq = $db->prepare("SELECT * FROM `users` WHERE email=:email AND deleted='0'");
					$user_sqx = $user_sq->execute([
						'email' => $email
					]);

					if ($user_sq->rowCount() == 0) {
						$user_iq = $db->prepare(
							"INSERT INTO `users` (first_name, last_name, username, email, 
										password, insertion_date, insertion_time, users_id
			    			) VALUES (
			    				:first_name,
			    				:last_name,
			    				:username,
			    				:email,
			    				:password,
			    				:holu_date,
			    				:holu_time,
			    				:holu_users_id
			    			)"
						);

						$user_iqx = $user_iq->execute([
							'first_name' => $first_name,
							'last_name' => $last_name,
							'username' => $username,
							'email' => $email,
							'password' => $password,
							'holu_date' => $holu_date,
							'holu_time' => $holu_time,
							'holu_users_id' => $holu_users_id
						]);
					}
				}

				if ($user_iqx) {
					header("location:list_user.php?success");
					exit();
				} else {
					header("location:list_user.php?error");
					exit();
				}


				break;

			case "edit_user":

				$data_id = holu_escape(holu_decode($_POST['data_id']));
				$first_name = holu_escape($_POST['first_name']);
				$last_name = holu_escape($_POST['last_name']);
				$username = holu_escape($_POST['username']);
				$email = holu_escape($_POST['email']);

				$password = holu_escape($_POST['password']);
				$confirm_password = holu_escape($_POST['confirm_password']);

				if (!empty($password) and !empty($confirm_password) and $password == $confirm_password) {
					$edit_password_portion = ", password='" . md5($password) . "'";
				} else {
					$edit_password_portion = "";
				}

				if (
					!empty($first_name)
					and !empty($last_name)
					and !empty($username)
					and !empty($email)
				) {

					$user_sq = $db->prepare("SELECT * FROM `users` WHERE email=:email AND deleted='0'");
					$user_sqx = $user_sq->execute([
						'email' => $email
					]);

					if($user_sq->rowCount() == 0)
						$edit_email_portion = ", email='" . $email . "'";
					else
						$edit_email_portion = "";

					$user_uq = $db->prepare( 
						"UPDATE `users` SET 
					first_name=:first_name, 
					last_name=:last_name, 
					username=:username
					$edit_email_portion
					$edit_password_portion 
					WHERE id=:data_id LIMIT 1"
					);

					$user_uqx = $user_uq->execute([
						'first_name' => $first_name,
						'last_name' => $last_name,
						'username' => $username,
						'data_id' => $data_id
					]);

				}

				if ($user_uqx) {
					header("location:list_user.php?success");
					exit();
				} else {
					header("location:list_user.php?error");
					exit();
				}

				break;

			case "delete_user":

				$data_id = holu_escape(holu_decode($_POST['data_id']));

				$user_dq = $db->prepare("UPDATE `users` SET deleted='1' WHERE id=:data_id LIMIT 1");
				$user_dqx = $user_dq->execute([
					'data_id' => $data_id
				]);

				if ($user_dqx) {
					header("location:list_user.php?success");
					exit();
				} else {
					header("location:list_user.php?error");
					exit();
				}


				break;

			case "set_accessibilty":

				$data_id = holu_escape(holu_decode($_POST['data_id']));
				$access_path = holu_escape($_POST['access_path']);
				var_dump($data_id);
				$accessibility_uq = $db->prepare("UPDATE `accessibilities` SET is_accessed='0' WHERE system_users_id=:data_id AND access_point LIKE :access_path");

				$accessibility_sqx = $accessibility_uq->execute([
					'data_id' => $data_id,
					'access_path' => $access_path . '%'
				]);

				$flag = 1;
				if (isset($_POST['access_points'])) {
					$access_points = $_POST['access_points'];

					foreach ($access_points as $access_point) {
						$access_point = holu_escape($access_point);
						var_dump($access_point);
						$accessibility_sq = $db->prepare("SELECT id FROM `accessibilities` WHERE access_point=:access_point AND system_users_id=:data_id LIMIT 1");

						$accessibility_sqx = $accessibility_sq->execute([
							'access_point' => $access_point,
							'data_id' => $data_id
						]);

						if ($accessibility_sq->rowCount() > 0) {
							$accessibility_uq2 = $db->prepare("UPDATE `accessibilities` SET is_accessed='1' WHERE access_point=:access_point AND system_users_id=:data_id LIMIT 1");
							$accessibility_uqx2 = $accessibility_uq2->execute([
								'access_point' => $access_point,
								'data_id' => $data_id
							]);

							if (!$accessibility_uqx2) {
								$flag++;
							}
						} else {
							$accessibility_iq = $db->prepare("INSERT INTO `accessibilities` (access_point, is_accessed, system_users_id, insertion_date, insertion_time, users_id) VALUES (:access_point, '1', :data_id, :holu_date, :holu_time, :holu_users_id)");

							$accessibility_iqx = $accessibility_iq->execute([
								'access_point' => $access_point,
								'data_id' => $data_id,
								'holu_date' => $holu_date,
								'holu_time' => $holu_time,
								'holu_users_id' => $holu_users_id
							]);

							if (!$accessibility_iqx) {
								$flag++;
							}
						}
					}
				}

				$accessibility_uq3 = $db->prepare("UPDATE `accessibilities` SET is_accessed='1' WHERE system_users_id=:data_id AND access_point='system_accessibility/home/default/' LIMIT 1");
				$accessibility_uqx3 = $accessibility_uq3->execute([
					'data_id' => $data_id
				]);

				if ($flag == 1) {
					echo "1";
					exit();
				} else {
					echo "0";
					exit();
				}

				break;

			default: {
				}
			break;
		}
	}
}
?>
