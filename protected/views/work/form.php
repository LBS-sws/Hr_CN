<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('work/index'));
}
$this->pageTitle=Yii::app()->name . ' - Work Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'work-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    *[readonly]{pointer-events: none;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('fete','Overtime work Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('work/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='new'||$model->status == 0||$model->status == 3): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('work/save')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','For Audit'), array(
                    'submit'=>Yii::app()->createUrl('work/audit')));
                ?>
            <?php endif ?>
            <?php if ($model->scenario=='edit'&&($model->status == 0||$model->status == 3)): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
            <?php if (Yii::app()->user->validFunction('ZR05')&&$model->status == 4): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','cancel'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#canceldialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif; ?>
	</div>
            <?php if ($model->status==4): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','Download'), array(
                        'submit'=>Yii::app()->createUrl('work/PdfDownload',array("index"=>$model->id))));
                    ?>
                </div>
            <?php endif; ?>
            <?php if (Yii::app()->user->validFunction('ZR13')&&$model->status==1): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('contract','send back'), array(
                        'submit'=>Yii::app()->createUrl('work/back',array("index"=>$model->id))));
                    ?>
                </div>
            <?php endif; ?>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['workem'] > 0) ? ' <span id="docworkem" class="label label-info">'.$model->no_of_attm['workem'].'</span>' : ' <span id="docworkem"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadworkem',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>

            <?php
            $this->renderPartial('//site/workform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <?php if ($model->scenario != 'new' && $model->status != 3 && $model->status != 4): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'state',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textField($model, 'state',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($model->status != 0 && $model->status != 3 && Yii::app()->user->validFunction('ZR07') && $model->scenario!='new'): ?>
                <legend>&nbsp;</legend>
                <?php if ($model->work_cost == "0.00"): ?>
                    <div class="form-group text-danger">
                        <label class="col-sm-4 col-sm-offset-2 form-control-static">
                            不支付加班工資
                        </label>
                    </div>
                    <?php else:?>
                    <div class="form-group text-danger">
                        <label class="col-sm-2 control-label">
                            加班工资计算公式
                        </label>
                        <div class="form-control-static col-sm-10">
                            1、工作日加班费= 员工合同约定月工资÷(21.75×8)×150%×加班小时数<br>
                            2、周末加班费= 员工合同约定月工资÷(21.75×8)×200%×加班小时数<br>
                            3、法定节假日加班费= 员工合同约定月工资÷(21.75×8)×工资倍率×加班小时数
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="form-control-static col-sm-10">
                            <?php echo $model->wage;?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'work_cost',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-3">
                            <?php echo $form->textField($model, 'work_cost',
                                array('readonly'=>(true)));
                            ?>
                        </div>
                        <div class="form-control-static col-sm-7">
                            <?php
                            echo $model->wage."÷";
                            echo "(21.75×8)×".$model->getMuplite()."×".$model->log_time;
                            echo " = ".$model->work_cost;
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($model->z_index != 3 && $model->z_index != 0): ?>
                <legend>&nbsp;</legend>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'audit_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'audit_remark',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'WORKEM',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>(false),
    'delBtn'=>($model->scenario=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR05')),
));
//$model->getInputBool()
?>
<div id="fete_error" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">验证信息</h4></div><div class="modal-body"><p></p>
                <div class="errorSummary">
                    <p>请更正下列输入错误:</p>
                    <ul>
                    </ul>
                </div>
                <p></p>
            </div>
            <div class="modal-footer"><button data-dismiss="modal" class="btn btn-primary" name="yt4" type="button">确定</button></div>
        </div>
    </div>
</div>
<?php
$this->renderPartial('//site/removedialog');
$this->renderPartial('//site/canceldialog');
?>
<?php
Script::genFileUpload($model,$form->id,'WORKEM');

$js = "
$('#work_time_div').delegate('#start_time','change',function(){
    if($('#end_time').val()==''){
        $('#end_time').val($(this).val());
        $('#end_time').trigger('change');
    }
});
$('#work_time_div').delegate('#start_time,#end_time,#hours,#hours_end','change',function(){
    var start_day = $('#start_time').val();
    var end_day = $('#end_time').val();
    var start_hour = $('#hours').val();
    var end_hour = $('#hours_end').val();
    if(start_day!=''&&end_day!=''){
        var d1 = new Date(start_day);
        var d2 = new Date(end_day);
        d1 = d1.getTime();
        d2 = d2.getTime();
        if(d1<=d2){
            var time = d2-d1;
            var hours=time/(3600*1000); 
            end_hour = end_hour=='00:00'?'24:00':end_hour;
            var num = parseInt(end_hour,10)-parseInt(start_hour,10);
            hours+=num;
            if(hours>0){
                $('#log_time').val(hours);
            }else{
                $('#log_time').val('');
            }
        }else{
            $('#log_time').val('');
        }
    }else{
        $('#log_time').val('');
    }
});
//取消事件
$('#btnCancelData').on('click',function() {
	$('#canceldialog').modal('hide');
	var elm=$('#btnCancelData');
	jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('work/cancel')."',{});
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('work/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

