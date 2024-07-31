<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('bossSetA/index'));
}
$this->pageTitle=Yii::app()->name . ' - bossSetA Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'bossSetA-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Setting Boss A'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('bossSetA/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('bossSetA/save')));
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
                <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'city',CGeneral::getCityList(),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'num_ratio',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php
                    echo $form->numberField($model, 'num_ratio',
                        array('readonly'=>($model->getReadonly()),'append'=>'%')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'tacitly',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->inlineRadioButtonList($model, 'tacitly',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <p class="form-control-static text-danger">1、没有设置的城市都会调用“默认”的配置，“默认”状态只有一个</p>
                    <p class="form-control-static text-danger">2、老总年度考核保存后（草稿之后的所有状态），表单内容不会随着本页面的配置而变化。如果需要变化，需要删除那个考核然后重新保存</p>
                </div>
            </div>

			<div class="form-group">
                <div class="col-sm-6 col-sm-offset-2">
					<?php echo $model->printTable(); ?>
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
$('.tr_show').change(function(){
    if($(this).val()==1){
        $(this).parents('tr:first').removeClass('danger');
    }else{
        $(this).parents('tr:first').addClass('danger');
    }
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('bossSetA/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

