<?php
// Reference records are isolated from transaction snapshots; no schema migration.
function ct_reference_types(){
    return array('customer'=>array('Customers','Customer Name','Customer ID'), 'vendor'=>array('Vendors','Vendor Name','Vendor ID'), 'employee'=>array('Employees','Employee Name','Employee ID'), 'package'=>array('Packages','Package',null), 'equipment'=>array('Equipment','Equipment',null), 'other_services'=>array('Other Services','Other Services',null));
}
function ct_reference_clean($value){
    return trim(preg_replace('/[\s\x{00a0}]+/u', ' ', html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8')));
}
function ct_reference_normal($value){
    $value=ct_reference_clean($value);
    return function_exists('mb_strtolower') ? mb_strtolower($value,'UTF-8') : strtolower($value);
}
function ct_reference_length($value){ return preg_match_all('/./us',(string)$value,$matches); }
function ct_reference_escape($value){ return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8'); }
function ct_reference_can($action){ return check_access('system_accessibility/management/reference_data/'.$action.'/'); }
function ct_reference_rows($type,$active_only=true){
    global $db;
    $types=ct_reference_types(); if(!isset($types[$type])) return array();
    $spec=$types[$type];
    $q=$db->prepare("SELECT r.id,r.value_info AS name,r.deleted,ident.value_info AS party_id FROM additional_informations r LEFT JOIN additional_informations ident ON ident.reference_type=r.reference_type AND ident.reference_id=r.id AND ident.key_info=:id_key WHERE r.reference_type='CashTrack Reference' AND r.key_info=:name_key".($active_only?" AND r.deleted=0 AND (ident.id IS NULL OR ident.deleted=0)":"")." ORDER BY r.value_info,r.id");
    $q->execute(array('name_key'=>$spec[1],'id_key'=>$spec[2]===null?'__no_id__':$spec[2]));
    return $q->fetchAll(PDO::FETCH_ASSOC);
}
function ct_reference_suggestions($input,$field_id,$suggestion_type){
    $input=ct_reference_normal($input); if(ct_reference_length($input)<1) return '';
    if(strpos((string)$suggestion_type,'additional_information_')!==0) return '';
    $key=substr($suggestion_type,strlen('additional_information_'));
    $is_id=substr($key,-3)==='_id';
    $type=preg_replace('/_(name|id)$/','',$key);
    $types=ct_reference_types(); if(!isset($types[$type])) return '';
    $spec=$types[$type]; $html='';$count=0;
    $rows=ct_reference_rows($type);
    $empty_message='No saved '.strtolower($spec[0]).' found. Please add the details in Management > Reference Data first, or ask an authorized user.';
    if(!$rows || ct_reference_length($input)<2){
        $message=$rows?'Type at least two characters to see saved suggestions.':$empty_message;
        return '<div class="holu_auto_suggest_container holu_auto_suggest_status" role="status" id="holu_auto_suggest_container">'.ct_reference_escape($message).'</div>';
    }
    foreach($rows as $row){
        if(strpos(ct_reference_normal($row['name']),$input)===false && strpos(ct_reference_normal($row['party_id']??''),$input)===false) continue;
        if($spec[2]!==null){
            $selected=$is_id?$row['party_id']:$row['name'];
            $onclick='select_party_data('.json_encode($field_id).','.json_encode($type.'_name').','.json_encode($type.'_id').','.json_encode($selected).','.json_encode($row['name']).','.json_encode($row['party_id']).');';
            $label=ct_reference_escape($row['name']).' <span class="suggestion-secondary">'.ct_reference_escape($row['party_id']).'</span>';
        }else{
            $onclick='select_data('.json_encode($field_id).','.json_encode($row['name']).');';$label=ct_reference_escape($row['name']);
        }
        $html.='<li onclick="'.ct_reference_escape($onclick).'">'.$label.'</li>';
        if(++$count>=5) break;
    }
    return '<div class="holu_auto_suggest_container" id="holu_auto_suggest_container">'.($html?'<ul>'.$html.'</ul>':'<div class="p-2" role="status">'.ct_reference_escape('No matching '.strtolower($spec[0]).' found. Please add the details in Management > Reference Data first, or ask an authorized user.').'</div>').'</div>';
}
function ct_reference_validate_transaction($reference_type,$operation){
    global $db;
    $fields=array('customer_name'=>'Customer Name','customer_id'=>'Customer ID','vendor_name'=>'Vendor Name','vendor_id'=>'Vendor ID','employee_name'=>'Employee Name','employee_id'=>'Employee ID','package'=>'Package','equipment'=>'Equipment','other_services'=>'Other Services');
    $submitted=array();$places=array();
    foreach($fields as $field=>$key){ if(isset($_POST[$field]) && is_scalar($_POST[$field]) && ct_reference_clean($_POST[$field])!==''){ $submitted[$key]=ct_reference_clean($_POST[$field]);$places[$key][]=array('field',$field); } }
    if(isset($_POST['key_infos']) && is_array($_POST['key_infos']) && isset($_POST['value_infos']) && is_array($_POST['value_infos'])){
        foreach($_POST['key_infos'] as $i=>$key){ if(in_array($key,$fields,true) && isset($_POST['value_infos'][$i]) && is_scalar($_POST['value_infos'][$i]) && ct_reference_clean($_POST['value_infos'][$i])!==''){
            if(isset($submitted[$key]) && ct_reference_normal($submitted[$key])!==ct_reference_normal($_POST['value_infos'][$i])) throw new RuntimeException('Conflicting '.$key.' values.');
            $submitted[$key]=ct_reference_clean($_POST['value_infos'][$i]);$places[$key][]=array('array',$i);
        } }
    }
    $old=array();
    if(strpos($operation,'edit_')===0){
        $id=(int)holu_decode($_POST['data_id']??'');
        $q=$db->prepare('SELECT key_info,value_info FROM additional_informations WHERE reference_type=:type AND reference_id=:id AND deleted=0');
        $q->execute(array('type'=>$reference_type,'id'=>$id));
        foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row) $old[$row['key_info']]=$row['value_info'];
    }
    foreach(ct_reference_types() as $type=>$spec){
        $keys=$spec[2]===null?array($spec[1]):array($spec[1],$spec[2]);$present=array_intersect($keys,array_keys($submitted)); if(!$present) continue;
        $unchanged=true;foreach($present as $key){ if(!isset($old[$key]) || ct_reference_normal($old[$key])!==ct_reference_normal($submitted[$key])) $unchanged=false; }
        if($unchanged) continue;
        $found=null;
        foreach(ct_reference_rows($type) as $row){
            $match=true;foreach($present as $key){ $value=$key===$spec[1]?$row['name']:$row['party_id'];if(ct_reference_normal($value)!==ct_reference_normal($submitted[$key])) $match=false; }
            if($match){$found=$row;break;}
        }
        if(!$found) throw new RuntimeException('Select a saved '.$spec[0].' entry. New details must be created in Management > Reference Data first.');
        foreach($present as $key){$canonical=$key===$spec[1]?$found['name']:$found['party_id'];foreach($places[$key] as $where){if($where[0]==='field') $_POST[$where[1]]=$canonical;else $_POST['value_infos'][$where[1]]=$canonical;}}
    }
}
