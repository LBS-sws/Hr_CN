<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('lookSet/index'));
}
$this->pageTitle=Yii::app()->name . ' - LookSet Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'lookSet-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .input-group .input-group-addon{background: #eee;}
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
    .select2-container .select2-selection--single{ height: 34px;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','look manage set'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('lookSet/index')));
		?>
<?php if ($model->scenario!='view'): ?>
            <?php
            if($model->scenario!='new'){
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('lookSet/new'),
                ));
            }
            ?>

			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('lookSet/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('lookSet/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'staff_id_str',array('id'=>'staff_id_str')); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'username',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'username',$model->getUserList($model->username),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'staff_name_str',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->textArea($model, 'staff_name_str',
                        array('readonly'=>(true),'rows'=>4,'id'=>'staff_name_str')
                    ); ?>
                </div>
                <div class="col-lg-4">
                    <?php echo TbHtml::button(Yii::t("contract","Select employee"),array('data-toggle'=>'modal','data-target'=>'#selectDialog')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-6 col-lg-offset-2">
                    <p class="text-danger form-control-static">允许额外查看配置员工的加班、请假、出差列表</p>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//lookSet/selectDialog',array('model'=>$model));
?>

<?php

switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = ($model->scenario!='view') ? 'false' : 'true';
$js="
$('#LookSetForm_username,#searchCity').select2({
    multiple: false,
    maximumInputLength: 10,
    language: '$lang',
    disabled: $disabled
});
function formatState(state) {
	var rtn = $('<span style=\"color:black\">'+state.text+'</span>');
	return rtn;
}
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


