<?php
$this->pageTitle=Yii::app()->name . ' - supportEmail Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'supportEmail-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    select[readonly="readonly"]{pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Email support employee'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('supportEmail/index')));
		?>

        <?php if (!$model->getReadonly()): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('supportEmail/save')));
            ?>
        <?php endif ?>
	</div>
            <?php if (!$model->getReadonly()&&!empty($model->id)): ?>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            </div>
            <?php endif; ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo CHtml::hiddenField('dtltemplate'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'employee_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'support_city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'support_city',CGeneral::getCityList(),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'wage_city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'wage_city',SupportEmailForm::getCityList(),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'start_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model,"start_date",
                        array('readonly'=>$model->getReadonly(),
                            'size'=>'10', 'maxlength'=>'10','class'=>'deadline',
                            'prepend'=>'<i class="fa fa-calendar"></i>',
                        )); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'end_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model,"end_date",
                        array('readonly'=>$model->getReadonly(),
                            'size'=>'10', 'maxlength'=>'10','class'=>'deadline',
                            'prepend'=>'<i class="fa fa-calendar"></i>',
                        )); ?>
                </div>
            </div>
		</div>



        <div class="box">
            <div class="box-body table-responsive">
                <legend><?php echo Yii::t('contract','history point'); ?></legend>
                <?php $this->widget('ext.layout.TableView2Widget', array(
                    'model'=>$model,
                    'attribute'=>'supportInfo',
                    'viewhdr'=>'//supportEmail/_formhdr',
                    'viewdtl'=>'//supportEmail/_formdtl',
                    'gridsize'=>'24',
                    'height'=>'200',
                ));
                ?>
            </div>
        </div>
    </div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php

$js = "
$('table').on('change','[id^=\"SupportEmailForm\"]',function() {
	var n=$(this).attr('id').split('_');
	$('#SupportEmailForm_'+n[1]+'_'+n[2]+'_uflag').val('Y');
});
";
Yii::app()->clientScript->registerScript('setFlag',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = "
$('table').on('click','#btnDelRow', function() {
	$(this).closest('tr').find('[id*=\"_uflag\"]').val('D');
	$(this).closest('tr').hide();
});
	";
    Yii::app()->clientScript->registerScript('removeRow',$js,CClientScript::POS_READY);

    $language = Yii::app()->language;
    $js = "
$(document).ready(function(){
	var ct = $('#tblDetail tr').eq(1).html();
	$('#dtltemplate').attr('value',ct);
	$('.deadline').datepicker({autoclose: true,language: '$language', format: 'yyyy-mm-dd'});
});

$('#btnAddRow').on('click',function() {
	var r = $('#tblDetail tr').length;
	if (r>0) {
		var nid = '';
		var ct = $('#dtltemplate').val();
		$('#tblDetail tbody:last').append('<tr>'+ct+'</tr>');
		$('#tblDetail tr').eq(-1).find('[id*=\"SupportEmailForm_\"]').each(function(index) {
			var id = $(this).attr('id');
			var name = $(this).attr('name');

			var oi = 0;
			var ni = r;
			id = id.replace('_'+oi.toString()+'_', '_'+ni.toString()+'_');
			$(this).attr('id',id);
			name = name.replace('['+oi.toString()+']', '['+ni.toString()+']');
			$(this).attr('name',name);
			if (id.indexOf('_id') != -1) $(this).attr('value','0');
			if (id.indexOf('_qty') != -1) $(this).attr('value','');
			if (id.indexOf('_date') != -1) {
				$(this).attr('value','');
				$(this).datepicker({autoclose: true,language: '$language', format: 'yyyy-mm-dd'});
			}
		});
		if (nid != '') {
			var topos = $('#'+nid).position().top;
			$('#tbl_detail').scrollTop(topos);
		}
	}
});
	";
    Yii::app()->clientScript->registerScript('addRow',$js,CClientScript::POS_READY);
}
$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('supportEmail/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

