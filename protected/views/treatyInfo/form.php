<?php
$this->pageTitle=Yii::app()->name . ' - TreatyInfo Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'TreatyInfo-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('treaty','Treaty Hint'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('treatyService/edit',array("index"=>$model->treaty_id))));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('treatyInfo/save')));
			?>
<?php endif ?>
<?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'treaty_id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'treaty_name',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->textField($model, 'treaty_name',
					array('readonly'=>(true))
				); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'city_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php if ($model->scenario!='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'contract_code',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-2">
                        <?php echo $form->textField($model, 'contract_code',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'start_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'start_date',
                        array('readonly'=>($model->scenario=='view'),'id'=>'start_date',"autocomplete"=>"off",'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'month_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->numberField($model, 'month_num',
                        array('readonly'=>($model->scenario=='view'),'id'=>'month_num',"autocomplete"=>"off",'append'=>'月','min'=>0)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'end_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'end_date',
                        array('readonly'=>($model->scenario=='view'),'id'=>'end_date',"autocomplete"=>"off",'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'email_hint',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <?php
                            echo $form->dropDownList($model, 'email_hint',array(0=>Yii::t("treaty","no"),1=>Yii::t("treaty","yes")),
                                array('readonly'=>($model->scenario=='view'),'style'=>'width:80px','id'=>'email_hint')
                            ); ?>
                        </div>
                        <div class="input-group-addon" style="display: none;">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php
                        $display=empty($model->email_hint)?"none":"block";
                        echo $form->textField($model, 'email_date',
                            array('class'=>'form-control pull-right',"autocomplete"=>"off",'readonly'=>($model->scenario=='view'),"id"=>"email_date","style"=>"display:{$display}"));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->textArea($model, 'remark',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
            </div>

		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
//.trigger('change')
$js ="
    $('#start_date,#month_num').change(function(){
        var startDate = $('#start_date').val();
        var monthNum = $('#month_num').val();
        if(startDate!=''&&monthNum!=''){
            $('#end_date').datepicker('setDate',addMonth(startDate,monthNum));
            $('#end_date').trigger('change');
        }
    });
    
    $('#email_hint').change(function(){
        if($(this).val()==1){
            $('#end_date').trigger('change');
            $(this).parent('.input-group-btn').nextAll().show();
        }else{
            $(this).parent('.input-group-btn').nextAll().hide();
        }
    });
    
    $('#end_date').change(function(){
        var end_date = $('#end_date').val();
        if(end_date!=''){
            $('#email_date').datepicker('setDate',addMonth(end_date,-1));
        }
    });
    
    function addMonth(startDate,monthNum){
        startDate = startDate.replace(/-/g,\"/\");
        var dateArr = startDate.split('/');
        var year = parseInt(dateArr[0],10);
        var month = parseInt(dateArr[1],10);
        var day = parseInt(dateArr[2],10);
        monthNum = parseInt(monthNum,10);
        month+=monthNum;
        var yearAddNum = Math.floor(month/12);
        month = month%12;
        year+=yearAddNum;
        if(month==0){
            year--;
            month=12;
        }
        month=month<10?'0'+month:month;
        day=day<10?'0'+day:day;
        return year+'/'+month+'/'+day;
    }
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'start_date',
        'end_date',
        'email_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('treatyInfo/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


