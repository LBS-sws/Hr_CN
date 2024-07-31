<?php
$this->pageTitle=Yii::app()->name . ' - auditHoliday Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'auditHoliday-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong>
            <?php
            echo $model->getTitleAppText();
            ?>
        </strong>
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
                    'submit'=>Yii::app()->createUrl('auditHoliday/index')));
                ?>

                <?php if ($model->scenario!='view' && $model->status == 1): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Audit'), array(
                        'submit'=>Yii::app()->createUrl('auditHoliday/audit')));
                    ?>
                <?php endif ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='view' && $model->status == 1): ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Rejected'), array(
                        'submit'=>Yii::app()->createUrl('auditHoliday/reject')));
                    ?>
                <?php endif ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'type'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo $form->hiddenField($model, 'status'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'employee_name',
                        array('readonly'=>(true)));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label required"><?php echo $model->getTypeName().Yii::t("contract"," Cause");?><span class="required">*</span></label>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'holiday_id',$model->getHolidayAllList(),
                        array('disabled'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label required"><?php echo $model->getTypeName().Yii::t("contract","Time");?><span class="required">*</span></label>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'start_time',
                        array('readonly'=>(true)));
                    ?>
                </div>
                <div class="pull-left">
                    <p class="form-control-static">-</p>
                </div>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'end_time',
                        array('readonly'=>(true)));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 'reject_remark',
                        array('rows'=>3,'readonly'=>($model->status != 1))
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

</div><!-- form -->

