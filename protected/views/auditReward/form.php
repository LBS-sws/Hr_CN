<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('auditReward/index'));
}
$this->pageTitle=Yii::app()->name . ' - auditReward Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'auditReward-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .input-group .input-group-addon{background: #eee;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Reward Audit'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('auditReward/index')));
		?>
        <?php if ($model->scenario!='view' && $model->status == 1): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Audit'), array(
                'submit'=>Yii::app()->createUrl('auditReward/audit')));
            ?>
        <?php endif ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='view' && $model->status == 1): ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Rejected'), array(
                        'submit'=>Yii::app()->createUrl('auditReward/reject')));
                    ?>
                <?php endif ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'employee_code',
                        array('size'=>50,'readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'employee_name',
                        array('size'=>50,'readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'reward_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'reward_name',
                        array('size'=>50,'readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'reward_money',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'reward_money',
                        array('size'=>50,'readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'reward_goods',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'reward_goods',
                        array('rows'=>3,'readonly'=>true)
                    ); ?>
                </div>
            </div>


            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'reject_remark',
                        array('rows'=>3,'readonly'=>($model->status !=1 ||$model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


