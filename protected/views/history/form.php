<?php
if (empty($model->employee_id)){
    $this->redirect(Yii::app()->createUrl('/'));
}
$this->pageTitle=Yii::app()->name . ' - History Form';
?>
<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'history-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

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
            <div class="btn-group" role="group">
                <?php
                if(isset($back_type)&&!empty($back_type)){
                    $url = Yii::app()->createUrl('employee/edit',array('index'=>$model->employee_id));
                }else{
                    $url = Yii::app()->createUrl('history/index');
                }
                echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>$url));
                ?>
            </div>
            <?php if (!$model->readonly()): ?>
                <div class="btn-group" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                        'submit'=>Yii::app()->createUrl('history/save')));
                    ?>
                </div>
                <div class="btn-group" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','For Audit'), array(
                        'submit'=>Yii::app()->createUrl('history/audit')));
                    ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($model->id)&&(in_array($model->staff_status,array(1,3)))): ?>
                <div class="btn-group" role="group">
                    <?php
                    echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                    ?>
                </div>
            <?php endif; ?>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'&&Yii::app()->user->validFunction('ZR02')){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                } ?>
                <?php if (array_key_exists('employ',$model->no_of_attm)&&$model->no_of_attm['employ'] > 0): ?>
                    <?php
                    $counter = ($model->no_of_attm['employ'] > 0) ? ' <span id="docemploy" class="label label-info">'.$model->no_of_attm['employ'].'</span>' : ' <span id="docemploy"></span>';
                    echo TbHtml::button('<span class="fa  fa-file-text-o"></span> 原'.Yii::t('misc','Attachment').$counter, array(
                            'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploademploy',)
                    );
                    ?>
                <?php endif; ?>
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
            <?php echo $form->hiddenField($model, 'change_city'); ?>
            <?php echo $form->hiddenField($model, 'staff_status'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>

            <?php if ($model->staff_status==3): ?>
            <div class="form-group has-error">
                <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'ject_remark',
                        array('rows'=>3,'readonly'=>true)
                    ); ?>
                </div>
            </div>
            <legend></legend>
            <?php endif; ?>
            <?php if ($model->scenario=='change'||!empty($model->opr_type)): ?>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'effect_time',array('class'=>"col-sm-2 control-label")); ?>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'effect_time',
                                array('class'=>'form-control pull-right','readonly'=>($model->readonly()),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'opr_type',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'opr_type',StaffFun::getOperationTypeList($model->employee_id,$model->scenario),
                            array('disabled'=>($model->readonly()),'id'=>"opr_type")
                        ); ?>
                    </div>
                </div>
                <div class="opr_next_div  hide">
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
                                array('disabled'=>($model->readonly()),"id"=>"change_city")
                            ); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'update_remark',
                        array('rows'=>3,'readonly'=>($model->readonly()))
                    ); ?>
                </div>
            </div>
            <?php if ($model->scenario=='departure'||$model->operation=='departure'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'leave_time',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'leave_time',
                                array('class'=>'form-control pull-right','readonly'=>($model->readonly()),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'leave_reason',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-7">
                        <?php echo $form->textArea($model, 'leave_reason',
                            array('rows'=>3,'readonly'=>($model->readonly()))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <legend></legend>

            <?php
            $city = $model->city;
            $model->city=$model->change_city;
            $this->renderPartial('//employView/employform',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->readonly()),
            ));
            $model->city=$city;
            ?>

            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->readonly()))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'social_code',
                        array('readonly'=>($model->readonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'jj_card',
                        array('readonly'=>($model->readonly()))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>

<?php
$id = $model->id;
$model->id = $model->employee_id;
$this->renderPartial('//employView/historylist',array('model'=>$model));
$model->id = $id;
?>
<?php if (array_key_exists('employ',$model->no_of_attm)&&$model->no_of_attm['employ'] > 0): ?>
    <?php
    $history_id = $model->id;
    $model->id = $model->employee_id;
    $this->renderPartial('//site/fileupload',array(
        'model'=>$model,
        'form'=>$form,
        'doctype'=>"EMPLOY",
        'header'=>"原".Yii::t('misc','Attachment'),
        'ronly'=>(true),
    ));
    $model->id = $history_id;
    ?>
<?php endif; ?>
<?php
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>"EMPLOYEE",
    'header'=>"".Yii::t('misc','Attachment'),
    'ronly'=>($model->scenario=='view'),
));
?>
<?php
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/
Script::genFileUpload($model,$form->id,'EMPLOYEE');
Script::genFileUpload($model,$form->id,'EMPLOY');

$ajaxCityUrl = Yii::app()->createUrl('employ/changeCity');
$js = "
var staffStatus = '".$model->staff_status."';
$('#HistoryForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
    $('.file-update').upload({uploadUrl:'".Yii::app()->createUrl('employ/uploadImg')."'});
    
    $('body').delegate('.fileImgShow a','click',function(){
        $(this).parents('.form-group:first').find('input').val('');
        $(this).parents('.fileImgShow').parents('.form-group:first').find('input[type=\"file\"]').show();
        $(this).parents('.fileImgShow').remove();
    });
    
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
    
    
    $('.changeButton').on('change',function(){
        $('#HistoryForm_staff_type').val($(this).find('option:selected').data('dept'));
    });
    //合同期限變化
    $('.fixTime').on('change',function(){
        var netDom = $(this).parents('.form-group:first').next('.form-group');
        if($(this).val() == 'nofixed'){
            netDom.find('input').eq(1).val('').prop('readonly',true).addClass('readonly');
        }else{
            netDom.find('input').eq(1).prop('readonly',false).removeClass('readonly');
        }
    });
    //調職變化
    $('#opr_type').on('change',function(){
        if($(this).val() == 'transfer'){
            $('.opr_next_div').removeClass('hide');
        }else{
            $('.opr_next_div').addClass('hide');
            if($('#change_city').val()!=$('#HistoryForm_city').val()){
                $('#change_city').val($('#HistoryForm_city').val());
                $('#change_city').trigger('change');
            }
        }
    }).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


if ($model->scenario!='view'||$model->staff_status == 3) {
    $js = Script::genDatePicker(array(
        'HistoryForm_leave_time',
        'birth_time',
        'HistoryForm_entry_time',
        'start_time',
        'end_time',
        'test_start_time',
        'HistoryForm_user_card_date',
        'HistoryForm_effect_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('history/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js?2", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>
</div><!-- form -->

