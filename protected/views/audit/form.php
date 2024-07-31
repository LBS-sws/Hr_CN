<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('audit/index'));
}
$this->pageTitle=Yii::app()->name . ' - Audit Form';
?>

<style>
    select[readonly="readonly"]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'audit-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Audit Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('audit/index')));
		?>
        <?php
            if($model->scenario!='view'){
                if($model->staff_status == 2){
                    echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                        'submit'=>Yii::app()->createUrl('audit/audit')));
                }
                if($model->staff_status == 2){
                    echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','Rejected'), array(
                        'data-toggle'=>'modal','data-target'=>'#jectdialog'));
                }
            }
        ?>
	</div>

<?php if ($model->scenario!='new'): ?>
	<div class="btn-group pull-right" role="group">
        <?php if ($model->scenario!='new'){
            //流程
            echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                'data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
        } ?>
        <?php if ($model->staff_status == 4): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-word-o"></span> '.Yii::t('contract','Staff Contract'),array(
                'id'=>"down_btn_word"
            ));
            ?>
        <?php endif; ?>
        <?php
        $counter = ($model->no_of_attm['employ'] > 0) ? ' <span id="docemploy" class="label label-info">'.$model->no_of_attm['employ'].'</span>' : ' <span id="docemploy"></span>';
        echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploademploy',)
        );
        ?>
	</div>
<?php endif; ?>
	</div></div>

	<div class="box box-info">
        <div class="box-body" style="position: relative">
            <?php if (!empty($model->image_user)): ?>
                <img src="<?php echo Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_user"));?>" width="150px" style="position: absolute;right: 5px;top: 5px;z-index: 2;">
            <?php endif; ?>

            <?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'staff_status'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'table_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php
                    echo $form->dropDownList($model, 'table_type',StaffFun::getTableTypeList(false),
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <?php
            $this->renderPartial('//employView/employform',array('model'=>$model,
                'form'=>$form,
                'readonly'=>($model->scenario=='view'||($model->staff_status != 1)),
            ));
            ?>

            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'||$model->staff_status != 1))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
    <?php
    $this->renderPartial('//site/ject',array(
        'form'=>$form,
        'model'=>$model,
        'rejectName'=>"ject_remark",
        'submit'=>Yii::app()->createUrl('audit/reject'),
    ));
    ?>
</section>

<?php
$this->renderPartial('//site/removedialog');
if($model->table_type==1){
    $this->renderPartial('//employView/historylist',array('model'=>$model));
}else{
    $this->renderPartial('//external/historylist',array('model'=>$model));
}
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'EMPLOY',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->scenario=='view'||($model->staff_status != 1)),
));
?>
<?php
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/
Script::genFileUpload($model,$form->id,'EMPLOY');

$js = "
var staffStatus = '".$model->staff_status."';
$('#AuditForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
    
    //時間計算
    $('.test_add_time').on('change',function(){
        $.ajax({
            type: 'post',
            url: '".Yii::app()->createUrl('employ/addDate')."',
            data: {dateTime:$('.test_add_time').eq(1).val(),month:$('.test_add_time').eq(0).val()},
            dataType: 'json',
            success: function(data){
                $('.test_sum_time').val(data);
            }
        });
    }).trigger('change');
    //合同下載
    $('#down_btn_word').on('click',function(){
        window.open('".Yii::app()->createUrl('employee/Downfile?index='.$model->id)."');
        location.reload();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('employ/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
/*
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);
*/
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>

</div><!-- form -->

