<?php

include("../lib/_configuration.php");

if(isset($_POST['flag_request'])){
  
  $flag_request = holu_escape($_POST['flag_request']);

  if($flag_request=="modal"){

    $modal = holu_escape($_POST['modal']);
    $data_id = holu_escape($_POST['data_id']);

    switch ($modal) {

      case "add_province_form":
?>
      <div class="modal-header">
        <h4 class="modal-title" id="add_provinceTitle"><i class="fa fa-plus"></i> Add Province</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" action="controller_province.php" method="POST">
          <input type="hidden" name="flag_request" value="operation"/>
          <input type="hidden" name="flag_operation" value="add_province"/>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="name">Province Name</label>
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

      case "edit_province_form":

        $province_sq = $db->query("SELECT * FROM `provinces` WHERE id='$data_id' LIMIT 1");

        if($province_sq->rowCount()>0){
          $province_row = $province_sq->fetch();
?>
          <div class="modal-header">
            <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Province</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" role="form" action="controller_province.php" method="POST">
              <input type="hidden" name="flag_request" value="operation"/>
              <input type="hidden" name="flag_operation" value="edit_province"/>
              <input type="hidden" name="data_id" value="<?php echo $data_id; ?>"/>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="name">Province Name</label>
                <div class="col-sm-6">
                  <input type="text" id="name" name="name" class="form-control" placeholder="Type here..." required value="<?php echo $province_row['name']; ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="abbreviation">Abbreviation</label>
                <div class="col-sm-6">
                  <input type="text" id="abbreviation" name="abbreviation" class="form-control" placeholder="Type here..." value="<?php echo htmlspecialchars($province_row['abbreviation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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

      case "delete_province_form":
?>
        <div class="modal-header">
          <h4 class="modal-title"><i class="fas fa-trash"></i> Delete Province</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" action="controller_province.php" method="POST">
            <input type="hidden" name="flag_request" value="operation"/>
            <input type="hidden" name="flag_operation" value="delete_province"/>
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

      case "add_province":

        $name = trim(holu_escape($_POST['name']));
        $abbreviation = trim(holu_escape($_POST['abbreviation'] ?? ''));
        if($name===''){
          header("location:list_province.php?error");
          exit();
        }

        $duplicate_sq = $db->prepare("SELECT id FROM `provinces` WHERE name=:name LIMIT 1");
        $duplicate_sqx = $duplicate_sq->execute([
          'name'=>$name
        ]);

        if($duplicate_sq && $duplicate_sq->rowCount()>0){
          header("location:list_province.php?duplicated");
          exit();
        }

        if(does_table_column_exist('provinces', 'abbreviation')){
          $province_iq = $db->prepare("INSERT INTO `provinces` (name, abbreviation) VALUES (:name, :abbreviation)");
          $province_iqx = $province_iq->execute([
            'name'=>$name,
            'abbreviation'=>$abbreviation
          ]);
        }else{
          $province_iq = $db->prepare("INSERT INTO `provinces` (name) VALUES (:name)");
          $province_iqx = $province_iq->execute([
            'name'=>$name
          ]);
        }

        if($province_iqx){
          header("location:list_province.php?success");
          exit();
        }else{
          header("location:list_province.php?error");
          exit();
        }

      break;

      case "edit_province":

        $data_id = holu_escape($_POST['data_id']);
        $name = trim(holu_escape($_POST['name']));
        $abbreviation = trim(holu_escape($_POST['abbreviation'] ?? ''));
        if($name===''){
          header("location:list_province.php?error");
          exit();
        }

        $duplicate_sq = $db->prepare("SELECT id FROM `provinces` WHERE name=:name AND id!=:id LIMIT 1");
        $duplicate_sqx = $duplicate_sq->execute([
          'name'=>$name,
          'id'=>$data_id
        ]);

        if($duplicate_sq && $duplicate_sq->rowCount()>0){
          header("location:list_province.php?duplicated");
          exit();
        }

        if(does_table_column_exist('provinces', 'abbreviation')){
          $province_uq = $db->prepare("UPDATE `provinces` SET name=:name, abbreviation=:abbreviation WHERE id=:id LIMIT 1");
          $province_uqx = $province_uq->execute([
            'name'=>$name,
            'abbreviation'=>$abbreviation,
            'id'=>$data_id
          ]);
        }else{
          $province_uq = $db->prepare("UPDATE `provinces` SET name=:name WHERE id=:id LIMIT 1");
          $province_uqx = $province_uq->execute([
            'name'=>$name,
            'id'=>$data_id
          ]);
        }

        if($province_uqx){
          header("location:list_province.php?success");
          exit();
        }else{
          header("location:list_province.php?error");
          exit();
        }

      break;

      case "delete_province":

        $data_id = holu_escape($_POST['data_id']);
        $province_name = get_col('provinces', 'name', 'id', $data_id);

        $references_sq = $db->query("SELECT
          (SELECT COUNT(id) FROM `incomes` WHERE province='$province_name') +
          (SELECT COUNT(id) FROM `expenses` WHERE province='$province_name') +
          (SELECT COUNT(id) FROM `exchanges` WHERE province='$province_name') +
          (SELECT COUNT(id) FROM `purchases` WHERE province='$province_name') +
          (SELECT COUNT(id) FROM `transfers` WHERE from_province='$province_name' OR to_province='$province_name') AS reference_count
        ");

        $references_row = $references_sq ? $references_sq->fetch() : ['reference_count' => 0];
        if((int)$references_row['reference_count']>0){
          header("location:list_province.php?in_use");
          exit();
        }

        $province_dq = $db->prepare("DELETE FROM `provinces` WHERE id=:id LIMIT 1");
        $province_dqx = $province_dq->execute([
          'id'=>$data_id
        ]);

        if($province_dqx){
          header("location:list_province.php?success");
          exit();
        }else{
          header("location:list_province.php?error");
          exit();
        }

      break;

      default:

    }
  }
}
