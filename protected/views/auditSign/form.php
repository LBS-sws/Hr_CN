<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('auditSign/index'));
}
$this->pageTitle=Yii::app()->name . ' - auditSign';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'auditSign-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Audit sign contract'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('auditSign/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','signed'), array(
                'submit'=>Yii::app()->createUrl('auditSign/audit')));
            ?>
        <?php endif ?>
	</div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='view'): ?>
                    <?php
                    echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('contract','Rejected'), array(
                        'name'=>'btnJect','id'=>'btnJect','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                    ?>
                <?php endif ?>
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



            <?php
            $this->renderPartial('//site/signform',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>(true),
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
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tableModel = new OldContractForm();
                        echo $tableModel->printTableBody($model->employee_id,true,$model->his_id);
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/ject',
    array(
        'model'=>$model,
        'form'=>$form,
        'rejectName'=>"reject_remark",
        'submit'=>Yii::app()->createUrl('auditSign/reject')
    )
);
?>
<?php
$id = $model->id;
$model->id = $model->employee_id;
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'SIGNC',
    'header'=>Yii::t('contract','sign attachment'),
    'ronly'=>(true),
));
$model->id = $id;
?>
<?php
$model->id = $model->employee_id;
Script::genFileUpload($model,$form->id,'SIGNC');
$model->id = $id;


    $js = "
    ";
    Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

