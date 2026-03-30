
//modal loader function
function load_modal(source, destination, modal, modal_id, data_id){
	
	$.ajax({
		url:destination,
		method:'post',
		data:{
			data_id:data_id, 
			modal:modal, 
			source:source, 
			flag_request:'modal'
		},
		success:function(result){
			$("#"+modal_id).modal();
			$("#"+modal_id+" .modal-content").html(result);
			reload_js();
		}
	});

}

function specify_rate(){
	var currency = $("#currency").val();
	if(currency=="AFN"){
		$("#currency_rate").val(1);
		$("#currency_rate").attr("readonly", "on");
	}else{
		$("#currency_rate").val(0);
		$("#currency_rate").removeAttr("readonly");
	}
}
function disable_form_submit_btn() {
  $('form').on('submit', function() {
    $(this).find('button[type=submit]').prop('disabled', true);
  });
}
function reload_js(){
	$(".date_picker").attr("data-provide","datepicker");
	$(".date_picker").attr("data-date-autoclose","true");
	$(".date_picker").attr("autocomplete","off");
  $(".date_picker").datepicker({
    "orientation": "bottom"
  });


  $('.select2').select2();
  $('form').each(function(){
    $(this).find('select[name="branch"]').slice(1).each(function(){
      $(this).closest('.form-group').remove();
    });
  });
  disable_form_submit_btn();
}

reload_js();

function get_random_color() {
  var letters = '0123456789ABCDEF'.split('');
  var color = '#';
  for (var i = 0; i < 6; i++ ) {
      color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

function convert_chart_to_pdf(canvas_id){
	var canvas_id = canvas_id;
	// get size of report page
  var reportPageHeight = $('#'+canvas_id).innerHeight();
  var reportPageWidth = $('#'+canvas_id).innerWidth();

  // create a new canvas object that we will populate with all other canvas objects
  var pdfCanvas = $('<canvas />').attr({
    id: "canvaspdf",
    width: reportPageWidth,
    height: reportPageHeight
  });

  // keep track canvas position
  var pdfctx = $(pdfCanvas)[0].getContext('2d');
  var pdfctxX = 0;
  var pdfctxY = 0;
  var buffer = 0;

  // for each chart.js chart
  $("#"+canvas_id).each(function(index) {
    // get the chart height/width
    var canvasHeight = $('#'+canvas_id).innerHeight();
    var canvasWidth = $('#'+canvas_id).innerWidth();

    // draw the chart into the new canvas
    pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
    pdfctxX += canvasWidth + buffer;

    // our report page is in a grid pattern so replicate that in the new canvas
    if (index % 2 === 1) {
      pdfctxX = 0;
      pdfctxY += canvasHeight + buffer;
    }
  });

  // create new pdf and add our new canvas as an image
  var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
  pdf.addImage($(pdfCanvas)[0], 'PNG', 0, 0);

  // download the pdf
  pdf.save('Ariyabod Holu.pdf');
}

function get_category_option(categories_id, category_type, target_id){
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'get_category_option',
      categories_id:categories_id,
      category_type:category_type
    },
    success:function(result){
      $("#"+target_id).html(result);
    }
  });
}

function get_sub_category_option(sub_categories_id, categories_id, target_id){
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'get_sub_category_option',
      sub_categories_id:sub_categories_id, 
      categories_id:categories_id
    },
    success:function(result){
      $("#"+target_id).html(result);
    }
  });
}

function get_branch_option(province, branch, target_id){
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'get_branch_option',
      province:province,
      branch:branch
    },
    success:function(result){
      $("#"+target_id).html(result);
    }
  });
}
window.get_branch_option = get_branch_option;

$(document).on('change', '[data-branch-target]', function(){
  var targetId = $(this).data('branch-target');
  var branchValue = $(this).data('branch-value') || '0';
  if($(this).data('branch-loaded')===1){
    branchValue = '0';
  }
  $(this).data('branch-loaded', 1);
  get_branch_option($(this).val(), branchValue, targetId);
});


