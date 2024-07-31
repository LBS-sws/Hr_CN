<?php
$this->pageTitle=Yii::app()->name . ' - pinApply';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'pinApply-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
    .select2-container .select2-selection--single{ height: 34px;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Apply For Pin'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('pinApply/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('pinApply/save')));
            ?>
        <?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif; ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <?php if ($model->scenario!='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'pin_code',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'pin_code',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'apply_date',
                        array('readonly'=>($model->scenario=='view'),'autocomplete'=>'off','id'=>'apply_date','prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo PinApplyForm::getSelectForData($model, 'employee_id',PinApplyForm::getEmployeeList($model->employee_id),
                        array('readonly'=>($model->scenario=='view'),'class'=>"changeStaff")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'position',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'entry_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'entry_time',
                        array('readonly'=>(true),'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'class_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->dropDownList($model, 'class_id',PinClassForm::getPinClassList(),
                        array('disabled'=>($model->scenario=='view'),'class'=>"changePinClass",'id'=>'class_id')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo PinApplyForm::getSelectForData($model, 'name_id',PinApplyForm::getPinNameList(),
                        array('readonly'=>($model->scenario=='view'),'class'=>"changePinName")
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'pin_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->numberField($model, 'pin_num',
                        array('disabled'=>($model->scenario=='view'),'min'=>0)
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
switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = ($model->scenario=='view') ? 'true' : 'false';
$js = "
$('#employee_id').select2({
	multiple: false,
	maximumInputLength: 10,
	language: '$lang',
	disabled: $disabled
});
    $('#employee_id').change(function(){
        var entry = $('#employee_id option:selected').data('entry');
        var dept = $('#employee_id option:selected').data('dept');
        $('#PinApplyForm_position').val(dept);
        $('#PinApplyForm_entry_time').val(entry);
    });
    $('#name_id').change(function(){
        var class_id = $('#name_id option:selected').data('class_id');
        $('#class_id').val(class_id);
    });
    $('#class_id').change(function(){
        var class_id = $(this).val();
        if(class_id==''||class_id==undefined){
            $('#name_id option').show();
        }else{
            $('#name_id option').hide();
            $('#name_id option').each(function(){
                if($(this).data('class_id')==undefined||$(this).data('class_id')==class_id){
                    $(this).show();
                }
            });
            $('#name_id').val('');
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'apply_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('pinApply/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

