<?php
if (empty($model->id)){
    $this->redirect(Yii::app()->createUrl('auditHistory/index'));
}
$this->pageTitle=Yii::app()->name . ' - AuditHistory Form';
?>
<style>
    input[readonly="readonly"]{pointer-events: none;}
    .compare-bottom-div{ position: fixed;bottom: 10px;right: 10px;width: 420px;max-height: 400px;box-shadow: 0 1px 1px rgba(0,0,0,0.1);background: #fff;overflow-y: scroll;z-index: 2}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'auditHistory-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
));
$form->compareName=Yii::t("code","Name");
$form->compareOldStr=Yii::t("contract","update ago");
$form->compareNewStr=Yii::t("contract","update new");
?>

<section class="content-header">
    <h1>
        <strong><?php echo $model->setFormTitle(); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('auditHistory/index')));
                ?>
            </div>
            <?php
            if($model->scenario!='view'){
                if($model->staff_status == 2){
                    echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                        'submit'=>Yii::app()->createUrl('auditHistory/audit')));
                }
                if($model->staff_status == 2){
                    echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','Rejected'), array(
                        'data-toggle'=>'modal','data-target'=>'#jectdialog'));
                }
            }
            ?>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                        'data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                } ?>
                <?php
                if($model->table_type==1){
                    $counter = ($model->no_of_attm['employee'] > 0) ? ' <span id="docemployee" class="label label-info">'.$model->no_of_attm['employee'].'</span>' : ' <span id="docemployee"></span>';
                    echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                            'data-toggle'=>'modal','data-target'=>'#fileuploademployee',)
                    );
                }else{
                    $counter = ($model->no_of_attm['employ'] > 0) ? ' <span id="docemploy" class="label label-info">'.$model->no_of_attm['employ'].'</span>' : ' <span id="docemploy"></span>';
                    echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                            'data-toggle'=>'modal','data-target'=>'#fileuploademploy',)
                    );
                }
                ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body" style="position: relative">
            <?php if (!empty($model->image_user)): ?>
                <img src="<?php echo Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_user"));?>" width="150px" style="position: absolute;right: 5px;top: 5px;z-index: 2;">
            <?php endif; ?>

            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'city'); ?>
            <?php echo $form->hiddenField($model, 'change_city'); ?>
            <?php echo $form->hiddenField($model, 'staff_status'); ?>
            <?php echo $form->hiddenField($model, 'operation'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>

            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo Yii::t("contract","Operation Status");?></label>
                <div class="col-sm-5">
                    <input class="input-10 form-control readonly" readonly type="text" value="<?php echo Yii::t("contract",$model->operation);?>">
                </div>
            </div>
            <?php if ($model->operation=="change"): ?>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'effect_time',array('class'=>"col-sm-2 control-label")); ?>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'effect_time',
                                array('class'=>'form-control pull-right','readonly'=>(true),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'opr_type',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'opr_type',StaffFun::getOperationTypeList($model->employee_id),
                            array('disabled'=>(true))
                        ); ?>
                    </div>
                </div>
                <?php if ($model->opr_type=="transfer"): ?>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'change_city_old',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-3">
                            <?php echo $form->dropDownList($model, 'city',StaffFun::getCityListAll(),
                                array('disabled'=>(true))
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'change_city',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-3">
                            <?php echo $form->dropDownList($model, 'change_city',StaffFun::getCityListAll(),
                                array('disabled'=>(true))
                            ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'update_remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php if ($model->operation=="departure"): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'leave_time',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'leave_time',
                                array('class'=>'form-control pull-right','disabled'=>(true),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'leave_reason',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-7">
                        <?php echo $form->textArea($model, 'leave_reason',
                            array('rows'=>3,'disabled'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <legend></legend>

            <?php
            $city = $model->city;
            $model->city=$model->change_city;
            $this->renderPartial('//employView/employcompare',array(
                'oldModel'=>$oldModel,
                'form'=>$form,
                'model'=>$model,
                'readonly'=>(true),
            ));
            $model->city=$city;
            ?>


            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textFieldCompare($oldModel,$model, 'social_code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textFieldCompare($oldModel,$model, 'jj_card',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $form->echoCompareDiv();//對比信息列表

    $this->renderPartial('//site/ject',array(
        'form'=>$form,
        'model'=>$model,
        'rejectName'=>"ject_remark",
        'submit'=>Yii::app()->createUrl('AuditHistory/reject'),
    ));
    ?>
</section>

<?php
$id = $model->id;
$model->id = $model->employee_id;
if($model->table_type==1){
    $this->renderPartial('//employView/historylist',array('model'=>$model));
}else{
    $this->renderPartial('//external/historylist',array('model'=>$model));
}
$model->id = $id;
?>
<?php
if($model->table_type==1){
    $this->renderPartial('//site/fileupload',array('model'=>$model,
        'form'=>$form,
        'doctype'=>'EMPLOYEE',
        'header'=>Yii::t('misc','Attachment'),
        'ronly'=>($model->scenario=='view'),
    ));
    Script::genFileUpload($model,$form->id,'EMPLOYEE');
}else{
    $model->id = $model->employee_id;
    $this->renderPartial('//site/fileupload',array('model'=>$model,
        'form'=>$form,
        'doctype'=>'EMPLOY',
        'header'=>Yii::t('misc','Attachment'),
        'ronly'=>($model->scenario=='view'),
    ));
    Script::genFileUpload($model,$form->id,'EMPLOY');
    $model->id = $id;
}
?>
<?php
$js = "
$('input.compare-error,select.compare-error,textarea.compare-error').each(function(){
    $(this).parents('div:first').addClass('has-error');
});
var staffStatus = '".$model->staff_status."';
$('#AuditHistoryForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);

/*Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);*/
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>
</div><!-- form -->

