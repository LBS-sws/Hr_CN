<?php
$this->pageTitle=Yii::app()->name . ' - pinInventory';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'pinInventory-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Pin Inventory'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('pinInventory/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('pinInventory/save')));
            ?>
        <?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php
                    //库存记录
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('contract','Inventory History'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#historydialog'));
                 ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'pin_name_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->dropDownList($model, 'pin_name_id',PinNameForm::getPinNameList(),
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'inventory',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->numberField($model, 'inventory',
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'safe_stock',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->numberField($model, 'safe_stock',
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'z_index',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->numberField($model, 'z_index',
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/removedialog');
$this->renderPartial('//site/history',array("tableHtml"=>PinInventoryForm::getHistoryList($model->id)));
?>
<?php

$js = "
    $('#changeStatus').change(function(){
        var type = $(this).val();
        if(type==''||type==undefined){
            $('#tblFlow>tbody>tr').removeClass('hidden');
        }else{
            $('#tblFlow>tbody>tr').each(function(){
                if(type==$(this).data('type')){
                    $(this).removeClass('hidden');
                }else{
                    $(this).addClass('hidden');
                }
            });
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('pinInventory/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

