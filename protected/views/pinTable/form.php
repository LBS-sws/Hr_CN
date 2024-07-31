<?php
$this->pageTitle=Yii::app()->name . ' - pinTable';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'pinTable-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .table-responsive>table {
        table-layout: fixed;
    }
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Pin Table'); ?></strong>
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
	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <?php if (!Yii::app()->user->isSingleCity()): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'city', PinTableForm::getAllowCity(),
                            array('id'=>"city")
                        ); ?>
                    </div>
                </div>
            <?php else: ?>
                <?php echo $form->hiddenField($model, 'city'); ?>
            <?php endif ?>

            <div class="form-group">
                <div class="col-sm-12">
                    <?php
                    echo $model->printTable();
                    ?>
                </div>
            </div>

		</div>
	</div>
</section>
<?php
$url = Yii::app()->createUrl('pinTable/index');
$js = "
    $('#city').on('change',function(){
        var city=$(this).val();
        window.location.href='{$url}?city='+city;
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

