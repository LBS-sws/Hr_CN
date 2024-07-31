<?php
$this->pageTitle=Yii::app()->name . ' - Boss Apply Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'bossSearch-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    .table-responsive th{white-space: nowrap;}
    .table-responsive>table{table-layout:fixed}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Boss Apply Form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('bossSearch/index')));
		?>
	</div>

                <div class="btn-group pull-right" role="group">
                    <?php if ($model->scenario!='new'){
                        // 下载
                        echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('dialog','Download'), array(
                            'submit'=>Yii::app()->createUrl('bossApply/downExcel',array("index"=>$model->id))));
                    } ?>
                    <?php if (Yii::app()->user->validFunction('ZR16')): ?>
                    <?php echo TbHtml::button('<span class="fa fa-backward"></span> '.Yii::t('contract','send back'), array(
                        'submit'=>Yii::app()->createUrl('bossSearch/back')));
                    ?>
                    <?php endif ?>
                    <?php if ($model->scenario!='new'){
                        //流程
                        echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                            'name'=>'btnBossFlow','id'=>'btnBossFlow','data-toggle'=>'modal','data-target'=>'#bossflowinfodialog'));
                    } ?>
                </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>

           <?php if ($model->status_type == 3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6 error">
                        <?php echo $form->textArea($model, 'reject_remark',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
               <legend>&nbsp;</legend>
           <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo TbHtml::label(Yii::t("contract","City"),'',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::textField('city',CGeneral::getCityName($model->city),
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group">
                        <?php echo $form->textField($model, 'audit_year',
                            array('readonly'=>(true))
                        ); ?>
                        <span class="input-group-addon"><?php echo Yii::t("contract"," year");?></span>
                    </div>
                </div>
            </div>
            <?php
            $bossApplyModel = new BossApplyForm();
            $tabs = $bossApplyModel->getTabList($model,true);
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/bossflow',array('model'=>$model));

$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

