<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('supportApply/index'));
}
$this->pageTitle=Yii::app()->name . ' - supportApply';
?>
<style>
    tbody>tr{position: relative;}
    select[readonly="readonly"]{pointer-events: none;}
    .reviewSumDiv{position:relative;display: inline-block;padding: 7px;}
    .reviewSumDiv_hint{position: absolute;top: 100%;left: 50%;margin-top: 8px;margin-left:-160px;width: 320px;height: 50px;line-height: 50px;overflow: visible;text-align: center;background: #000;color: #fff;z-index: 1;border-radius: 10px;display: none;}
    .reviewSumDiv_hint.active{display: block;}
    .reviewSumDiv_hint:after{content: " ";position: absolute;top: 0px;left: 50%;margin-top: -9px;margin-left: -4.5px;border-bottom: 9px solid #000;border-left: 9px solid transparent;border-right: 9px solid transparent;}
    /*td.remark{position: absolute;min-width: 300px;}*/
    textarea.form-control{margin: 0px !important;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'supportApply-form',
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
				'submit'=>Yii::app()->createUrl('supportApply/index')));
		?>

        <?php if ($model->scenario!='view'&&$model->status_type==1): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Draft'), array(
                'submit'=>Yii::app()->createUrl('supportApply/draft')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Send'), array(
                'submit'=>Yii::app()->createUrl('supportApply/save')));
            ?>
            <?php if ($model->scenario!='new'): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif ?>
        <?php endif ?>

        <?php if ($model->scenario=='edit'&&in_array($model->status_type,array(5,8,11,14))): ?>
            <?php echo TbHtml::button('<span class="fa fa-gavel"></span> '.Yii::t('contract','save score'), array(
                    'submit'=>Yii::app()->createUrl('supportApply/saveDraft'))
            );
            ?>
            <?php echo TbHtml::button('<span class="fa fa-gavel"></span> '.Yii::t('contract','audit score'), array(
                    'id'=>'btnConfirm','data-toggle'=>'modal','data-target'=>'#confirmDialog',)
            );
            ?>
            <div class="reviewSumDiv"><?php echo Yii::t("contract","total score");?>：
                <span id="reviewSum">...</span>
            </div>
        <?php endif; ?>
	</div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario=='edit'&&in_array($model->status_type,array(5,8,11,14))): ?>
                    <?php echo TbHtml::button('<span class="fa fa-plug"></span> '.Yii::t('contract','early end'), array(
                            'id'=>'btnEarly','data-url'=>Yii::app()->createUrl('supportApply/early'),
                            'data-label1'=>Yii::t('contract','early date'),
                            'data-label2'=>Yii::t('contract','early remark'),
                        )
                    );
                    ?>
                <?php endif; ?>
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
			<?php echo $form->hiddenField($model, 'apply_city'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'apply_type'); ?>

            <?php if (in_array($model->status_type,array(8,11))): ?>
            <div class="form-group has-error">
                <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'reject_remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php endif ?>
            <?php if ($model->status_type==4): ?>
            <div class="form-group has-error">
                <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'audit_remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php endif ?>
            <?php if ($model->scenario!='new'): ?>
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
            <?php if ($model->scenario!='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'apply_type',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'apply_type',SupportApplyList::getApplyTypeList(),
                            array('readonly'=>($model->getReadonly()))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::textField('apply_city', CGeneral::getCityName($model->apply_city),
                        array('readonly'=>(true))
                    ); ?>
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
                            array('rows'=>3,'readonly'=>(true),'id'=>"apply_length")
                        ); ?>
                        <span class="input-group-btn" style="width: 80px;">
                        <?php echo $form->dropDownList($model, 'length_type',array(1=>"个月",2=>"天"),
                            array('rows'=>3,'readonly'=>(true),'id'=>"length_type")
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
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'apply_remark',
                        array('rows'=>3,'readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <?php if (!in_array($model->status_type,array(1,2,4))): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'audit_remark',
                            array('rows'=>3,'readonly'=>($model->getReadonly()))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($model->early_remark)): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'early_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'early_remark',
                            array('rows'=>3,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

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


            <?php if (!in_array($model->status_type,array(1,2,12))): ?>
            <legend><?php echo Yii::t("contract","reviewAllot project");?></legend><!--考核项目-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_sum',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'review_sum',
                        array('rows'=>3,'readonly'=>(true),'id'=>"review_sum_val")
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::textField('employee_id', YearDayList::getEmployeeNameToId($model->employee_id),
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php
            $ReviewHandleForm = new ReviewHandleForm();
            echo $ReviewHandleForm->reviewHandleDiv($model,$model->scenario=='edit'&&!in_array($model->status_type,array(5,8,11,14)));
            ?>
            <?php endif ?>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/history',array('tableHtml'=>SupportSearchForm::getHistoryHtml($model->id)));
$this->renderPartial('//site/removedialog');
if(in_array($model->status_type,array(5,8,11,14))){
    $this->renderPartial('//site/earlydialog',array('form'=>$form,'model'=>$model));
}
?>
<?php
$content = "<p>".Yii::t('contract','confirmed and submitted, it cannot be modified after submission?')."</p>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'confirmDialog',
    'header'=>Yii::t('contract','review score'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,
            'submit'=>Yii::app()->createUrl('supportApply/review'))),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>
<?php
$js = Script::genDeleteData(Yii::app()->createUrl('supportApply/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = "
$('#apply_date,#privilege').on('change',function(){
    $.ajax({
        type: 'post',
        url: '".Yii::app()->createUrl('supportApply/ajaxEndDate')."',
        data: {apply_date:$('#apply_date').val(),apply_length:$('#apply_length').val(),length_type:$('#length_type').val(),privilege:$('#privilege').val()},
        dataType: 'json',
        success: function(data){
            if(data.status == 1){
                $('#apply_end_date').val(data.endDate);
            }
        }
    });
});
$('#privilege').on('change',function(){
    if($(this).val() == 1){
        $('#privilege_user').show();
    }else{
        $('#privilege_user').hide();
    }
});
";


if($model->scenario=='edit'&&in_array($model->status_type,array(5,8,11,14))){ //如果需要評分
$js.="
var xmpText = '<textarea rows=\"1\" name=\":name\" class=\"form-control\" placeholder=\"".Yii::t("contract","Scoring remark")."\"></textarea>';
$('.changeSelect').change(function(){
    var num = $(this).val();
    var tr = $(this).parents('tr').eq(0);
    var name = tr.data('name')+'[remark]';
    var html = '';
    if(num!=7&&num!=8){
        html = xmpText.replace(/:name/g,name);
        tr.find('td.remark').html(html);
    }else{
        tr.find('td.remark').html('<button class=\"addRemark btn btn-default\" type=\"button\"><span class=\"glyphicon glyphicon-plus\"></span></button>');
    }
    reviewSum();
});
$('#prompt_button').on('click',function(){
    if($('#prompt').hasClass('active')){
        $('#prompt').removeClass('active');
    }else{
        $('#prompt').addClass('active');
    }
});
$('.remark').delegate('.addRemark','click',function(){
    var tr = $(this).parents('tr').eq(0);
    var name = tr.data('name')+'[remark]';
    var html = xmpText.replace(/:name/g,name);
    tr.find('td.remark').html(html);
});

function reviewSum(){
    var reviewNum = 0;
    var reviewSum = 0;
    $('.reviewTable').each(function(){
        var num_ratio = parseInt($(this).data('ratio'),10);
        var length = $(this).find('.changeSelect').length;
        reviewSum += length*10*num_ratio;
        $(this).find('.changeSelect').each(function(){
            reviewNum+=parseInt($(this).val(),10)*num_ratio;
        });
    });
        var sum = (reviewNum/reviewSum)*100;
        sum = sum.toFixed(2);
        $('#reviewSum').text(sum);
        $('#review_sum_val').val(sum);
}
reviewSum();

$('#btnEarly,#btnRenewal').on('click',function(){
    var url = $(this).data('url');
    var label1 = $(this).data('label1');
    var label2 = $(this).data('label2');
    $('#early_head').text($(this).text());
    $('#early_date_label').text(label1);
    $('#early_remark_label').text(label2);
    $('#earlydialog').modal('show');
    $('#btnEARLYSubmit').data('url',url);
});

$('#btnEARLYSubmit').on('click',function(){
    var url = $(this).data('url');
	jQuery.yii.submitForm(this,url,{});
	$('#earlydialog').modal('hide');
});
";
}
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    if($model->status_type == 1){
        $js = Script::genDatePicker(array(
            'apply_date',
        ));
    }else{
        $js = Script::genDatePicker(array(
            'early_date',
        ));
    }
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

