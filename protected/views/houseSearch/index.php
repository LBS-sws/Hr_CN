<?php
$this->pageTitle=Yii::app()->name . ' - Customer Enquiry';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'customer-enq',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
//'layout'=>TbHtml::FORM_LAYOUT_INLINE,
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    #HouseSearchList_noOfItem{ width: 30%;display: inline-block;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','household Search'); ?></strong>
	</h1>
</section>


<section class="content">
	<div class="box"><div class="box-body">
		<div class="form-group">
			<?php echo $form->labelEx($model,'address',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-5">
				<?php echo $form->textField($model, 'address', array('maxlength'=>20)); ?>
			</div>

			<?php echo $form->labelEx($model,'department',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-2">
				<?php echo $form->textField($model, 'department', array('maxlength'=>250)); ?>
			</div>
		</div>
		<div class="btn-group" role="group">
			<?php 
				echo TbHtml::button('dummyButton', array('style'=>'display:none','disabled'=>true,'submit'=>'#',));
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Search'), array(
						'id'=>'btnSubmit', 
					)); 
			?>
		</div>
	</div></div>

	<?php 
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('contract','Employee List'),
			'model'=>$model,
				'viewhdr'=>'//houseSearch/_listhdr',
				'viewdtl'=>'//houseSearch/_listdtl',
				'hasSearchBar'=>false,
				'item_bool'=>true,
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
switch(Yii::app()->language) {
	case 'zh_cn': $lang = 'zh-CN'; break;
	case 'zh_tw': $lang = 'zh-TW'; break;
	default: $lang = Yii::app()->language;
}
//$disabled = (!$model->isReadOnly()) ? 'false' : 'true';
	$js = <<<EOF
$('#HouseSearchList_city_list').select2({
	tags: false,
	multiple: true,
	maximumInputLength: 0,
	maximumSelectionLength: 200,
	allowClear: true,
	language: '$lang',
	disabled: false
});

$('#HouseSearchList_city_list').on('select2:opening select2:closing', function( event ) {
    var searchfield = $(this).parent().find('.select2-search__field');
    searchfield.prop('disabled', true);
});
EOF;
Yii::app()->clientScript->registerScript('select2',$js,CClientScript::POS_READY);

$js = <<<EOF
function showdetail(id) {
	var icon = $('#btn_'+id).attr('class');
	if (icon.indexOf('plus') >= 0) {
		$('.detail_'+id).show();
		$('#btn_'+id).attr('class', 'fa fa-minus-square');
	} else {
		$('.detail_'+id).hide();
		$('#btn_'+id).attr('class', 'fa fa-plus-square');
	}
}
EOF;
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_HEAD);

$url = Yii::app()->createUrl('houseSearch/index', array('pageNum'=>1));
$js = <<<EOF
$('#btnSubmit').on('click', function() {
	Loading.show();
	jQuery.yii.submitForm(this,'$url',{});
});
EOF;
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);

//$js = Script::genTableRowClick();
//Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

