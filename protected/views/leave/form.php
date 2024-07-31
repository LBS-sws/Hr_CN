<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('leave/index'));
}
$this->pageTitle=Yii::app()->name . ' - Leave Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'leave-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    *[readonly]{pointer-events: none;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('fete','Ask leave Form'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb"> 
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('leave/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='new'||$model->status == 0||$model->status == 3): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('leave/save')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','For Audit'), array(
                    'submit'=>Yii::app()->createUrl('leave/audit')));
                ?>
            <?php endif ?>
            <?php if ($model->scenario=='edit'&&($model->status == 0||$model->status == 3)): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
            <?php if (Yii::app()->user->validFunction('ZR05')&&$model->status == 4): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','cancel'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#jectdialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif; ?>
        <div class="pull-left text-red" style="padding-top: 7px;padding-left: 10px;">
            <span><?php echo Yii::t('contract','Note: Leave can be selected in stages'); ?></span>
            <a href="javascript:void (0);" data-toggle="modal" data-target="#helpdialog"><?php echo Yii::t('contract','（Click to see an example）'); ?></a>
        </div>
	</div>
            <?php if ($model->status==4): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','Download'), array(
                        'submit'=>Yii::app()->createUrl('leave/PdfDownload',array("index"=>$model->id))));
                    ?>
                </div>
            <?php endif; ?>
            <?php if (Yii::app()->user->validFunction('ZR13')&&$model->status==1): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('contract','send back'), array(
                        'submit'=>Yii::app()->createUrl('leave/back',array("index"=>$model->id))));
                    ?>
                </div>
            <?php endif; ?>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['leave'] > 0) ? ' <span id="docleave" class="label label-info">'.$model->no_of_attm['leave'].'</span>' : ' <span id="docleave"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadleave',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2 text-danger">
                    <?php echo Yii::t('contract','leave_title_1'); ?>
                    <br>
                    <?php echo Yii::t('contract','leave_title_2'); ?>
                </div>
            </div>
            <?php
            $this->renderPartial('//site/leaveform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
            ));
            ?>

            <?php if ($model->scenario != 'new' && in_array($model->status,array(1,2))): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'state',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textField($model, 'state',
                        array('readonly'=>(true),"rows"=>4)
                    ); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($model->status != 0 && $model->status != 3 && Yii::app()->user->validFunction('ZR07') && $model->scenario!='new'): ?>
                <legend>&nbsp;</legend>
                <?php if ($model->leave_cost == "0.00"): ?>
                    <div class="form-group text-danger">
                        <label class="col-sm-4 col-sm-offset-2 form-control-static">
                            不扣減工資
                        </label>
                    </div>
                <?php else:?>
                    <div class="form-group text-danger">
                        <label class="col-sm-2 control-label">
                            扣减工资计算公式
                        </label>
                        <div class="form-control-static col-sm-10">
                            扣减工资= 员工合同约定月工资÷出勤天数×請假天数×假期倍率<br>
                            出勤天数：22天（員工类型为办公室）、26天（员工类型不是办公室）
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="form-control-static col-sm-10">
                            <?php echo $model->wage;?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'leave_cost',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-3">
                            <?php echo $form->textField($model, 'leave_cost',
                                array('readonly'=>(true)));
                            ?>
                        </div>
                        <div class="form-control-static col-sm-7">
                            <?php
                            echo $model->wage."÷";
                            echo $model->getUserWorkDay()."×".$model->log_time."×".$model->getMuplite();
                            echo " = ".$model->leave_cost;
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($model->z_index != 3 && $model->z_index != 0): ?>
                <legend>&nbsp;</legend>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'audit_remark',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'LEAVE',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>(false),
    'delBtn'=>($model->scenario=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR05')),
));
//$model->getInputBool()
?>
<div id="fete_error" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title"><?php echo Yii::t("dialog","Validation Message");?></h4></div><div class="modal-body"><p></p>
                <div class="errorSummary">
                    <p><?php echo Yii::t("yii","Please fix the following input errors:");?></p>
                    <ul>
                    </ul>
                </div>
                <p></p>
            </div>
            <div class="modal-footer"><button data-dismiss="modal" class="btn btn-primary" name="yt4" type="button"><?php echo Yii::t("dialog","OK");?></button></div>
        </div>
    </div>
</div>
<?php
$this->renderPartial('//site/removedialog');
$this->renderPartial('//site/ject',array('form'=>$form,'model'=>$model,'rejectName'=>'reject_cause','header_name'=>Yii::t('dialog','Are you sure to cancel?'),'submit'=>Yii::app()->createUrl('leave/cancel')));
$this->renderPartial('//site/help',array('helpHtml'=>'<img width="100%" class="responsive-image" src="'.Yii::app()->baseUrl . "/images/pasted_2.png".'">'));
?>
<?php
Script::genFileUpload($model,$form->id,'LEAVE');
switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = (!$model->getInputBool()) ? 'false' : 'true';
$js = "
$('#work_id').select2({
    tags: false,
    multiple: true,
    maximumInputLength: 0,
    maximumSelectionLength: 10,
    allowClear: true,
    disabled: $disabled,
    language: '$lang',
    templateSelection: formatState
});
function formatState(state) {
	var rtn = $('<span style=\"color:black\">'+state.text+'</span>');
	return rtn;
}
var ajaxBool = true;
//顯示年假剩餘天數
$('#leave_type,.s_time,#employee_id').on('change',function(){
    if(ajaxBool){
        ajaxBool = false;
    }else{
        ajaxBool = true;
    }
    var work_id = $('#workDiv').data('id');
    work_id = $('#work_id').length>0?$('#work_id').val():work_id;
    var startTime='';
    $('.s_time').each(function(){
        if(startTime == ''||startTime<$(this).val()){
            startTime = $(this).val();
        }
    });
    $.ajax({
        type: 'post',
        url: '".Yii::app()->createUrl('leave/ajaxYearDay')."',
        data: {
            'index':$('#employee_id').val(),
            'time':startTime,
            'leave_type':$('#leave_type').val(),
            'work_id':work_id
        },
        dataType: 'json',
        success: function(data){
            ajaxBool = true;
            if(data.status == 1){
                var remark = data.remark;
                var html = data.html;
                var entry_time = data.entry_time;
                var workHtml = data.work_html;
                var parentDiv = $('#leave_type').parents('div.form-group:first');
                if(parentDiv.find('div.yearDay').length > 0){
                    parentDiv.find('div.yearDay').html(html);
                }else{
                    parentDiv.append('<div class=\"col-sm-7 yearDay\">'+html+'</div>');
                }
                $('#vacation_remark').val(remark);
                $('#workDiv').html(workHtml);
                $('#work_id').select2({
                    tags: false,
                    multiple: true,
                    maximumInputLength: 0,
                    maximumSelectionLength: 10,
                    allowClear: true,
                    disabled: $disabled,
                    language: '$lang',
                    templateSelection: formatState
                });
                if(entry_time!=''){//修改年假最大日期
                    $('#end_time').datepicker('setEndDate',entry_time);
                    if($('#end_time').val()!=''&&Date.parse($('#end_time').val())>Date.parse(entry_time)){
                        $('#end_time').val(entry_time);
                    }
                }
            }
        }
    });
});
";
if(!$model->getInputBool()){
$js.="$('#leave_type').trigger('change');";
}
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('leave/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

