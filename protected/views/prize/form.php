<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('prize/index'));
}
$this->pageTitle=Yii::app()->name . ' - Prize Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'prize-form',
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
		<strong><?php echo Yii::t('fete','Pennants Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('prize/index')));
		?>

        <?php if ($model->scenario=='edit'): ?>
            <?php
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('prize/new'),
                ));
            ?>
        <?php endif; ?>
        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='new'||$model->status == 0||$model->status == 2): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('prize/save')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','For Audit'), array(
                    'submit'=>Yii::app()->createUrl('prize/audit')));
                ?>
            <?php endif ?>
        <?php endif; ?>
        <?php if ($model->scenario=='edit'&&$model->status == 0): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif; ?>
	</div>
            <?php if (Yii::app()->user->validFunction('ZR18')): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-backward"></span> '.Yii::t('contract','send back'), array(
                        'submit'=>Yii::app()->createUrl('prize/back')));
                    ?>
                </div>
            <?php endif ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <?php if ($model->status == 2): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'reject_remark',
                            array('readonly'=>true,'rows'=>4)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php
            $this->renderPartial('//site/prizeForm',array('model'=>$model,
                'form'=>$form,
            ));
            ?>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php

$js = "
    $('#city').on('change',function(){
        if($(this).val() != ''){
            $.ajax({
                type: 'post',
                url: '".Yii::app()->createUrl('prize/ajaxCity')."',
                data: {city:$(this).val()},
                dataType: 'json',
                success: function(data){
                    if(data.status == 1){
                        var staffList = data.staffList;
                        var customerList = data.customerList;
                        $('#staffNum').val(data.staffNum);
                        $('#staff').html('<option></option>');
                        $.each(staffList,function(i,n){
                            $('#staff').append('<option value=\"'+i+'\">'+n+'</option');
                        });

                    }
                }
            });
        }
    });
    
    $('#staff').on('change',function(){
        if($(this).val() != ''){
            $.ajax({
                type: 'post',
                url: '".Yii::app()->createUrl('assess/ajaxStaff')."',
                data: {staff:$(this).val()},
                dataType: 'json',
                success: function(data){
                    if(data.status == 1){
                        var staffList = data.staffList;
                        $('#city').val(staffList.city);
                        $('#work_type').val(staffList.work_type);
                    }
                }
            });
        }
    });
    $('#customer_name').on('change',function(){
        if($(this).val() != ''){
            $.ajax({
                type: 'post',
                url: '".Yii::app()->createUrl('prize/ajaxCustomer')."',
                data: {code:$(this).val()},
                dataType: 'json',
                success: function(data){
                    if(data.status == 1){
                        var customerList = data.customerList;
                        $('#cont_name').val(customerList.cont_name);
                        $('#cont_phone').val(customerList.cont_phone);
                    }
                }
            });
        }
    });
    //圖片上傳
    $('.file-update').upload({uploadUrl:'".Yii::app()->createUrl('prize/uploadImg')."'});
    
    $('body').delegate('.fileImgShow a','click',function(){
        $(this).parents('.form-group:first').find('input').val('');
        $(this).parents('.fileImgShow').parents('.form-group:first').find('input[type=\"file\"]').show();
        $(this).parents('.fileImgShow').remove();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'prize_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('prize/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);
?>

<?php $this->endWidget(); ?>

<?php
$this->renderPartial('//site/customerdialog',array(
    'model'=>$model,
));
?>
</div><!-- form -->

