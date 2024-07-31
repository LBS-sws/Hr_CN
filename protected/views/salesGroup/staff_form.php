
<?php
if (empty($model->id)&&$model->scenario!='add'){
    $this->redirect(Yii::app()->createUrl('SalesGroup/index'));
}
$this->pageTitle=Yii::app()->name . ' - SalesGroup';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'SalesGroup-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    *[readonly]{pointer-events: none;}
    .datepicker.datepicker-dropdown{z-index: 9999!important;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo SalesGroupForm::getGroupListToId($model->index)["group_name"]; ?></strong>
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
				'submit'=>Yii::app()->createUrl('SalesGroup/staff',array('index'=>$model->index))));
		?>

        <?php if ($model->scenario!='add'): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('contract','Add Staff Group'), array(
                'submit'=>Yii::app()->createUrl('SalesGroup/staffAdd',array('index'=>$model->index))));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('SalesGroup/saveStaff')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
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
            <?php echo $form->hiddenField($model, 'index'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,"employee_id",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php
                    $list = $model->getSalesList();
                    echo $form->dropDownList($model, "employee_id",$list["list"],
                        array('readonly'=>($model->scenario!='add'),'empty'=>'','options'=>$list["option"],'id'=>"employee_id")
                    );
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,"start_time",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <div class="input-group">
                  <span class="input-group-btn" style="width: 30%">
                    <?php echo TbHtml::dropDownList("DL_end_time", empty($model->start_time)?0:1,array(Yii::t("contract","unlimited"),Yii::t("contract","limited")),
                        array('readonly'=>(false),'class'=>'changeLimit')
                    );
                    ?>
                  </span>
                        <?php echo $form->textField($model, 'start_time',
                            array('readonly'=>($model->scenario=='view'||empty($model->start_time)),'id'=>'start_time',"autocomplete"=>"off")
                        ); ?>
                    </div><!-- /input-group -->
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,"end_time",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <div class="input-group">
                  <span class="input-group-btn" style="width: 30%">
                    <?php echo TbHtml::dropDownList("DL_end_time", empty($model->end_time)?0:1,array(Yii::t("contract","unlimited"),Yii::t("contract","limited")),
                        array('readonly'=>(false),'class'=>'changeLimit')
                    );
                    ?>
                  </span>
                        <?php echo $form->textField($model, 'end_time',
                            array('readonly'=>($model->scenario=='view'||empty($model->end_time)),'id'=>'end_time',"autocomplete"=>"off")
                        ); ?>
                    </div><!-- /input-group -->
                </div>
            </div>

            <div class="form-group">
                <?php
                echo TbHtml::label(Yii::t("contract","Entry Time"),"entry",array('class'=>"col-sm-2 control-label"));
                ?>
                <div class="col-sm-2">
                    <?php
                    echo TbHtml::textField("entry","",array('readonly'=>(true),'id'=>"entry"));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php
                echo TbHtml::label(Yii::t("contract","Leave Date"),"leave",array('class'=>"col-sm-2 control-label"));
                ?>
                <div class="col-sm-2">
                    <?php
                    echo TbHtml::textField("leave","",array('readonly'=>(true),'id'=>"leave"));
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$js = "
$('#employee_id').change(function(){
    var leave_time = $(this).find('option:selected').data('leave');
    var entry_time = $(this).find('option:selected').data('entry');
    $('#leave').val(leave_time);
    $('#entry').val(entry_time);
});
$('#employee_id').trigger('change');

    $('.changeLimit').change(function(){
        if($(this).val() == 1){
            $(this).parent().next().attr('readonly',false);
        }else{
            $(this).parent().next().val('').attr('readonly',true);
        }
    });
    
    $('#start_time,#end_time').datepicker({autoclose: true,language: 'zh_cn', format: 'yyyy-mm', minViewMode: 1});;
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('SalesGroup/delStaff',array("index"=>$model->id)));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

