<?php
$this->pageTitle=Yii::app()->name . ' - RecruitSummary';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'recruitSummary-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','recruit summary'); ?></strong>
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
    <?php
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= TbHtml::dropDownList($modelName.'[year]',$model->year,$model->getYearList(),
        array("class"=>"form-control submit_year"));
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('recruit','recruit apply list'),
        'model'=>$model,
        'viewhdr'=>'//recruitSummary/_listhdr',
        'viewdtl'=>'//recruitSummary/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>array(
            'city'
        ),
        'search_add_html'=>$search_add_html,
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

<?php $this->renderPartial('//recruitApply/dtlview',array('model'=>$model)); ?>
<?php
$js= <<<EOF
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

$js= "
    $('.submit_year,.submit_year_type').on('change',function(){
        $('form:first').submit();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>
