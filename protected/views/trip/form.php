<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('trip/index'));
}
$this->pageTitle=Yii::app()->name . ' - Trip Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'trip-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    *[readonly]{ pointer-events: none;}
    .input-group-btn>.end_time_lg,.input-group-btn>.start_time_lg{ width: 80px;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('fete','trip form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('trip/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='new'||$model->status == 0||$model->status == 3): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('trip/save')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','For Audit'), array(
                    'submit'=>Yii::app()->createUrl('trip/audit')));
                ?>
            <?php endif ?>
            <?php if ($model->scenario=='edit'&&($model->status == 0||$model->status == 3)): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
            <?php if ($model->status == 2): ?>
                <?php echo TbHtml::button('<span class="fa fa-laptop"></span> '.Yii::t('fete','trip result'), array(
                        'name'=>'btnResult','data-toggle'=>'modal','data-target'=>'#resultDialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif; ?>

	</div>
            <div class="btn-group pull-right" role="group">
                <?php if (Yii::app()->user->validFunction('ZG10')&&in_array($model->status,array(2,4,5,6))): ?>
                    <?php echo TbHtml::button('<span class="fa fa-mail-reply"></span> '.Yii::t('contract','send back'), array(
                        'submit'=>Yii::app()->createUrl('trip/reply')));
                    ?>
                <?php endif; ?>
                <?php if (Yii::app()->user->validFunction('ZR05')&&$model->status == 4): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','cancel'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#jectdialog',)
                    );
                    ?>
                <?php endif; ?>
                <?php
                $counter = ($model->no_of_attm['trip'] > 0) ? ' <span id="doctrip" class="label label-info">'.$model->no_of_attm['trip'].'</span>' : ' <span id="doctrip"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadtrip',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
			<?php echo $form->hiddenField($model, 'z_index'); ?>
            <?php echo CHtml::hiddenField('dtltemplate'); ?>
            <?php echo CHtml::hiddenField('dtltemplateTwo'); ?>


            <?php if ($model->status==4||($model->status==5&&!empty($model->result_id))): ?>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'result_text',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-6">
                        <?php echo $form->textArea($model, 'result_text',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
                <legend>&nbsp;</legend>
            <?php endif; ?>

            <?php if ($model->status==3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_cause',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-6">
                        <?php echo $form->textArea($model, 'reject_cause',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
                <legend>&nbsp;</legend>
            <?php endif; ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textField($model, 'employee_name',
						array('readonly'=>(true))
					); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'addTime',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-8">
                    <?php $this->widget('ext.layout.TableView2Widget', array(
                        'model'=>$model,
                        'tableClass'=>'table-bordered addTime',
                        'attribute'=>'addTime',
                        'viewhdr'=>'//trip/_formhdr',
                        'viewdtl'=>'//trip/_formdtl',
                        'gridsize'=>'24',
                        'height'=>'200',
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'trip_address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textField($model, 'trip_address',
                        array('readonly'=>($model->ready()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textField($model, 'company_name',
                        array('readonly'=>($model->ready()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'addMoney',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-8">
                    <?php $this->widget('ext.layout.TableView2Widget', array(
                        'model'=>$model,
                        'tableClass'=>'table-bordered addMoney',
                        'attribute'=>'addMoney',
                        'viewhdr'=>'//trip/_moneyFormhdr',
                        'viewdtl'=>'//trip/_moneyFormdtl',
                        'gridsize'=>'24',
                        'height'=>'200',
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'trip_cost',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->numberField($model, 'trip_cost',
                        array('readonly'=>(true),'id'=>'trip_cost')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'trip_cause',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'trip_cause',
                        array('readonly'=>($model->ready()),'rows'=>4)
                    ); ?>
                </div>
            </div>

            <?php
            $this->renderPartial('//trip/auditInfo',array(
                'model'=>$model,
                'form'=>$form,
            ));
            ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'TRIP',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>(false),
    'delBtn'=>($model->scenario=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZA10')),
));
//$model->getInputBool()
?>
<?php
if ($model->status == 2){
    $this->renderPartial('//trip/resultDialog',array('form'=>$form,'model'=>$model));
}
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
Script::genFileUpload($model,$form->id,'TRIP');

$js = "
$('table').on('change','[id^=\"TripForm\"]',function() {
	var n=$(this).attr('id').split('_');
	$('#TripForm_'+n[1]+'_'+n[2]+'_uflag').val('Y');
	changeTripCost();
});
function changeTripCost(){
    var money =0;
    var sumMoney =0;
    $('.addMoney>tbody>tr').not('.removeTr').each(function(){
        money = $(this).find('input.trip_money').val();
        money = money==''?0:parseFloat(money);
        sumMoney+=money;
    });
    $('#trip_cost').val(sumMoney);
}
";
Yii::app()->clientScript->registerScript('setFlag',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = "
$('table').on('click','.btnDelRow', function() {
	$(this).closest('tr').find('[id*=\"_uflag\"]').val('D');
	$(this).closest('tr').addClass('removeTr').hide();
	changeTripCost();
});
	";
    Yii::app()->clientScript->registerScript('removeRow',$js,CClientScript::POS_READY);

    $dateLang = Yii::app()->language;
    $js = "
$(document).ready(function(){
    $('.tblDetail').each(function(){
        var ct = $(this).find('tr').eq(1).html();
        if($(this).hasClass('addTime')){
            $('#dtltemplate').attr('value',ct);
        }else{
            $('#dtltemplateTwo').attr('value',ct);
        }
    });
});

$('.btnAddRow').on('click',function() {
	var tableTop = $(this).parents('.tblDetail').eq(0);
	var r = tableTop.find('tr').length;
	if (r>0) {
		var nid = '';
        if(tableTop.hasClass('addTime')){
		    var ct = $('#dtltemplate').val();
        }else{
		    var ct = $('#dtltemplateTwo').val();
        }
		tableTop.find('tbody:last').append('<tr>'+ct+'</tr>');
		tableTop.find('tr').eq(-1).find('[id*=\"TripForm_\"]').each(function(index) {
			var id = $(this).attr('id');
			var name = $(this).attr('name');

			var oi = 0;
			var ni = r;
			id = id.replace('_'+oi.toString()+'_', '_'+ni.toString()+'_');
			$(this).attr('id',id);
			name = name.replace('['+oi.toString()+']', '['+ni.toString()+']');
			$(this).attr('name',name);
			if (id.indexOf('_id') != -1) $(this).attr('value','0');
			if (id.indexOf('_trip_id') != -1) $(this).attr('value','0');
			if (id.indexOf('_start_time_lg') != -1) $(this).val('AM');
			if (id.indexOf('_end_time_lg') != -1) $(this).val('PM');
			if (id.indexOf('_money_set_id') != -1) $(this).val('');
			if (id.indexOf('_trip_money') != -1) $(this).val('');
			if (name.indexOf('[start_time]') != -1) {
				$(this).attr('value','');
				$(this).datepicker({autoclose: true, format: 'yyyy/mm/dd',language: '{$dateLang}'});
			}
			if (name.indexOf('[end_time]') != -1) {
				$(this).attr('value','');
				$(this).datepicker({autoclose: true, format: 'yyyy/mm/dd',language: '{$dateLang}'});
			}
		});
		if (nid != '') {
			var topos = $('#'+nid).position().top;
			$('#tbl_detail').scrollTop(topos);
		}
	}
});
	";
    Yii::app()->clientScript->registerScript('addRow',$js,CClientScript::POS_READY);

    $js = Script::genDatePicker(array(
        'aa,.end_time,.start_time'
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('trip/delete'));
$this->renderPartial('//site/ject',array('form'=>$form,'model'=>$model,'rejectName'=>'reject_cause','header_name'=>Yii::t('dialog','Are you sure to cancel?'),'submit'=>Yii::app()->createUrl('trip/cancel')));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

