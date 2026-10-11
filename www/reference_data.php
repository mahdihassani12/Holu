<?php
include('../lib/_configuration.php');
if(!ct_reference_can('view')){ http_response_code(403); exit('You do not have permission to view Reference Data.'); }
$holu_page_paths=array('Management','Reference Data');
$types=ct_reference_types();$type=$_GET['type']??'customer';if(!isset($types[$type]))$type='customer';$spec=$types[$type];
if(empty($_SESSION['ct_reference_csrf']))$_SESSION['ct_reference_csrf']=bin2hex(random_bytes(32));
$error='';$notice=isset($_GET['saved'])?'Reference Data saved.':'';$edit=null;$form_name='';$form_id='';
$rows=ct_reference_rows($type,false);
if(isset($_GET['edit']) && ct_reference_can('edit')){foreach($rows as $row){if((int)$row['id']===(int)$_GET['edit']){$edit=$row;break;}}}
if($edit){$form_name=$edit['name'];$form_id=$edit['party_id']??'';}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';$record_id=(int)($_POST['record_id']??0);$lock=false;
    $form_name=ct_reference_clean($_POST['name']??'');$form_id=ct_reference_clean($_POST['party_id']??'');
    try{
        if(!is_string($_POST['csrf']??null) || !hash_equals($_SESSION['ct_reference_csrf'],$_POST['csrf']))throw new RuntimeException('The form expired. Refresh and try again.');
        if(!in_array($action,array('save','status'),true))throw new RuntimeException('Invalid action.');
        if(!ct_reference_can($action==='status'?'status':($record_id?'edit':'add')))throw new RuntimeException('You do not have permission for this action.');
        $q=$db->query("SELECT GET_LOCK('cashtrack_reference_data',5)");$lock=(int)$q->fetchColumn()===1;if(!$lock)throw new RuntimeException('Another update is in progress. Please try again.');
        $rows=ct_reference_rows($type,false);$existing=null;foreach($rows as $row)if((int)$row['id']===$record_id)$existing=$row;
        if($record_id && !$existing)throw new RuntimeException('Reference entry not found.');
        if($action==='status' && !$record_id)throw new RuntimeException('Reference entry not found.');
        if($action==='save'){
            if($form_name==='' || ct_reference_length($form_name)>90)throw new RuntimeException('Enter a name of 1–90 characters.');
            if($spec[2]!==null && ($form_id==='' || ct_reference_length($form_id)>90))throw new RuntimeException('Enter an ID of 1–90 characters.');
            foreach($rows as $row){
                if((int)$row['id']===$record_id)continue;
                $duplicate=$spec[2]!==null?ct_reference_normal($row['party_id'])===ct_reference_normal($form_id):ct_reference_normal($row['name'])===ct_reference_normal($form_name);
                if($duplicate)throw new RuntimeException('This entry already exists. Edit or reactivate it instead.');
            }
        }
        $db->beginTransaction();
        if($action==='status'){
            $q=$db->prepare("UPDATE additional_informations SET deleted=:deleted WHERE reference_type='CashTrack Reference' AND reference_id=:id");
            if(!$q->execute(array('deleted'=>$existing['deleted']?0:1,'id'=>$record_id)))throw new RuntimeException('Could not update the status.');
        }elseif($record_id){
            $q=$db->prepare("UPDATE additional_informations SET value_info=:value WHERE reference_type='CashTrack Reference' AND reference_id=:id AND key_info=:key");
            if(!$q->execute(array('value'=>$form_name,'id'=>$record_id,'key'=>$spec[1])))throw new RuntimeException('Could not update the name.');
            if($spec[2]!==null && !$q->execute(array('value'=>$form_id,'id'=>$record_id,'key'=>$spec[2])))throw new RuntimeException('Could not update the ID.');
        }else{
            $q=$db->prepare("INSERT INTO additional_informations (insertion_date,insertion_time,users_id,deleted,reference_type,reference_id,key_info,value_info) VALUES (:date,:time,:user,0,'CashTrack Reference',:group_id,:key,:value)");
            $args=array('date'=>$holu_date,'time'=>$holu_time,'user'=>$holu_users_id,'group_id'=>0,'key'=>$spec[1],'value'=>$form_name);
            if(!$q->execute($args))throw new RuntimeException('Could not create the entry.');$record_id=(int)$db->lastInsertId();
            $group=$db->prepare("UPDATE additional_informations SET reference_id=:id WHERE id=:id2 AND reference_type='CashTrack Reference'");
            if(!$group->execute(array('id'=>$record_id,'id2'=>$record_id)))throw new RuntimeException('Could not create the entry.');
            if($spec[2]!==null){$args['group_id']=$record_id;$args['key']=$spec[2];$args['value']=$form_id;if(!$q->execute($args))throw new RuntimeException('Could not create the ID.');}
        }
        $db->commit();$db->query("SELECT RELEASE_LOCK('cashtrack_reference_data')");$lock=false;
        header('Location: reference_data.php?type='.urlencode($type).'&saved=1');exit;
    }catch(Throwable $exception){
        if($db->inTransaction())$db->rollBack();
        $error=$exception instanceof RuntimeException && !($exception instanceof PDOException)?$exception->getMessage():'Could not save the entry. Please try again.';
        if($record_id && isset($existing))$edit=$existing;
    }finally{if($lock)$db->query("SELECT RELEASE_LOCK('cashtrack_reference_data')");}
    $rows=ct_reference_rows($type,false);
}
$search=ct_reference_normal($_GET['search']??'');
?>
<!doctype html><html lang="en"><head><?php include('_head.php'); ?></head>
<body class="left-side-menu-dark"><div id="wrapper">
<div class="navbar-custom"><?php include('_navbar.php'); ?></div>
<div class="left-side-menu"><?php include('_sidebar.php'); ?></div>
<div class="content-page"><div class="content"><div class="container-fluid">
<?php include('_page_title.php'); ?>
<div class="card-box ct-reference-data">
<h4>Reference Data</h4><p class="text-muted">Save details here before using them in Input or Output. Names and IDs are matched without extra spaces or letter-case differences.</p>
<ul class="nav nav-pills mb-3"><?php foreach($types as $key=>$tab){ ?><li class="nav-item"><a class="nav-link <?php echo $key===$type?'active':''; ?>" href="reference_data.php?type=<?php echo ct_reference_escape($key); ?>"><?php echo ct_reference_escape($tab[0]); ?></a></li><?php } ?></ul>
<?php if($error){ ?><div class="alert alert-danger"><?php echo ct_reference_escape($error); ?></div><?php } ?>
<?php if($notice){ ?><div class="alert alert-success"><?php echo ct_reference_escape($notice); ?></div><?php } ?>
<?php if(($edit && ct_reference_can('edit')) || (!$edit && ct_reference_can('add'))){ ?>
<form method="post" action="reference_data.php?type=<?php echo ct_reference_escape($type); ?>" class="mb-4">
<input type="hidden" name="csrf" value="<?php echo ct_reference_escape($_SESSION['ct_reference_csrf']); ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="record_id" value="<?php echo $edit?(int)$edit['id']:0; ?>">
<div class="row"><div class="col-md-5"><label for="reference_name"><?php echo ct_reference_escape($spec[1]); ?></label><input id="reference_name" name="name" class="form-control" required maxlength="90" value="<?php echo ct_reference_escape($form_name); ?>"></div>
<?php if($spec[2]!==null){ ?><div class="col-md-4"><label for="reference_party_id"><?php echo ct_reference_escape($spec[2]); ?></label><input id="reference_party_id" name="party_id" class="form-control" required maxlength="90" value="<?php echo ct_reference_escape($form_id); ?>"></div><?php } ?>
<div class="col-md-3 pt-4"><button class="btn btn-primary" type="submit"><?php echo $edit?'Save changes':'Add entry'; ?></button> <?php if($edit){ ?><a class="btn btn-light" href="reference_data.php?type=<?php echo ct_reference_escape($type); ?>">Cancel</a><?php } ?></div></div></form><?php } ?>
<form method="get" class="mb-3"><input type="hidden" name="type" value="<?php echo ct_reference_escape($type); ?>"><div class="input-group"><input name="search" class="form-control" placeholder="Search saved details..." value="<?php echo ct_reference_escape($_GET['search']??''); ?>"><div class="input-group-append"><button class="btn btn-primary">Search</button></div></div></form>
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Name</th><?php if($spec[2]!==null){ ?><th>ID</th><?php } ?><th>Status</th><th>Operations</th></tr></thead><tbody>
<?php $shown=0;foreach($rows as $row){if($search!=='' && strpos(ct_reference_normal($row['name'].' '.($row['party_id']??'')),$search)===false)continue;$shown++; ?>
<tr><td><?php echo ct_reference_escape($row['name']); ?></td><?php if($spec[2]!==null){ ?><td><?php echo ct_reference_escape($row['party_id']); ?></td><?php } ?><td><?php echo $row['deleted']?'Inactive':'Active'; ?></td><td>
<?php if(ct_reference_can('edit')){ ?><a class="btn btn-sm btn-primary" href="reference_data.php?type=<?php echo ct_reference_escape($type); ?>&edit=<?php echo (int)$row['id']; ?>">Edit</a><?php } ?>
<?php if(ct_reference_can('status')){ ?><form method="post" class="d-inline" action="reference_data.php?type=<?php echo ct_reference_escape($type); ?>"><input type="hidden" name="csrf" value="<?php echo ct_reference_escape($_SESSION['ct_reference_csrf']); ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="record_id" value="<?php echo (int)$row['id']; ?>"><button class="btn btn-sm btn-secondary"><?php echo $row['deleted']?'Activate':'Deactivate'; ?></button></form><?php } ?></td></tr><?php } ?>
<?php if(!$shown){ ?><tr><td colspan="4">No saved details found.</td></tr><?php } ?></tbody></table></div>
</div></div></div><footer class="footer"><?php include('_footer.php'); ?></footer></div></div>
<?php include('_script.php'); ?></body></html>
