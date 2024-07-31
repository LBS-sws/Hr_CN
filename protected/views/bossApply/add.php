<?php
$this->pageTitle=Yii::app()->name . ' - Boss Apply Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'bossApply-form',
'method'=>'get',
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
		<strong><?php echo Yii::t('contract','Boss Apply Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('bossApply/index')));
		?>
        <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Submit'), array(
            'submit'=>Yii::app()->createUrl('bossApply/new')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $model->getBossApplyYearHtml(); ?>
                </div>
            </div>
		</div>
	</div>
</section>
<?php

$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

