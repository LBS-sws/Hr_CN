<?php
$this->pageTitle=Yii::app()->name . ' - ReviewAllot Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'reviewAllot-form',
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
		<strong><?php echo Yii::t('contract','reviewAllot Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('reviewAllot/index')));
		?>

        <?php if (!$model->getReadonly()): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('app','review'), array(
                'submit'=>Yii::app()->createUrl('reviewAllot/save')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Draft'), array(
                'submit'=>Yii::app()->createUrl('reviewAllot/draft')));
            ?>
        <?php endif ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->status_type == 1&&$model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-reply-all"></span> '.Yii::t('contract','undo'), array(
                    'submit'=>Yii::app()->createUrl('reviewAllot/undo')));
                ?>
                <?php endif ?>
                <?php
                $counter = ($model->no_of_attm['review'] > 0) ? ' <span id="docreview" class="label label-info">'.$model->no_of_attm['review'].'</span>' : ' <span id="docreview"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadreview',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'review_id'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'year_type'); ?>

            <?php
            $this->renderPartial('//site/reviewStaff',array(
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::textField("review_type",DeptForm::getReviewType($model->review_type),array("readonly"=>true)) ?>
                </div>
            </div>

            <?php
                echo $model->returnChangeReviewType();
            ?>

            <legend><?php echo Yii::t("contract","reviewAllot manager");?></legend><!--考核经理-->
            <div class="form-group">
                <div class="col-sm-5 col-sm-offset-2">
                    <?php
                    $ReviewAllotForm = new ReviewAllotForm();
                    echo $ReviewAllotForm->returnManager($model);
                    ?>
                </div>
            </div>



            <?php
            $this->renderPartial('//site/reviewSelect',array(
                'model'=>$model,
                'button_template'=>!$model->getReadonly(),
                'linkAction'=>"applytemplate",
            ));
            ?>
		</div>

	</div>
</section>
<?php
$fileUpload = array('model'=>$model,
    'form'=>$form,
    'doctype'=>'REVIEW',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>$model->getScenario()=='view',
);
if(in_array($model->status_type,array(1,2,3))){
    $fileUpload['delBtn']=false;
}

$this->renderPartial('//site/fileupload',$fileUpload);
?>

<xmp hidden id="readyOne">
    <?php echo $model->getRowOnly($model,":num",$model->getReviewManagerList($model->city),false,array("employee_id"=>"","num"=>""));?>
</xmp>
<?php
Script::genFileUpload($model,$form->id,'REVIEW');


switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = ($model->scenario!='view') ? 'false' : 'true';
$js="
$('.selectStaff').select2({
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
Yii::app()->clientScript->registerScript('searchStaffFunction',$js,CClientScript::POS_READY);

$js = "
    $(\"select[name^='ReviewAllotForm[tem_list]']\").attr('readonly','readonly').addClass('readonly');
    var rowHtml = $('#readyOne').html();
    $('#readyOne').remove();
    $('#addManager').on('click',function(){
        var num = $('#managerTable>tbody').data('num');
        num = parseInt(num,10);
        num++;
        $('#managerTable>tbody').data('num',num)
        var newHtml = rowHtml.replace(/:num/g,num);
        $('#managerTable>tbody').append(newHtml);
        $('#managerTable>tbody').find('.selectStaff').last().select2({
            multiple: false,
            maximumInputLength: 10,
            language: '$lang',
            disabled: $disabled
        });
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
    
    $('#changeTwo').keyup(function(){
        var value = $(this).val();
        var change = $(this).data('change');
        if(!isNaN(value)){
            if(change == 'three'){
                value *=10;
                value = parseFloat(value).toFixed(2);
            }else{
                value =15-(value*0.5);
                value = value<0?0:value;
            }
            $('#change_value').val(value);
        }
    });
    $('#changeTwo').trigger('keyup');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

