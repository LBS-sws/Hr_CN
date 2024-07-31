<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('supportAudit/index'));
}
$this->pageTitle=Yii::app()->name . ' - supportAudit';
?>
<style>
    tbody>tr{position: relative;}
    select[readonly="readonly"]{pointer-events: none;}
    td.remark{;min-width: 350px;}
    tr.text-weight>td{font-weight: bold;}

    select[readonly="readonly"]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'supportAudit-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','support apply form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('supportAudit/index')));
		?>

        <?php if ($model->scenario=='new'): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','support'), array(
                'submit'=>Yii::app()->createUrl('supportAudit/support')));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'&&in_array($model->status_type,array(2,3,4))): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('supportAudit/save')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                    'id'=>'btnConfirm','data-toggle'=>'modal','data-target'=>'#confirmDialog',)
            );
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'&&in_array($model->status_type,array(2,3))): ?>
            <?php echo TbHtml::button('<span class="fa fa-bug"></span> '.Yii::t('contract','Wait in line'), array(
                'submit'=>Yii::app()->createUrl('supportAudit/wait')));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'&&$model->status_type == 6): ?>
            <?php echo TbHtml::button('<span class="fa fa-legal"></span> '.Yii::t('contract','Finish'), array(
                'submit'=>Yii::app()->createUrl('supportAudit/finish')));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'&&$model->status_type == 9): ?>
            <?php echo TbHtml::button('<span class="fa fa-plug"></span> '.Yii::t('contract','agreed early'), array(
                'submit'=>Yii::app()->createUrl('supportAudit/early')));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'&&$model->status_type == 10): ?>
            <?php echo TbHtml::button('<span class="fa fa-retweet"></span> '.Yii::t('contract','agreed renewal'), array(
                'submit'=>Yii::app()->createUrl('supportAudit/renewal')));
            ?>
        <?php endif ?>
        <?php if ($model->scenario=='edit'&&$model->status_type == 2): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='view'): ?>
                    <?php if ($model->status_type == 6): ?>
                        <?php echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('contract','undo'), array(
                            'submit'=>Yii::app()->createUrl('supportAudit/undo')));
                        ?>
                    <?php endif ?>

                    <?php if (in_array($model->status_type,array(9,10))): ?>
                        <?php echo TbHtml::button('<span class="fa fa-mail-forward"></span> '.Yii::t('contract','Rejected'), array(
                            'data-toggle'=>'modal','data-target'=>'#jectdialog'));
                        ?>
                    <?php endif ?>

                    <?php if (in_array($model->status_type,array(2,3,4))): ?>
                        <?php echo TbHtml::button('<span class="fa fa-desktop"></span> '.Yii::t('contract','Reply/end'), array(
                            'submit'=>Yii::app()->createUrl('supportAudit/endReply')));
                        ?>
                    <?php endif ?>
                <?php endif ?>
                <?php if ($model->scenario!='new'): ?>
                    <?php echo TbHtml::button('<span class="fa fa-calendar"></span> '.Yii::t('app','History'), array(
                        'data-toggle'=>'modal','data-target'=>'#historydialog'));
                    ?>
                <?php endif ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>

            <?php if ($model->scenario!='new'): ?>
                <?php if (in_array($model->status_type,array(9,10))): ?>
                    <div class="form-group has-error">
                        <?php echo $form->labelEx($model,'early_date'.$model->status_type,array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-3">
                            <?php echo $form->textField($model, 'early_date',
                                array('class'=>'form-control','readonly'=>(true)));
                            ?>
                        </div>
                    </div>
                    <div class="form-group has-error">
                        <?php echo $form->labelEx($model,'early_remark'.$model->status_type,array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-4">
                            <?php echo $form->textArea($model, 'early_remark',
                                array('rows'=>3,'readonly'=>(true))
                            ); ?>
                        </div>
                    </div>
                <?php endif ?>
                <?php if (in_array($model->status_type,array(2,3,4))): ?>
                    <div class="form-group has-error">
                        <?php echo $form->labelEx($model,'apply_remark',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-4">
                            <?php echo $form->textArea($model, 'apply_remark',
                                array('rows'=>3,'readonly'=>(true))
                            ); ?>
                        </div>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'support_code',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'support_code',
                            array('class'=>'form-control','readonly'=>(true)));
                        ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'service_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'service_type',SupportApplyList::getServiceList(),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <?php if ($model->apply_type==2): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'apply_type',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo TbHtml::textField('apply_type', Yii::t("contract","stationary point"),
                            array('class'=>'form-control','readonly'=>(true)));
                        ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'apply_city',$model->getAllCity(),
                        array('class'=>'form-control','readonly'=>($model->scenario!='new')));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'apply_date',
                            array('class'=>'form-control','readonly'=>($model->getReadonly()),'id'=>"apply_date"));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_end_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'apply_end_date',
                            array('class'=>'form-control','readonly'=>(true),'id'=>"apply_end_date"));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'length_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group">
                        <?php echo $form->textField($model, 'apply_length',
                            array('rows'=>3,'readonly'=>($model->getReadonly()),'id'=>"apply_length")
                        ); ?>
                        <span class="input-group-btn" style="width: 80px;">
                        <?php echo $form->dropDownList($model, 'length_type',array(1=>"个月",2=>"天"),
                            array('rows'=>3,'readonly'=>($model->getReadonly()),'id'=>"length_type")
                        ); ?>
                    </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'privilege',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'privilege',SupportApplyList::getPrivilegeList(),
                        array('readonly'=>($model->getReadonly()),'id'=>'privilege')
                    ); ?>
                </div>
            </div>
            <div class="form-group" id="privilege_user" style="<?php if($model->privilege != 1){ echo "display:none;";}?>">
                <?php echo $form->labelEx($model,'privilege_user',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'privilege_user',SupportApplyForm::getPrivilegeUserList($model->apply_city),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <?php if (!in_array($model->status_type,array(2,3,4))): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'apply_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'apply_remark',
                            array('rows'=>3,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'audit_remark',
                        array('rows'=>3,'readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'employee_id',$model->getSupportEmployee(),
                        array('class'=>'form-control','readonly'=>($model->getReadonly())));
                    ?>
                </div>
            </div>

            <?php if ($model->update_type==1): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'update_remark',
                            array('rows'=>3,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <?php
            if(in_array($model->status_type,array(6,9))){ //顯示評核分數
                echo "<legend>".Yii::t("contract","reviewAllot project")."</legend>";
                $tabs = $model->getTabList();
                $this->widget('bootstrap.widgets.TbTabs', array(
                    'tabs'=>$tabs,
                ));
            }else{ //分配考核選項
                $this->renderPartial('//site/reviewSelect',array(
                    'model'=>$model,
                    'button_template'=>!$model->getReadonly(),
                    'linkAction'=>"applytemplate",
                ));
            }
            ?>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/removedialog');
$this->renderPartial('//site/history',array('tableHtml'=>SupportSearchForm::getHistoryHtml($model->id)));
if(in_array($model->status_type,array(10,9))){
    $this->renderPartial('//site/ject',array('form'=>$form,'model'=>$model,'rejectName'=>'reject_remark','submit'=>Yii::app()->createUrl('supportAudit/reject')));
}

$content = "<p>".Yii::t('contract','confirmed and submitted, it cannot be modified after submission?')."</p>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'confirmDialog',
    'header'=>Yii::t('contract','Confirm Audit'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnConfirmData','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));//Yii::app()->createUrl('supportAudit/audit')
?>

<?php

$link = Yii::app()->createUrl('supportAudit/audit');
$js = "
$('td.remark').each(function(){
    if(!$(this).parents('.tab-pane.fade').hasClass('active')){
        $(this).parents('.tab-pane.fade').addClass('active in');
    }
    var height = $(this).css('width','360px').outerHeight();
    $(this).parent('tr').height(height);
    $(this).css({
        'position':'absolute',
        'height':height+'px'
    });
    $(this).parents('.tab-pane.fade').removeClass('active in');
});
$('#btnConfirmData').on('click',function() {
	var elm=$('#btnConfirm');
	$('#removedialog').modal('hide');
	jQuery.yii.submitForm(elm,'$link',{});
});

$('#privilege').on('change',function(){
    if($(this).val() == 1){
        $('#privilege_user').show();
    }else{
        $('#privilege_user').hide();
    }
});

$('#SupportAuditForm_apply_city').on('change',function(){
    $.ajax({
        type: 'post',
        url: '".Yii::app()->createUrl('supportAudit/ajaxChangeCity')."',
        data: {city:$(this).val()},
        dataType: 'json',
        success: function(data){
            if(data.status == 1){
                $('#SupportAuditForm_privilege_user').html(data.html);
            }
        }
    });
});
$('#apply_date,#apply_length,#length_type,#privilege').on('change',function(){
    if($('#length_type').val()==1){
        $('#apply_length').val(1);
        $('#apply_length').prop('disabled',true);
    }else{
        $('#apply_length').prop('disabled',false);
    }
    $.ajax({
        type: 'post',
        url: '".Yii::app()->createUrl('supportApply/ajaxEndDate')."',
        data: {apply_date:$('#apply_date').val(),apply_length:$('#apply_length').val(),privilege:$('#privilege').val(),length_type:$('#length_type').val(),support:1},
        dataType: 'json',
        success: function(data){
            if(data.status == 1){
                $('#apply_end_date').val(data.endDate);
                $('#SupportAuditForm_employee_id').html(data.html);
            }
        }
    });
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

if ($model->scenario!='view'&& in_array($model->status_type,array(1,2,3))) {
    $js = Script::genDatePicker(array(
        'apply_date'
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('supportAudit/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

