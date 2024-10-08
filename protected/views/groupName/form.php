<?php
$this->pageTitle=Yii::app()->name . ' - GroupName Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'GroupName-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('group','Group Name Form'); ?></strong>
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
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('groupName/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('groupName/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('groupName/save')));
			?>
<?php endif ?>
<?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'group_code',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->textField($model, 'group_code',
					array('size'=>50,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'group_name',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->textField($model, 'group_name',
					array('size'=>50,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'group_remark',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->textArea($model, 'group_remark',
					array('rows'=>4,'readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-10 col-lg-offset-2 text-danger">
                    <ul class="list-unstyled">
                        <li>KARPT：日报表系统 → KA签单报表</li>
                        <li>KALIST：销售系统 → KA业务管理列表</li>
                    </ul>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('groupName/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


