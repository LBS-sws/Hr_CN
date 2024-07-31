<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('appointLeave/index'));
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
				'submit'=>Yii::app()->createUrl('appointLeave/index')));
		?>

        <?php if ($model->scenario!='view'&&$model->status!=3): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                'submit'=>Yii::app()->createUrl('appointLeave/audit')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','Rejected'), array(
                'name'=>'btn88','id'=>'btn88','data-toggle'=>'modal','data-target'=>'#jectdialog'));
            ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('fete','Leave record this month'), array(
                    'name'=>'btn99','id'=>'btn99','data-toggle'=>'modal','data-target'=>'#workList'));
                ?>
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
			<?php echo $form->hiddenField($model, 'only'); ?>
            <?php echo $form->hiddenField($model, 'employee_name'); ?>
            <?php echo $form->hiddenField($model, 'start_time'); ?>
            <?php echo $form->hiddenField($model, 'end_time'); ?>

            <?php
            $this->renderPartial('//site/leaveform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <?php if (Yii::app()->user->validFunction('ZR07')): ?>
            <legend>&nbsp;</legend>
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

            <legend>&nbsp;</legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 'audit_remark',
                        array('readonly'=>($model->scenario=='view'),"rows"=>4)
                    ); ?>
                </div>
            </div>
            <?php
            $this->renderPartial('//site/ject',array(
                'form'=>$form,
                'model'=>$model,
                'rejectName'=>"reject_cause",
                'submit'=>Yii::app()->createUrl('appointLeave/reject'),
            ));
            ?>
		</div>
	</div>
</section>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'LEAVE',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->getInputBool()),
));
?>
<?php
$this->renderPartial('//site/workList',array(
    'model'=>$model,
    'tableName'=>Yii::t("fete","Ask leave List"),
));
?>
<?php
$this->renderPartial('//site/removedialog');
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
";
Yii::app()->clientScript->registerScript('select2Function',$js,CClientScript::POS_READY);
$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('leave/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

