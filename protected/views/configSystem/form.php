<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('configSystem/index'));
}
$this->pageTitle=Yii::app()->name . ' - ConfigSystem Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'configSystem-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Config System'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('configSystem/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('configSystem/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

			<div class="form-group">
				<div class="col-sm-10 col-sm-offset-2">
                    <p class="form-control-static">1、yearLeaveType的值：（0：正常年假計算 1：新加坡。 2：吉隆坡。）</p>
                    <p class="form-control-static">2、personnelType的值：（0：正常加班請假 2：吉隆坡。）</p>
                    <p class="form-control-static">3、bossRewardType的值：（0：老總年度考核A、B、C三項。  1：忽略C部分的老總年度考核。）</p>
                    <p class="form-control-static">4、retirementAgeType的值：（0：正常退休年龄-男60 女50。 1：新加坡-62岁。 2：吉隆坡-60岁。）台湾没有退休年龄</p>
                    <p class="form-control-static">5、signedContractType的值：（0：需要合同寄出功能。 1：不需要合同寄出功能 -> 新增、變更員工後不會提示需要發送員工合同。）</p>
                    <p class="form-control-static">6、systemId的值：（0：大陸。 1：台灣。2：新加坡。 3：吉隆坡）</p>
				</div>
			</div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'set_city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'set_city',CGeneral::getCityList(),
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'set_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textField($model, 'set_name',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'set_value',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->numberField($model, 'set_value',
						array('readonly'=>($model->scenario=='view'))
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

$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('configSystem/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

