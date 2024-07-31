<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('configOffice/index'));
}
$this->pageTitle=Yii::app()->name . ' - ConfigOffice Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'configOffice-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .input-group .input-group-addon{background: #eee;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Office Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('configOffice/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('configOffice/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('configOffice/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php
                    echo TbHtml::textField("city",CGeneral::getCityName($model->city),array("readonly"=>true));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->textField($model, 'name',
                        array('size'=>50,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'u_id',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->numberField($model, 'u_id',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'z_display',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <div class="input-group">
                        <?php echo $form->inlineRadioButtonList($model, 'z_display',array(Yii::t("contract","none"),Yii::t("contract","show")),
                            array('readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            </div>

            <?php if ($model->scenario!='new'): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'office_sum',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <div class="input-group">
                        <?php echo $form->textField($model, 'office_sum',
                            array('readonly'=>true)
                        ); ?>
                          <span class="input-group-btn">
                              <?php
                              echo TbHtml::button('&nbsp;<span class="fa fa-search"></span>&nbsp;',array(
                                  "data-toggle"=>"modal",
                                  "data-target"=>"#officeModel",
                              ));
                              ?>
                          </span>
                    </div>
                </div>
                <?php endif ?>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//configOffice/_office',array('id'=>$model->id,'name'=>$model->name));
?>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


