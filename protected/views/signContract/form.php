<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('signContract/index'));
}
$this->pageTitle=Yii::app()->name . ' - signContract';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'signContract-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','sign contract'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('signContract/index')));
		?>

        <?php if ($model->scenario!='view'&&in_array($model->status_type,array(-1,0,1,4))): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Draft'), array(
                'submit'=>Yii::app()->createUrl('signContract/draft')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','pack off'), array(
                'submit'=>Yii::app()->createUrl('signContract/save')));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'&&in_array($model->status_type,array(-1,0,1,3,4))&&Yii::app()->user->validFunction('ZR17')): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif; ?>
	</div>

            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['signc'] > 0) ? ' <span id="docsignc" class="label label-info">'.$model->no_of_attm['signc'].'</span>' : ' <span id="docsignc"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('contract','sign attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadsignc',)
                );
                ?>

            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'his_id'); ?>



            <?php if ($model->status_type == 4): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'reject_remark',
                            array('readonly'=>true,'rows'=>3)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php
            $this->renderPartial('//site/signform',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'||in_array($model->status_type,array(3,2))),
            ));
            ?>
            <legend><?php echo  Yii::t("contract","Staff View");?></legend>
            <div class="form-group">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th><?php echo Yii::t("contract","contract type");?></th>
                            <th><?php echo Yii::t("contract","Employee Code");?></th>
                            <th><?php echo Yii::t("contract","Employee Name");?></th>
                            <th><?php echo Yii::t("contract","ID Card");?></th>
                            <th><?php echo Yii::t("contract","Department");?></th>
                            <th><?php echo Yii::t("contract","Position");?></th>
                            <th><?php echo Yii::t("contract","Company Name");?></th>
                            <th><?php echo Yii::t("contract","contract deadline");?></th>
                            <th><?php echo Yii::t("contract","Contract Start Time");?></th>
                            <th><?php echo Yii::t("contract","Contract End Time");?></th>
                            <th width="1%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tableModel = new OldContractForm();
                        echo $tableModel->printTableBody($model->employee_id,true,$model->his_id,2);
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$id = $model->id;
$model->id = $model->employee_id;
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'SIGNC',
    'header'=>Yii::t('contract','sign attachment'),
    'ronly'=>($model->scenario=='view'||in_array($model->status_type,array(3,2))),
));
$model->id = $id;
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$model->id = $model->employee_id;
Script::genFileUpload($model,$form->id,'SIGNC');
$model->id = $id;

$js = Script::genDeleteData(Yii::app()->createUrl('signContract/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

    $js = "
    ";
    Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'send_date'
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

