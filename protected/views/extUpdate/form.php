<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('extUpdate/index'));
}
$this->pageTitle=Yii::app()->name . ' - ExtUpdate Form';
?>

<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'extUpdate-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','External Update'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('extUpdate/index')));
		?>
        <?php
            if(!$model->readonly()){
                echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('extUpdate/save')));
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                    'submit'=>Yii::app()->createUrl('extUpdate/audit')));
            }
            if($model->scenario=='edit'&&$model->staff_status==9){
                echo TbHtml::button('<span class="fa fa-remove"></span> ' . Yii::t('misc', 'Delete'), array(
                        'name' => 'btnDelete', 'id' => 'btnDelete', 'data-toggle' => 'modal', 'data-target' => '#removedialog',)
                );
            }
            if($model->scenario=='edit'&&$model->staff_status==1){
                echo TbHtml::button('<span class="fa fa-pencil"></span> ' . Yii::t('contract', 'Update'), array(
                    'submit'=>Yii::app()->createUrl('extUpdate/update')));
                echo TbHtml::button('<span class="fa fa-user-times"></span> ' . Yii::t('contract', 'Staff Departure'), array(
                    'submit'=>Yii::app()->createUrl('extUpdate/departure')));
            }
        ?>
	</div>

	<div class="btn-group pull-right" role="group">
        <?php echo TbHtml::button('<span class="fa fa-list"></span> '.Yii::t('contract','Flow Info'), array(
                'data-toggle'=>'modal','data-target'=>'#flowinfodialog',)
        );
        ?>
    <?php
        $counter = ($model->no_of_attm['employ'] > 0) ? ' <span id="docemploy" class="label label-info">'.$model->no_of_attm['employ'].'</span>' : ' <span id="docemploy"></span>';
        echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploademploy',)
        );
    ?>

	</div>
	</div></div>

	<div class="box box-info">
        <div class="box-body" style="position: relative">
            <?php if (!empty($model->image_user)): ?>
                <img src="<?php echo Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_user"));?>" width="150px" style="position: absolute;right: 5px;top: 5px;z-index: 2;">
            <?php endif; ?>

            <?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'staff_status'); ?>
			<?php echo $form->hiddenField($model, 'operation'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>

            <?php if ($model->staff_status == 3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'ject_remark',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
                <legend></legend>
            <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'update_remark',
                        array('rows'=>3,'readonly'=>($model->readonly()))
                    ); ?>
                </div>
            </div>
            <?php if ($model->operation=='departure'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'leave_time',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'leave_time',
                                array('class'=>'form-control pull-right','readonly'=>($model->readonly()),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'leave_reason',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-7">
                        <?php echo $form->textArea($model, 'leave_reason',
                            array('rows'=>3,'readonly'=>($model->readonly()))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <legend></legend>

            <div class="form-group">
                <?php echo $form->labelEx($model,'table_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'table_type',StaffFun::getTableTypeList(),
                        array('readonly'=>$model->readonly())
                    ); ?>
                </div>
                <div class="col-sm-7">
                    <ul class="list-unstyled text-danger">
                        <li><?php echo Yii::t("contract","external part one");?></li>
                        <li><?php echo Yii::t("contract","external part two");?></li>
                        <li><?php echo Yii::t("contract","external part three");?></li>
                        <li><?php echo Yii::t("contract","external part four");?></li>
                    </ul>
                </div>
            </div>
            <?php
            $this->renderPartial('//employView/employform',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>$model->readonly(),
            ));
            ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'social_code',
                        array('readonly'=>$model->readonly())
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'jj_card',
                        array('readonly'=>$model->readonly())
                    ); ?>
                </div>
            </div>

            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->readonly()))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/removedialog');
?>

<?php
$id = $model->id;
$model->id = $model->employee_id;
?>
<?php
$this->renderPartial('//external/historylist',array("model"=>$model));

$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'EMPLOY',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->readonly()),
));
?>
<?php
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/
Script::genFileUpload($model,$form->id,'EMPLOY');

$model->id = $id;

$js = "
var staffStatus = '".$model->staff_status."';
$('#ExtUpdateForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
    $('.file-update').upload({uploadUrl:'".Yii::app()->createUrl('employ/uploadImg')."'});
    
    $('body').delegate('.fileImgShow a','click',function(){
        $(this).parents('.form-group:first').find('input').val('');
        $(this).parents('.fileImgShow').parents('.form-group:first').find('input[type=\"file\"]').show();
        $(this).parents('.fileImgShow').remove();
    });
    
    //時間計算
    $('.test_add_time').on('change',function(){
        $.ajax({
            type: 'post',
            url: '".Yii::app()->createUrl('employ/addDate')."',
            data: {dateTime:$('.test_add_time').eq(1).val(),month:$('.test_add_time').eq(0).val()},
            dataType: 'json',
            success: function(data){
                $('.test_sum_time').val(data);
            }
        });
    }).trigger('change');
    
    $('#ExtUpdateForm_staff_id').on('change',function(){
        if($('#ExtUpdateForm_company_id').val() == ''){
            $('#ExtUpdateForm_company_id').val($(this).val());
        }
    });
    $('.changeButton').on('change',function(){
        $('#ExtUpdateForm_staff_type').val($(this).find('option:selected').data('dept'));
    });
    //合同期限變化
    $('.fixTime').on('change',function(){
        var netDom = $(this).parents('.form-group:first').next('.form-group');
        if($(this).val() == 'nofixed'){
            netDom.find('input').eq(1).val('').prop('readonly',true).addClass('readonly');
        }else{
            netDom.find('input').eq(1).prop('readonly',false).removeClass('readonly');
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'birth_time',
        'ExtUpdateForm_leave_time',
        'ExtUpdateForm_entry_time',
        'start_time',
        'end_time',
        'test_start_time',
        'ExtUpdateForm_user_card_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genDeleteData(Yii::app()->createUrl('extUpdate/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js?2", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>
</div><!-- form -->