$('.tip').each(function () {
  $(this).tooltip(
  {
    html: true,
    title: $('#' + $(this).data('tip')).html()
  });
});

function markup_item(rtap, reference_type, reference_id, markup_type){

  

  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'markup_item',
      rtap:rtap, 
      reference_type:reference_type, 
      reference_id:reference_id,
      markup_type:markup_type
    },
    beforeSend: function() {
      $("#markups"+reference_type+reference_id).html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span><br/><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span><br/><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span><br/><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span><br/>');
    },
    success:function(result){
      $("#markups"+reference_type+reference_id).html(result);
    }
  });
}



function edit_check_number(expenses_id, operation_type){
  
  if(operation_type=="edit"){
    $.ajax({
      url:'controller_ajax.php',
      method:'post',
      data:{
        operation:'edit_check_number',
        operation_type:operation_type,
        expenses_id:expenses_id
      },
      success:function(result){
        $("#check_number_containerExpense"+expenses_id).html(result);
      }
    });
  }else if(operation_type=="save"){
    var check_number = $("#check_number"+expenses_id).val();
    $.ajax({
      url:'controller_ajax.php',
      method:'post',
      data:{
        operation:'edit_check_number',
        operation_type:operation_type,
        check_number:check_number,
        expenses_id:expenses_id
      },
      success:function(result){
        $("#check_number_containerExpense"+expenses_id).html(result);
      }
    });
  }
}

function add_row(selector, counter, value){
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'add_row',
      selector:selector, 
      counter:counter,
      value:value
    },
    beforeSend:function(){
      $('#'+selector+'_adder_button').removeAttr('onclick');
    },
    success:function(result){
      $('#'+selector+'_input_containers').append(result);
      $('#'+selector+'_remover_button_'+(counter-1)).hide();
      $('#'+selector+'_adder_button').attr('onclick', 'add_row(\''+selector+'\', '+(counter+1)+', \'\')');
    }
  });
  
}

function remove_row(selector, counter){
  $('#'+selector+'_container_'+counter).remove();
  $('#holu_auto_suggest_container_'+counter).remove();
  $('#'+selector+'_remover_button_'+(counter-1)).show();
  $('#'+selector+'_adder_button').attr('onclick', 'add_row(\''+selector+'\', '+(counter)+', \'\')');
}

function configure_input_field(selector, counter){
  var key_info = $("#key_"+selector+"_"+counter).val();
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'configure_input_field',
      selector:selector,
      counter:counter,
      key_info:key_info
    },
    success:function(result){
      $("#value_"+selector+"_"+counter).replaceWith(result);
      reload_js();
      
    }
  });
}

function get_income_for_accounting(){
  var province = $("#province").val();
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'get_income_for_accouting',
      province:province
    },
    success:function(result){
      $("#report_accounting_tbody").html(result);
    }
  });
}

function suggest_data(input_field, type){
  var input = $(input_field).val();
  var field_id = $(input_field).attr('id');
  $("#holu_auto_suggest_container").remove();
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'suggest_data',
      input:input,
      field_id:field_id,
      type:type
    },
    beforeSend: function(){
      
    },
    success:function(result){
      $(input_field).after(result);
      $(document).mouseup(function(e){
        var container = $(".holu_auto_suggest_container");
        if (!container.is(e.target) && container.has(e.target).length === 0){
          $("#holu_auto_suggest_container").remove();
        }
      });
    }
    
  });
}

function select_data(field_id, data){
  $("#"+field_id).val(data);
  $("#holu_auto_suggest_container").remove();
}

function get_sub_cat_conf(){

  var sub_categories_id = $("#sub_categories_id").val();
  $.ajax({
    url:'controller_ajax.php',
    method:'post',
    data:{
      operation:'get_sub_cat_conf',
      sub_categories_id:sub_categories_id
    },
    success:function(result){
      $("#additional_information_input_containers").html(result);
      reload_js();
    }
  });
}

//End of functions
