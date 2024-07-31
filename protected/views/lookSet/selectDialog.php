<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'selectDialog',
					'header'=>Yii::t('contract','Employee List'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>


<div class="form-group">
    <?php echo TbHtml::label(Yii::t("user","City"),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-4">
        <?php echo TbHtml::dropDownList("searchCity",'',CGeneral::getCityList(),array("id"=>"searchCity","style"=>"width:100%","empty"=>"")); ?>
    </div>
    <?php echo TbHtml::label(Yii::t("app","Employee"),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-4">
        <?php echo TbHtml::textField("searchName",'',array("id"=>"searchName")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label(Yii::t("contract","Position"),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-4">
        <?php echo TbHtml::textField("searchPosition",'',array("id"=>"searchPosition")); ?>
    </div>
    <div class="col-lg-6">
        <?php echo TbHtml::button("查询",array("id"=>"searchBtn")); ?>
    </div>
</div>

<div class="box" id="flow-list" style="max-height: 300px; overflow-y: auto;">
	<table id="staffTable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
                <th><?php echo Yii::t("contract","Employee Code"); ?></th>
                <th><?php echo Yii::t("contract","Employee Name"); ?></th>
                <th><?php echo Yii::t("contract","Employee City"); ?></th>
                <th><?php echo Yii::t("contract","Position"); ?></th>
                <th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<?php

$ajaxCardUrl = Yii::app()->createUrl('lookSet/searchEmployee');
$js="
//员工查询
$(\"#searchBtn\").click(function () {
   var searchCity = ''+$('#searchCity').val();
   var searchName = ''+$('#searchName').val();
   var searchPosition = ''+$('#searchPosition').val();
   var staff_id_str = ','+$('#staff_id_str').val()+',';
   var html = '<tr><td colspan=\"5\">正在查询......</td></tr>';
   
    $('#staffTable>tbody').html(html);
    
       $.ajax({
           type: 'post',
           url: '{$ajaxCardUrl}',
           data: {
               city: searchCity,
               name: searchName,
               position: searchPosition
           },
           dataType: 'json',
           success: function (data) {
                $('#staffTable>tbody').html('');
                if(data[\"status\"]==1){
                    $.each(data[\"data\"],function(key,item){
                        html = '<tr data-id=\"'+item['id']+'\">';
                        html+= '<td class=\"staff_code\">'+item['code']+'</td>';
                        html+= '<td class=\"staff_name\">'+item['name']+'</td>';
                        html+= '<td>'+item['city_name']+'</td>';
                        html+= '<td>'+item['position_name']+'</td>';
                        if(staff_id_str.indexOf(','+item['id']+',')>=0){
                            html+= '<td><a class=\"btn btn-default searchSub\">-</a></td>';
                        }else{
                            html+= '<td><a class=\"btn btn-primary searchAdd\">+</a></td>';
                        }
                        html+= '</tr>';
                        $('#staffTable>tbody').append(html);
                    });
                }
           }
       });
});

//增加员工
$('#staffTable>tbody').on('click','.searchAdd',function(){
   var staff_id_str = ''+$('#staff_id_str').val();
   var staff_name_str = ''+$('#staff_name_str').val();
   var id = ''+$(this).parents('tr:first').data('id');
   var code = ''+$(this).parents('tr:first').find('td.staff_code').text();
   var name = ''+$(this).parents('tr:first').find('td.staff_name').text();
   staff_id_str+= staff_id_str==''?'':',';
   staff_id_str+=id;
   $('#staff_id_str').val(staff_id_str);
   staff_name_str+=name+' ('+code+'); ';
   $('#staff_name_str').val(staff_name_str);
   $(this).removeClass('btn-primary searchAdd').addClass('btn-default searchSub').text('-');
});

//减少员工
$('#staffTable>tbody').on('click','.searchSub',function(){
    var staff_id_str = ''+$('#staff_id_str').val();
    var staff_name_str = ''+$('#staff_name_str').val();
    var id = ''+$(this).parents('tr:first').data('id');
    var code = ''+$(this).parents('tr:first').find('td.staff_code').text();
    var name = ''+$(this).parents('tr:first').find('td.staff_name').text();
    name =name+' ('+code+'); ';
    staff_id_str = staff_id_str.split(',');
    var key = staff_id_str.indexOf(id);
    if(key>=0){
        staff_id_str.splice(key,1);
    }
    staff_id_str = staff_id_str.join(',');
   $('#staff_id_str').val(staff_id_str);
    staff_name_str = staff_name_str.replace(name, '');
    $('#staff_name_str').val(staff_name_str);
    $(this).removeClass('btn-default searchSub').addClass('btn-primary searchAdd').text('+');
});
";
Yii::app()->clientScript->registerScript('searchEmployee',$js,CClientScript::POS_READY);

?>

<?php
	$this->endWidget();
?>
