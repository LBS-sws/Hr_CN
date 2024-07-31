<?php
if (empty($model->id)){
    $this->redirect(Yii::app()->createUrl('supportSearch/index'));
}
$this->pageTitle=Yii::app()->name . ' - supportSearch';
?>
<style>
    tbody>tr{position: relative;}
    select[readonly="readonly"]{pointer-events: none;}
    tr.text-weight>td{font-weight: bold;}
    td.remark{ width:1px;display: none;}
    span.text-danger{ word-break: break-all;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'supportSearch-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','support form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('supportSearch/index')));
		?>
        <?php if (Yii::app()->user->validRWFunction("AY02")): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif; ?>
	</div>
	<div class="btn-group pull-right" role="group">
        <?php if (Yii::app()->user->validRWFunction("AY02")&&$model->apply_type == 2&&in_array($model->status_type,array(5,6,8,11))): ?>
            <?php echo TbHtml::button('<span class="fa fa-plug"></span> '.Yii::t('contract','early end'), array(
                    'id'=>'btnEarly','data-url'=>Yii::app()->createUrl('supportSearch/early'),
                    'data-label1'=>Yii::t('contract','early date'),
                    'data-label2'=>Yii::t('contract','early remark'),
                )
            );
            ?>
        <?php endif; ?>
        <?php if ($model->status_type == 5 && Yii::app()->user->validRWFunction("AY02")): ?>
            <?php echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('contract','send back'), array(
                'submit'=>Yii::app()->createUrl('supportSearch/back')));
            ?>
        <?php endif ?>
        <?php if ($model->status_type == 6 || $model->status_type == 7): ?>
            <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('contract','Down'), array(
                'submit'=>Yii::app()->createUrl('supportSearch/downExcel')));
            ?>
        <?php endif ?>
		<?php echo TbHtml::button('<span class="fa fa-calendar"></span> '.Yii::t('app','History'), array(
            'data-toggle'=>'modal','data-target'=>'#historydialog'));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>

            <?php if (in_array($model->status_type,array(9,10))): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'early_date'.$model->status_type,array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-3">
                        <?php echo $form->textField($model, 'early_date',
                            array('class'=>'form-control','readonly'=>(true)));
                        ?>
                    </div>
                </div>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'early_remark'.$model->status_type,array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-4">
                        <?php echo $form->textArea($model, 'early_remark',
                            array('rows'=>3,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>


            <div class="form-group">
                <?php echo $form->labelEx($model,'support_code',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'support_code',
                        array('class'=>'form-control','readonly'=>(true)));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'service_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->dropDownList($model, 'service_type',SupportApplyList::getServiceList(),
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'apply_type',SupportApplyList::getApplyTypeList(),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_city',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'city_name',
                        array('class'=>'form-control','readonly'=>(true)));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'apply_date',
                            array('class'=>'form-control','readonly'=>(true),'id'=>"apply_date"));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_end_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
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
                <?php echo $form->labelEx($model,'length_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
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
                <?php echo $form->labelEx($model,'privilege',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->dropDownList($model, 'privilege',SupportApplyList::getPrivilegeList(),
                        array('readonly'=>($model->getReadonly()),'id'=>'privilege')
                    ); ?>
                </div>
            </div>
            <div class="form-group" id="privilege_user" style="<?php if($model->privilege != 1){ echo "display:none;";}?>">
                <?php echo $form->labelEx($model,'privilege_user',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'privilege_user',SupportApplyForm::getPrivilegeUserList($model->apply_city),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_remark',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->textArea($model, 'apply_remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->textArea($model, 'audit_remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php if (!empty($model->early_remark)): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'early_remark',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->textArea($model, 'early_remark',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'employee_name',
                        array('rows'=>3,'readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <?php if ($model->update_type==1): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-6">
                        <?php echo $form->textArea($model, 'update_remark',
                            array('rows'=>3,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <?php
            if(!in_array($model->status_type,array(1,2,12))){ //顯示評核分數
                echo "<legend>".Yii::t("contract","reviewAllot project")."</legend>";
                $supportModel = new SupportAuditForm();
                $tabs = $supportModel->getTabList($model);
                $this->widget('bootstrap.widgets.TbTabs', array(
                    'tabs'=>$tabs,
                ));
            }
            ?>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$this->renderPartial('//site/history',array('tableHtml'=>SupportSearchForm::getHistoryHtml($model->id)));
if(Yii::app()->user->validRWFunction("AY02")&&$model->apply_type == 2&&in_array($model->status_type,array(5,6,8,11))){
    $this->renderPartial('//site/earlydialog',array('form'=>$form,'model'=>$model));
}


$js = "
    $('td.remark').each(function(){
        var showObj = $(this).parent('tr').find('td:first');
        var num = $(this).parent('tr').find('td.showNum').length;
        var className = $(this).parent('tr').attr('class');
        showObj.prop('rowspan',2);
        html='<tr class='+className+'><td colspan=\"'+num+'\">'+$(this).html()+'</td></tr>';
        $(this).parent('tr').after(html);
        $(this).remove();
    });
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
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDatePicker(array(
    'early_date',
));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('supportSearch/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

