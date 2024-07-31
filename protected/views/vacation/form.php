<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('vacation/index'));
}
$this->pageTitle=Yii::app()->name . ' - Fete Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'vacation-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('fete','Vacation Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('vacation/index')));
		?>
        <?php if ($model->scenario == "edit"): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                'submit'=>Yii::app()->createUrl('vacation/new')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Copy'), array(
                'submit'=>Yii::app()->createUrl('vacation/new',array("index"=>$model->id))));
            ?>
        <?php endif ?>
        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('vacation/save')));
            ?>
            <?php if ($model->scenario=='edit'&&!$model->isReadonly()): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <!--
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2 text-danger">
                    扣减工资= 员工合同约定月工资÷出勤天数×請假天数×倍率
                </div>
            </div>
            -->
			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textField($model, 'name',
						array('readonly'=>($model->isReadonly()))
					); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'vaca_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'vaca_type',VacationForm::getVacaTypeList(),
                        array('disabled'=>($model->isReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'city',CompanyList::getSingleCityToList(),
                        array('disabled'=>($model->isReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'log_bool',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'log_bool',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('disabled'=>($model->isReadonly()),"class"=>"changeBool")
                    ); ?>
                </div>
            </div>
            <div class='form-group'>
                <div class='col-sm-4 col-sm-offset-2'>
                    <?php
                    $model->parentTable();
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'ass_bool',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'ass_bool',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('disabled'=>($model->isReadonly()),"class"=>"changeBool")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'ass_id_name',array('class'=>"col-sm-2 control-label")); ?>
                <?php echo $form->hiddenField($model, 'ass_id',array("id"=>"ass_id")); ?>
                <div class="col-sm-5">
                    <div class="input-group">
                        <?php echo $form->textField($model, 'ass_id_name',
                            array('readonly'=>(true),'id'=>'ass_id_name')
                        ); ?>
                        <span class="input-group-btn">
                <?php echo TbHtml::button(Yii::t('dialog','Select'), array(
                        'class'=>'btn btn-primary','id'=>'btnSelect','disabled'=>$model->isReadonly(),'data-toggle'=>'modal','data-target'=>'#lookupdialog',)
                );
                ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'sub_bool',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'sub_bool',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('disabled'=>($model->isReadonly()),"class"=>"changeBool")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'sub_multiple',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <?php echo $form->textField($model, 'sub_multiple',
                            array('readonly'=>($model->isReadonly()))
                        ); ?>
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'only',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->inlineRadioButtonList($model, 'only',array("local"=>Yii::t("fete","local"),"default"=>Yii::t("fete","default")),
                        array('disabled'=>($model->isReadonly()))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'z_display',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->inlineRadioButtonList($model, 'z_display',array(0=>Yii::t("contract","none"),1=>Yii::t("contract","show")),
                        array('disabled'=>($model->isReadonly()))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>
<xmp hidden id="readyOne">
    <?php echo $model->getTrHtml(":num");?>
</xmp>

<?php
$this->renderPartial('//site/removedialog');

$list = CHtml::listBox('lstlookup', '', $model->getAssociatedList(), array('class'=>'form-control','size'=>10,"multiple"=>"multiple"));
$mesg = TbHtml::label(Yii::t('dialog','Hold down <kbd>Ctrl</kbd> button to select multiple options'), false);
$content = "
<div class='row'>
	<div class='col-sm -11' id='lookup-list'>
			$list
	</div>
</div>
<div class='row'>
	<div class='col-sm-11 small' id='lookup-label'>
			$mesg
	</div>
</div>
";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'lookupdialog',
    'header'=>Yii::t('contract','associated config'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','Select'), array('id'=>'btnLookupSelect','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnLookupCancel','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>
<?php

$js = "
    var rowHtml = $('#readyOne').html();
    $('#readyOne').remove();
$('.changeBool').on('change',function(){
    if($(this).val() == 0){
        $(this).parents('.form-group').next('.form-group').hide();
    }else{
        $(this).parents('.form-group').next('.form-group').show();
    }
}).trigger('change');

$('#ruleTable').delegate('.delRule','click',function(){
    $(this).parents('tr:first').remove();
});

    $('#addRule').on('click',function(){
        var num = $('#ruleTable>tbody').data('num');
        num = parseInt(num,10);
        num++;
        $('#ruleTable>tbody').data('num',num)
        var newHtml = rowHtml.replace(/:num/g,num);
        $('#otherTr').before(newHtml);
    });

$('#btnLookupSelect').on('click',function(){
    var list = $('#lstlookup').val();
    var nameList=[];
    if(list==''){
        $('#ass_id').val('');
        $('#ass_id_name').val('');
    }else{
        $('#lstlookup').find('option:selected').each(function(){
            nameList.push($(this).text());
        });
        list = list.join(',');
        nameList = nameList.join(',');
        $('#ass_id').val(list);
        $('#ass_id_name').val(nameList);
    }
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('vacation/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

