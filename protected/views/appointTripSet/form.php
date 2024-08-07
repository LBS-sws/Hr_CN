<?php
$this->pageTitle=Yii::app()->name . ' - Visit Type Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'appointTripSet-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
    .select2-container .select2-selection--single{ height: 34px;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','appoint form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('appointTripSet/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('appointTripSet/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('appointTripSet/save'))); 
			?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo CHtml::hiddenField('dtltemplate'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo TbHtml::hiddenField("searchType","trip"); ?>

            <?php if ($model->scenario!='new'): ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'appoint_code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'appoint_code',
						array('readonly'=>(true))
					); ?>
				</div>
			</div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php
                    echo $form->textField($model, 'employee_name',
                        array('size'=>60,'maxlength'=>1000,'readonly'=>true,
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('contract','Employee'),array('name'=>'btnEmployee','id'=>'btnEmployee','disabled'=>($model->scenario!='new'))),
                        ));
                    ?>
                    <?php echo $form->hiddenField($model, 'employee_id'); ?>
                </div>
            </div>


            <div class="box">
                <div class="box-body table-responsive">
                    <div class="col-lg-offset-2 col-lg-6">
                        <?php
                        $this->widget('ext.layout.TableView2Widget', array(
                            'model'=>$model,
                            'attribute'=>'detail',
                            'viewhdr'=>'//appointTripSet/_formhdr',
                            'viewdtl'=>'//appointTripSet/_formdtl',
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <p class="text-danger">1、层级的数值越高，审核顺序越靠后</p>
                <p class="text-danger">2、最多添加五个审核人</p>
                <p class="text-danger">3、审核人必须拥有 “审核指定出差” 这个权限</p>
            </div>
        </div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('//site/lookup'); ?>

<?php
switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = ($model->scenario!='view') ? 'false' : 'true';
$js="
$('.searchAuditUser').select2({
    multiple: false,
    maximumInputLength: 10,
    language: '$lang',
    disabled: $disabled
});
function formatState(state) {
	var rtn = $('<span style=\"color:black\">'+state.text+'</span>');
	return rtn;
}
";
Yii::app()->clientScript->registerScript('searchUserFunction',$js,CClientScript::POS_READY);

$js = "
$('table').on('change','[id^=\"AppointTripSetForm\"]',function() {
	var n=$(this).attr('id').split('_');
	$('#AppointTripSetForm_'+n[1]+'_'+n[2]+'_uflag').val('Y');
});
";
Yii::app()->clientScript->registerScript('setFlag',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = <<<EOF
$('table').on('click','#btnDelRow', function() {
	$(this).closest('tr').find('[id*=\"_uflag\"]').val('D');
	$(this).closest('tr').hide();
});
EOF;
    Yii::app()->clientScript->registerScript('removeRow',$js,CClientScript::POS_READY);

    $js = <<<EOF
$(document).ready(function(){
	var ct = $('#tblDetail tr').eq(1).html();
	$('#dtltemplate').attr('value',ct);
});

$('#btnAddRow').on('click',function() {
	var r = $('#tblDetail tr').length;
	if (r>0) {
		var nid = '';
		var ct = $('#dtltemplate').val();
		$('#tblDetail tbody:last').append('<tr>'+ct+'</tr>');
		$('#tblDetail tr').eq(-1).find('[id*=\"AppointTripSetForm_\"]').each(function(index) {
			var id = $(this).attr('id');
			var name = $(this).attr('name');

			var oi = 0;
			var ni = r;
			id = id.replace('_'+oi.toString()+'_', '_'+ni.toString()+'_');
			$(this).attr('id',id);
			name = name==undefined?'':name.replace('['+oi.toString()+']', '['+ni.toString()+']');
			$(this).attr('name',name);

			if (id.indexOf('_z_index') != -1) $(this).attr('value',0);
			if (id.indexOf('_id') != -1) $(this).attr('value',0);
			if (id.indexOf('_audit_user') != -1){
			     $(this).removeClass('select2-hidden-accessible').val('').next('span').remove();
                $(this).select2({
                    multiple: false,
                    maximumInputLength: 10,
                    language: '$lang',
                    disabled: $disabled
                });
			}
		});
		if (nid != '') {
			var topos = $('#'+nid).position().top;
			$('#tbl_detail').scrollTop(topos);
		}
	}
});
EOF;
    Yii::app()->clientScript->registerScript('addRow',$js,CClientScript::POS_READY);
}

$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnEmployee', 'employee', 'employee_id','employee_name',array(),false,array("searchType"=>"searchType"));
Yii::app()->clientScript->registerScript('lookupEmployee',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('appointTripSet/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


