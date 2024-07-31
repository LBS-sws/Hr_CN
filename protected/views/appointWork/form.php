<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('appointWork/index'));
}
$this->pageTitle=Yii::app()->name . ' - Work Form';
?>

<style>
    *[readonly]{pointer-events: none;}
</style>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'work-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('fete','Overtime work Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('appointWork/index')));
		?>

        <?php if ($model->scenario!='view'&&$model->status!='3'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                'submit'=>Yii::app()->createUrl('appointWork/audit')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','Rejected'), array(
                'name'=>'btn88','id'=>'btn88','data-toggle'=>'modal','data-target'=>'#jectdialog'));
            ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('fete','Overtime record this month'), array(
                    'name'=>'btn99','id'=>'btn99','data-toggle'=>'modal','data-target'=>'#workList'));
                ?>
                <?php
                $counter = ($model->no_of_attm['workem'] > 0) ? ' <span id="docworkem" class="label label-info">'.$model->no_of_attm['workem'].'</span>' : ' <span id="docworkem"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadworkem',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
            <?php echo $form->hiddenField($model, 'employee_name'); ?>

            <?php
            $this->renderPartial('//site/workform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <?php if (Yii::app()->user->validFunction('ZR07')): ?>
            <legend>&nbsp;</legend>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'bool_cost',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'bool_cost',AppointWorkForm::getPayList(),
                            array('readonly'=>($model->getInputBool()),"id"=>"bool_cost")
                        ); ?>
                    </div>
                </div>
            <div id="bool_cost_div">
                <div class="form-group text-danger">
                    <label class="col-sm-2 control-label">
                        加班工资计算公式
                    </label>
                    <div class="form-control-static col-sm-10">
                        1、工作日加班费= 员工合同约定月工资÷(21.75×8)×150%×加班小时数<br>
                        2、周末加班费= 员工合同约定月工资÷(21.75×8)×200%×加班小时数<br>
                        3、法定节假日加班费= 员工合同约定月工资÷(21.75×8)×工资倍率×加班小时数
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="form-control-static col-sm-10">
                        <?php echo $model->wage;?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'work_cost',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'work_cost',
                            array('readonly'=>(true)));
                        ?>
                    </div>
                    <div class="form-control-static col-sm-7">
                        <?php
                        echo $model->wage."÷";
                        echo "(21.75×8)×".$model->getMuplite()."×".$model->log_time;
                        echo " = ".$model->work_cost;
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>


            <legend>&nbsp;</legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 'audit_remark',
                        array('readonly'=>($model->scenario=='view'),"rows"=>4)
                    ); ?>
                </div>
            </div>
            <?php
            $this->renderPartial('//site/ject',array(
                'form'=>$form,
                'model'=>$model,
                'rejectName'=>"reject_cause",
                'submit'=>Yii::app()->createUrl('appointWork/reject'),
            ));
            ?>
		</div>
	</div>
</section>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'WORKEM',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->getInputBool()),
));
?>
<?php
$this->renderPartial('//site/workList',array(
    'model'=>$model,
    'tableName'=>Yii::t("fete","Overtime work List"),
));
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
Script::genFileUpload($model,$form->id,'WORKEM');

$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('work/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

