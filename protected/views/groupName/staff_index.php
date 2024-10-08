<?php
$this->pageTitle=Yii::app()->name . ' - GroupName';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'groupStaff-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong>
            <?php echo TbHtml::link(Yii::t('app','Group Name'),Yii::app()->createUrl('groupName/index')); ?>
            <?php echo " - ".GroupNameForm::getGroupNameToID($model->index); ?>
        </strong>
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
        echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
            'submit'=>Yii::app()->createUrl('groupName/index'),
        ));
        ?>
	</div>
	<div class="btn-group pull-right" role="group">
		<?php
			if (Yii::app()->user->validRWFunction('ZC24'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
					'submit'=>Yii::app()->createUrl('groupName/staffAdd',array("index"=>$model->index)),
				));
		?>
	</div>
	</div></div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('group','Group Staff List'),
			'model'=>$model,
				'viewhdr'=>'//groupName/staff_listhdr',
				'viewdtl'=>'//groupName/staff_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>array(
							'employee_code',
							'employee_name',
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

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
