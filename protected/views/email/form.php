<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('email/index'));
}
$this->pageTitle=Yii::app()->name . ' - Email';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'email-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','email hint form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('email/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Draft'), array(
                'submit'=>Yii::app()->createUrl('email/draft')));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'&&$model->status_type!=3): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Send'), array(
                'submit'=>Yii::app()->createUrl('email/save')));
            ?>
        <?php endif ?>

        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif; ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'city_id'); ?>
			<?php echo $form->hiddenField($model, 'staff_id'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'request_dt',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <?php
                            echo TbHtml::dropDownList("aaa",empty($model->request_dt)?0:1,
                                array(Yii::t("contract","message sending time"),Yii::t("contract","custom date")),
                                array('style'=>'width:150px','id'=>'change_dt'));
                            ?>
                        </div>
                        <div class="input-group-addon" style="display: none;">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'request_dt',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),"id"=>"request_dt","style"=>"display:none"));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'subject',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textField($model, 'subject',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'message',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'message',
                        array('readonly'=>($model->scenario=='view'),'rows'=>3)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'city_str',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php
                    echo $form->textArea($model, 'city_str',
                        array('rows'=>4,'cols'=>80,'maxlength'=>1000,'readonly'=>true,)
                    );
                    ?>
                </div>
                <div class="col-sm-2">
                    <?php
                    echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('dialog','Select'),
                        array('name'=>'btnCityStr','id'=>'btnCityStr',)
                    );
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'staff_str',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php
                    echo $form->textArea($model, 'staff_str',
                        array('rows'=>4,'cols'=>80,'maxlength'=>1000,'readonly'=>true,)
                    );
                    ?>
                </div>
                <div class="col-sm-2">
                    <?php
                    echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('contract','Account number'),
                        array('name'=>'btnStaffStr','id'=>'btnStaffStr')
                    );
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php $this->renderPartial('//site/lookup'); ?>
<?php
$js = Script::genDeleteData(Yii::app()->createUrl('email/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnCityStr', 'city', 'city_id', 'city_str',array(),true);

$js .= Script::genLookupButtonEx('btnStaffStr', 'staffEmail', 'staff_id', 'staff_str',array(),true);
Yii::app()->clientScript->registerScript('lookupStaffs',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);

    $js = "
    var date_time ='';
    $('#change_dt').change(function(){
        if($(this).val()==1){
            $('#request_dt').val(date_time);
            $(this).parent('.input-group-btn').nextAll().show();
        }else{
            date_time = $('#request_dt').val();
            if(date_time == ''){
                date_time = new Date();
                date_time = date_time.getFullYear()+'/'+(date_time.getMonth()+1)+'/'+date_time.getDate();
            }
            $('#request_dt').val('');
            $(this).parent('.input-group-btn').nextAll().hide();
        }
    }).trigger('change');
    ";
    Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'request_dt'
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

