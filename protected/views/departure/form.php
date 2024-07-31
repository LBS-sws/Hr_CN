<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('departure/index'));
}
$this->pageTitle=Yii::app()->name . ' - Departure Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'departure-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('contract','Departure Employee')." - ".$model->name; ?></strong>
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
                    'submit'=>Yii::app()->createUrl('departure/index')));
                ?>
            </div>

            <div class="btn-group pull-right" role="group">

                <?php if (Yii::app()->user->validFunction('ZR19')): ?>
                    <?php echo TbHtml::button('<span class="fa fa-backward"></span> '.Yii::t('contract','send back'), array(
                        'submit'=>Yii::app()->createUrl('departure/back')));
                    ?>
                <?php endif ?>
                <?php
                $counter = ($model->no_of_attm['signc'] > 0) ? ' <span id="docsignc" class="label label-info">'.$model->no_of_attm['signc'].'</span>' : ' <span id="docsignc"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('contract','sign attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadsignc',)
                );
                ?>
                <?php
                $counter = ($model->no_of_attm['employ'] > 0) ? ' <span id="docemploy" class="label label-info">'.$model->no_of_attm['employ'].'</span>' : ' <span id="docemploy"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploademploy',)
                );
                ?>
                <?php if ($model->scenario!='new'&&Yii::app()->user->validFunction('ZR02')){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                } ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php if (!empty($model->image_user)): ?>
                <img src="<?php echo Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_user"));?>" width="150px" style="position: absolute;right: 5px;top: 5px;z-index: 2;">
            <?php endif; ?>
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'city'); ?>
            <?php echo $form->hiddenField($model, 'staff_status'); ?>


            <div class="form-group">
                <?php echo $form->labelEx($model,'leave_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'leave_time',
                            array('class'=>'form-control pull-right','readonly'=>(true),));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'leave_reason',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'leave_reason',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <legend></legend>

            <?php
            $this->renderPartial('//employView/employform',array(
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
                        array('rows'=>3,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>


            <div class="form-group">
                <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'social_code',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'jj_card',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
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
    'ronly'=>(!Yii::app()->user->validFunction('ZE01')),
    'delBtn'=>(false),
));
?>
<?php
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'SIGNC',
    'header'=>Yii::t('contract','sign attachment'),
    'ronly'=>(true),
));
?>
<?php
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/
Script::genFileUpload($model,$form->id,'EMPLOY');
Script::genFileUpload($model,$form->id,'SIGNC');

$js = "
var staffStatus = '".$model->staff_status."';
$('#DepartureForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('employ/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js?2", CClientScript::POS_END);
/*
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);
 */

?>

<?php $this->endWidget(); ?>
</div><!-- form -->

