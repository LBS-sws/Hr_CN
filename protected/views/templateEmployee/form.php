<?php
$this->pageTitle=Yii::app()->name . ' - TemplateEmployee Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'templateEmployee-form',
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
		<strong><?php echo Yii::t('contract','Template Employee Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('templateEmployee/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('TemplateEmployee/save')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'review_type'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model,'employee_name',array("readonly"=>true)) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'review_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo TbHtml::textField("review_type",DeptForm::getReviewType($model->review_type),array("readonly"=>true)) ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'tem_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model,'tem_id',$model->getTemplateListToCity($model->city),array('readonly'=>$model->getReadonly())) ?>
                </div>
            </div>


            <legend><?php echo Yii::t("contract","reviewAllot manager");?></legend><!--考核经理-->
            <div class="form-group">
                <div class="col-sm-5 col-sm-offset-2">
                    <?php
                    $ReviewAllotForm = new ReviewAllotForm();
                    echo $ReviewAllotForm->returnManager($model);
                    ?>
                </div>
            </div>
		</div>

	</div>
</section>
<xmp hidden id="readyOne">
    <?php echo $ReviewAllotForm->getRowOnly($model,":num",ReviewAllotForm::getReviewManagerList($model->city),false,array("employee_id"=>"","num"=>""));?>
</xmp>
<?php
switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = $model->getReadonly() ? 'true' : 'false';

$js = "
function selectStaffChange(){
    $('.selectStaff').select2({
        multiple: false,
        maximumInputLength: 10,
        language: '$lang',
        disabled: $disabled
    });
}
selectStaffChange();

    var rowHtml = $('#readyOne').html();
    $('#readyOne').remove();
    $('#addManager').on('click',function(){
        var num = $('#managerTable>tbody').data('num');
        num = parseInt(num,10);
        num++;
        $('#managerTable>tbody').data('num',num)
        var newHtml = rowHtml.replace(/:num/g,num);
        $('#managerTable>tbody').append(newHtml);
        selectStaffChange();
    });
    
    $('#managerTable').delegate('.delManager','click',function(){
        $(this).parents('tr').remove();
    });

    $('#managerTable').delegate('.changeNum','keyup',function(){
        var value = $(this).val();
        $(this).addClass('noneChange');
        var num = $('.changeNum').not('.noneChange').length;
        var sum = ".$model->count_num.";
        if(value<sum&&num!=0){
            $('.changeNum.noneChange').each(function(){
                sum-=$(this).val();
            });
            var newNum = Math.floor(sum/num);
            var proNum = sum%num;
            $('.changeNum').not('.noneChange').val(newNum);
            if(proNum!=0){
                $('.changeNum').not('.noneChange').last().val(newNum+proNum);
            }
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

