<?php
$this->pageTitle=Yii::app()->name . ' - TreatyService Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'TreatyService-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('treaty','Treaty Hint'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('treatyService/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('treatyService/save')));
			?>
<?php endif ?>
<?php if ($model->scenario!='new' && $model->state_type==0 && $model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
<?php if ($model->state_type==2 && $model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-stop-circle-o"></span> '.Yii::t('treaty',"stop"), array(
			'data-toggle'=>'modal','data-target'=>'#stopDialog',)
		);
	?>
<?php endif ?>
	</div>
            <?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
            <div class="btn-group pull-right" role="group">
            <?php
            if ($model->scenario!='view') {
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('treaty','Add History'), array(
                    'submit'=>Yii::app()->createUrl('treatyInfo/new',array("treaty_id"=>$model->id)),'class'=>'pull-right'));
            }
            ?>
            </div>
            <?php endif ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'state_type'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'treaty_code',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php echo $form->textField($model, 'treaty_code',
					array('readonly'=>(true))
				); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'treaty_name',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->textField($model, 'treaty_name',
					array('readonly'=>($model->scenario=='view'),"autocomplete"=>"off")
				); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo TbHtml::textField("city_name",CGeneral::getCityName($model->city),
                        array('readonly'=>(true)));
                    ?>
                </div>
                <?php echo $form->labelEx($model,'lcu',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php
                    echo TbHtml::textField("lcu",$model->lcu,
                        array('readonly'=>(true),'append'=>TbHtml::button(Yii::t('treaty','treaty shift'),array('data-toggle'=>'modal','data-target'=>'#treatyDialog'))));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'apply_date',
                        array('readonly'=>(true),'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'start_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'start_date',
                        array('readonly'=>(true),'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'end_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'end_date',
                        array('readonly'=>(true),'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'state_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo TbHtml::textField("state_type",TreatyServiceList::getStateStr($model->state_type),
                        array('readonly'=>(true)));
                    ?>
                </div>
                <?php echo $form->labelEx($model,'treaty_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'treaty_num',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <?php if ($model->scenario!='new'): ?>
                <div class="box">
                    <div class="box-body table-responsive">
                        <legend>
                            <?php
                            echo Yii::t("treaty","treaty history")
                            ?>
                        </legend>
                        <?php
                        echo TreatyServiceForm::getHistoryTable($model->id,$model->scenario=='view')
                        ?>
                    </div>
                </div>
            <?php endif ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('shiftdialog',array("model"=>$model)); ?>
<?php $this->renderPartial('_stop'); ?>

<?php
$link = Yii::app()->createUrl('treatyService/stop');
$linkShift = Yii::app()->createUrl('treatyService/shift');
$js="
    $('#btnStopData').click(function(){
        jQuery.yii.submitForm(this,'$link',{});
    });
    $('#btnTreatyData').click(function(){
        jQuery.yii.submitForm(this,'$linkShift',{});
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('treatyService/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


