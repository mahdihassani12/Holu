<?php

include("../lib/_configuration.php");

if(isset($_POST['flag_request'])){
  
  $flag_request = holu_escape($_POST['flag_request']);

  if($flag_request=="modal"){

    $modal = holu_escape($_POST['modal']);
    $data_id = holu_escape($_POST['data_id']);

    switch ($modal) {

      case "add_branch_form":

        $province_sq = $db->query("SELECT id, name FROM `provinces` ORDER BY name ASC");
?>
      <div class="modal-header">
        <h4 class="modal-title" id="add_branchTitle"><i class="fa fa-plus"></i> Add Branch</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" action="controller_branch.php" method="POST">
          <input type="hidden" name="flag_request" value="operation"/>
          <input type="hidden" name="flag_operation" value="add_branch"/>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="name">Branch Name</label>
            <div class="col-sm-6">
              <input type="text" id="name" name="name" class="form-control" placeholder="Type here..." required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="abbreviation">Abbreviation</label>
            <div class="col-sm-6">
              <input type="text" id="abbreviation" name="abbreviation" class="form-control" placeholder="Type here...">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="province_id">Province</label>
            <div class="col-sm-6">
              <select id="province_id" name="province_id" class="form-control" required>
                <option value="" selected disabled>Select province</option>
                <?php while($province_row = $province_sq->fetch()){ ?>
                  <option value="<?php echo $province_row['id']; ?>"><?php echo $province_row['name']; ?></option>
                <?php } ?>
              </select>
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

      case "edit_branch_form":

        $branch_sq = $db->query("SELECT * FROM `branches` WHERE id='$data_id' LIMIT 1");
        $province_sq = $db->query("SELECT id, name FROM `provinces` ORDER BY name ASC");

        if($branch_sq->rowCount()>0){
          $branch_row = $branch_sq->fetch();
?>
          <div class="modal-header">
            <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Branch</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" role="form" action="controller_branch.php" method="POST">
              <input type="hidden" name="flag_request" value="operation"/>
              <input type="hidden" name="flag_operation" value="edit_branch"/>
              <input type="hidden" name="data_id" value="<?php echo $data_id; ?>"/>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="name">Branch Name</label>
                <div class="col-sm-6">
                  <input type="text" id="name" name="name" class="form-control" placeholder="Type here..." required value="<?php echo $branch_row['name']; ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="abbreviation">Abbreviation</label>
                <div class="col-sm-6">
                  <input type="text" id="abbreviation" name="abbreviation" class="form-control" placeholder="Type here..." value="<?php echo htmlspecialchars($branch_row['abbreviation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="province_id">Province</label>
                <div class="col-sm-6">
                  <select id="province_id" name="province_id" class="form-control" required>
                    <?php while($province_row = $province_sq->fetch()){ ?>
                      <option value="<?php echo $province_row['id']; ?>" <?php echo ((int)$province_row['id']===(int)$branch_row['province_id'])?'selected':''; ?>><?php echo $province_row['name']; ?></option>
                    <?php } ?>
                  </select>
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

      case "delete_branch_form":
?>
        <div class="modal-header">
          <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Branch</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" action="controller_branch.php" method="POST">
            <input type="hidden" name="flag_request" value="operation"/>
            <input type="hidden" name="flag_operation" value="delete_branch"/>
            <input type="hidden" name="data_id" value="<?php echo $data_id; ?>"/>

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

      case "add_branch":

        $name = trim(holu_escape($_POST['name']));
        $abbreviation = trim(holu_escape($_POST['abbreviation'] ?? ''));
        $province_id = (int) holu_escape($_POST['province_id'] ?? 0);

        if($name==='' || $province_id<=0){
          header("location:list_branch.php?error");
          exit();
        }

        $province_sq = $db->prepare("SELECT id FROM `provinces` WHERE id=:id LIMIT 1");
        $province_sq->execute([
          'id'=>$province_id
        ]);

        if($province_sq->rowCount()===0){
          header("location:list_branch.php?error");
          exit();
        }

        $duplicate_sq = $db->prepare("SELECT id FROM `branches` WHERE name=:name AND province_id=:province_id LIMIT 1");
        $duplicate_sq->execute([
          'name'=>$name,
          'province_id'=>$province_id
        ]);

        if($duplicate_sq->rowCount()>0){
          header("location:list_branch.php?duplicated");
          exit();
        }

        if(does_table_column_exist('branches', 'users_id') && does_table_column_exist('branches', 'abbreviation')){
          $branch_iq = $db->prepare("INSERT INTO `branches` (name, province_id, abbreviation, users_id) VALUES (:name, :province_id, :abbreviation, :users_id)");
          $branch_iqx = $branch_iq->execute([
            'name'=>$name,
            'province_id'=>$province_id,
            'abbreviation'=>$abbreviation,
            'users_id'=>$holu_users_id
          ]);
        }else if(does_table_column_exist('branches', 'users_id')){
          $branch_iq = $db->prepare("INSERT INTO `branches` (name, province_id, users_id) VALUES (:name, :province_id, :users_id)");
          $branch_iqx = $branch_iq->execute([
            'name'=>$name,
            'province_id'=>$province_id,
            'users_id'=>$holu_users_id
          ]);
        }else if(does_table_column_exist('branches', 'abbreviation')){
          $branch_iq = $db->prepare("INSERT INTO `branches` (name, province_id, abbreviation) VALUES (:name, :province_id, :abbreviation)");
          $branch_iqx = $branch_iq->execute([
            'name'=>$name,
            'province_id'=>$province_id,
            'abbreviation'=>$abbreviation
          ]);
        }else{
          $branch_iq = $db->prepare("INSERT INTO `branches` (name, province_id) VALUES (:name, :province_id)");
          $branch_iqx = $branch_iq->execute([
            'name'=>$name,
            'province_id'=>$province_id
          ]);
        }

        if($branch_iqx){
          header("location:list_branch.php?success");
          exit();
        }else{
          header("location:list_branch.php?error");
          exit();
        }

      break;

      case "edit_branch":

        $data_id = (int) holu_escape($_POST['data_id']);
        $name = trim(holu_escape($_POST['name']));
        $abbreviation = trim(holu_escape($_POST['abbreviation'] ?? ''));
        $province_id = (int) holu_escape($_POST['province_id'] ?? 0);

        if($name==='' || $province_id<=0 || $data_id<=0){
          header("location:list_branch.php?error");
          exit();
        }

        $province_sq = $db->prepare("SELECT id FROM `provinces` WHERE id=:id LIMIT 1");
        $province_sq->execute([
          'id'=>$province_id
        ]);

        if($province_sq->rowCount()===0){
          header("location:list_branch.php?error");
          exit();
        }

        $duplicate_sq = $db->prepare("SELECT id FROM `branches` WHERE name=:name AND province_id=:province_id AND id!=:id LIMIT 1");
        $duplicate_sq->execute([
          'name'=>$name,
          'province_id'=>$province_id,
          'id'=>$data_id
        ]);

        if($duplicate_sq->rowCount()>0){
          header("location:list_branch.php?duplicated");
          exit();
        }

        if(does_table_column_exist('branches', 'abbreviation')){
          $branch_uq = $db->prepare("UPDATE `branches` SET name=:name, province_id=:province_id, abbreviation=:abbreviation WHERE id=:id LIMIT 1");
          $branch_uqx = $branch_uq->execute([
            'name'=>$name,
            'province_id'=>$province_id,
            'abbreviation'=>$abbreviation,
            'id'=>$data_id
          ]);
        }else{
          $branch_uq = $db->prepare("UPDATE `branches` SET name=:name, province_id=:province_id WHERE id=:id LIMIT 1");
          $branch_uqx = $branch_uq->execute([
            'name'=>$name,
            'province_id'=>$province_id,
            'id'=>$data_id
          ]);
        }

        if($branch_uqx){
          header("location:list_branch.php?success");
          exit();
        }else{
          header("location:list_branch.php?error");
          exit();
        }

      break;

      case "delete_branch":

        $data_id = (int) holu_escape($_POST['data_id']);

        if($data_id<=0){
          header("location:list_branch.php?error");
          exit();
        }

        $branch_dq = $db->prepare("DELETE FROM `branches` WHERE id=:id LIMIT 1");
        $branch_dqx = $branch_dq->execute([
          'id'=>$data_id
        ]);

        if($branch_dqx){
          header("location:list_branch.php?success");
          exit();
        }else{
          header("location:list_branch.php?error");
          exit();
        }

      break;

      default:

    }
  }
}
