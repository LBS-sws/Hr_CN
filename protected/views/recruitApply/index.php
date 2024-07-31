<?php
$this->pageTitle=Yii::app()->name . ' - RecruitApply';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'recruitApply-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','recruit apply'); ?></strong>
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
		<?php 
			if (Yii::app()->user->validRWFunction('ZP03'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
					'submit'=>Yii::app()->createUrl('recruitApply/new'),
				)); 
		?>
	</div>
	</div></div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('recruit','recruit apply list'),
			'model'=>$model,
				'viewhdr'=>'//recruitApply/_listhdr',
				'viewdtl'=>'//recruitApply/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>array(
							'year',
							'city',
							'dept_name',
							'leader_name',
						),
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

	$js= Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
