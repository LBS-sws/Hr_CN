

<legend><?php echo Yii::t("contract","reviewAllot project");?></legend><!--考核项目-->
<?php if ($button_template): ?>
    <?php echo TbHtml::button(Yii::t('contract','review template'), array(
        'class'=>"pull-right btn btn-default",'id'=>'btnTemplate'));
    ?>
<?php endif ?>
<?php
echo TemplateForm::parentTemStrDiv($model);
?>

<?php if ($button_template): ?>
<?php
$list = TbHtml::listBox('lsttemplate', '', array(), array(
        'size'=>'15')
);

$content = "
<div class=\"row\">
	<div class=\"col-sm-11\" id=\"lookup-list\">
			$list
	</div>
</div>
";

$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'applytempdialog',
    'header'=>Yii::t('contract','review template'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnApply','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));

$className = get_class($model);
$mesg = Yii::t('dialog','No Record Found');
$link = Yii::app()->createAbsoluteUrl("lookup");
$js = "
$('#btnTemplate').on('click',function(){
	var link = '$link/template';
	$('#applytempdialog').modal('show');
	$.ajax({
		type: 'GET',
		url: link,
		data: [],
		dataType: 'json',
		success: function(data) {
			$('#lsttemplate').empty();
			$.each(data, function(index, element) {
				$('#lsttemplate').append('<option value=\"'+element.id+'\">'+element.name+'</option>');
			});
			
			var count = $('#lsttemplate').children().length;
			if (count<=0) $('#lsttemplate').append('<option value=\"-1\">$mesg</option>');
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});

$('#btnApply').on('click',function(){
	var tid = $('#lsttemplate').val();
	var data = 'id='+tid;
	var link = '$link/$linkAction';
	$.ajax({
		type: 'GET',
		url: link,
		data: data,
		dataType: 'json',
		success: function(data) {
		    $(\"select[id^='\"+'$className'+\"_tem']\").val('');
			$.each(data, function(index, element) {
				var fldid = '$className'+'_tem_'+element;
				$('#'+fldid).val('on');
			});
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});
	";
Yii::app()->clientScript->registerScript('lookupTemplate',$js,CClientScript::POS_READY);
?>

<?php endif ?>