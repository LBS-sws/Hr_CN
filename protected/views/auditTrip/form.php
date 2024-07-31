<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('auditTrip/index'));
}
$this->pageTitle=Yii::app()->name . ' - auditTrip Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'auditTrip-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    *[readonly]{ pointer-events: none;}
    .input-group .input-group-addon{background: #eee;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','audit for trip'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('auditTrip/index')));
		?>
        <?php if ($model->scenario!='view' && $model->status == 1): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                'submit'=>Yii::app()->createUrl('auditTrip/audit')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','Rejected'), array(
                'name'=>'btn88','id'=>'btn88','data-toggle'=>'modal','data-target'=>'#jectdialog'));
            ?>
        <?php endif ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['trip'] > 0) ? ' <span id="doctrip" class="label label-info">'.$model->no_of_attm['trip'].'</span>' : ' <span id="doctrip"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadtrip',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>

            <?php if ($model->status==3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_cause',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-6">
                        <?php echo $form->textArea($model, 'reject_cause',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'employee_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'addTime',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-8">
                    <?php $this->widget('ext.layout.TableView2Widget', array(
                        'model'=>$model,
                        'attribute'=>'addTime',
                        'viewhdr'=>'//auditTrip/_formhdr',
                        'viewdtl'=>'//auditTrip/_formdtl',
                        'gridsize'=>'24',
                        'height'=>'200',
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'trip_address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textField($model, 'trip_address',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textField($model, 'company_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'addMoney',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-8">
                    <?php $this->widget('ext.layout.TableView2Widget', array(
                        'model'=>$model,
                        'tableClass'=>'table-bordered addMoney',
                        'attribute'=>'addMoney',
                        'viewhdr'=>'//auditTrip/_moneyFormhdr',
                        'viewdtl'=>'//auditTrip/_moneyFormdtl',
                        'gridsize'=>'24',
                        'height'=>'200',
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'trip_cost',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->numberField($model, 'trip_cost',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'trip_cause',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'trip_cause',
                        array('readonly'=>(true),'rows'=>4)
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'TRIP',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>(true),
));

$this->renderPartial('//site/ject',array(
    'form'=>$form,
    'model'=>$model,
    'rejectName'=>"reject_cause",
    'submit'=>Yii::app()->createUrl('auditTrip/reject',array("only"=>0)),
));
?>

<?php

Script::genFileUpload($model,$form->id,'TRIP');

$js = Script::genReadonlyField();
$js.="$('#tblDetail').addClass('table-bordered');";
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


