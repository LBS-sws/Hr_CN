<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('employee/index'));
}
$this->pageTitle=Yii::app()->name . ' - Employee Form';
?>

<style>
    input[readonly="readonly"]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'employee-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('contract','Employee Info'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('employee/index')));
                ?>
                <?php if ($model->scenario!='new'&&Yii::app()->user->validRWFunction('ZE03')): ?>
                    <?php echo TbHtml::button('<span class="fa fa-pencil"></span> '.Yii::t('contract','Update'), array(
                        'submit'=>Yii::app()->createUrl('history/form',array("index"=>$model->id,"type"=>"update"))));
                    ?>
                    <?php echo TbHtml::button('<span class="fa fa-coffee"></span> '.Yii::t('contract','Staff Changes'), array(
                        'submit'=>Yii::app()->createUrl('history/form',array("index"=>$model->id,"type"=>"change"))));
                    ?>
                    <?php echo TbHtml::button('<span class="fa fa-user-times"></span> '.Yii::t('contract','Staff Departure'), array(
                        'submit'=>Yii::app()->createUrl('history/form',array("index"=>$model->id,"type"=>"departure"))));
                    ?>
                    <?php echo TbHtml::button('<span class="fa fa-refresh"></span> '.Yii::t('contract','displace'), array(
                        'data-toggle'=>'modal','data-target'=>'#displaceDialog'));
                    ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($model->staffHasAgreement())): ?>
                <div class="btn-group pull-right" role="group">
                    <?php if ($model->word_status == 1): ?>
                        <?php echo TbHtml::button('<span class="fa fa-file-word-o"></span> '.Yii::t('contract','Supplemental Agreement'),array(
                            'name'=>'btnAgreement','id'=>'btnAgreement','data-toggle'=>'modal','data-target'=>'#agreementdialog'));
                        ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($model->scenario!='new'&&Yii::app()->user->validFunction('ZR02')): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-file-word-o"></span> '.Yii::t('contract','Staff Contract'),array(
                        'id'=>"down_btn_word"
                    ));
                    ?>
                </div>
            <?php endif; ?>
            <?php if (Yii::app()->user->validFunction('ZR01')): ?>
                <div class="btn-group pull-right" role="group">
                    <?php
                    echo TbHtml::button('<span class="fa fa-clone"></span> '.Yii::t('app','Contract Word'), array(
                        'name'=>'downOnly','id'=>'downOnly','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                    ?>
                </div>
            <?php endif; ?>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['signc'] > 0) ? ' <span id="docsignc" class="label label-info">'.$model->no_of_attm['signc'].'</span>' : ' <span id="docsignc"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('contract','sign attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadsignc',)
                );
                ?>
                <?php if ($model->scenario!='new'&&Yii::app()->user->validFunction('ZR02')){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                        'data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                } ?>
                <?php
                $counter = ($model->no_of_attm['employ'] > 0) ? ' <span id="docemploy" class="label label-info">'.$model->no_of_attm['employ'].'</span>' : ' <span id="docemploy"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'data-toggle'=>'modal','data-target'=>'#fileuploademploy',)
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
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'city'); ?>
            <?php echo $form->hiddenField($model, 'staff_status'); ?>

            <?php
            $this->renderPartial('//employView/employform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'||$model->staff_status != 1),
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

            <div class="form-group">
                <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'social_code',
                        array('readonly'=>($model->scenario=='view'||$model->staff_status != 1))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'jj_card',
                        array('readonly'=>($model->scenario=='view'||$model->staff_status != 1))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$this->renderPartial('//employView/historylist',array('model'=>$model));
$this->renderPartial('//employView/agreementlist',array('model'=>$model));
$this->renderPartial('//employView/displaceDialog',array('model'=>$model));
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'EMPLOY',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->scenario=='view'||($model->staff_status != 1)),
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
$('#EmployeeForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
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
<?php
$this->renderPartial('//site/contractlist',array('model'=>$model,'form'=>$form,'submit'=>Yii::app()->createUrl('employee/downOnlyContract')));
?>

