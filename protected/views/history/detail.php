<?php
if (empty($model->employee_id)){
    $this->redirect(Yii::app()->createUrl('history/index'));
}
$this->pageTitle=Yii::app()->name . ' - Detail Form';
?>
<style>
    input[readonly="readonly"]{pointer-events: none;}
    .compare-bottom-div{ position: fixed;bottom: 10px;right: 10px;width: 420px;max-height: 400px;box-shadow: 0 1px 1px rgba(0,0,0,0.1);background: #fff;overflow-y: scroll;z-index: 2}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'detail-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
));
$form->compareName=Yii::t("code","Name");
$form->compareOldStr=Yii::t("contract","history new");
$form->compareNewStr=Yii::t("contract","history ago");
?>

<section class="content-header">
    <h1>
        <strong><?php echo $model->setFormTitle()." - ".$model->name; ?></strong>
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
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['employee'] > 0) ? ' <span id="docemployee" class="label label-info">'.$model->no_of_attm['employee'].'</span>' : ' <span id="docemployee"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploademployee',)
                );
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
            <?php echo $form->hiddenField($model, 'staff_status'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>

            <?php if ($model->opr_type=="transfer"): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'change_city_old',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'city',WordForm::getCityListAll(),
                            array('disabled'=>(true))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'change_city',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'change_city',WordForm::getCityListAll(),
                            array('disabled'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'update_remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <legend></legend>

            <?php
            $this->renderPartial('//employView/employcompare',array(
                'oldModel'=>$oldModel,
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'),
            ));
            ?>

            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textFieldCompare($oldModel,$model, 'social_code',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textFieldCompare($oldModel,$model, 'jj_card',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $form->echoCompareDiv();//對比信息列表
    ?>
</section>


<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'EMPLOYEE',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->scenario=='view'),
));
?>
<?php
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/
Script::genFileUpload($model,$form->id,'EMPLOYEE');

$js = "
$('input.compare-error,select.compare-error,textarea.compare-error').each(function(){
    $(this).parents('div:first').addClass('has-error');
});
var staffStatus = '".$model->staff_status."';
$('#HistoryForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>

</div><!-- form -->

