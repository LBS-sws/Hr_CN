<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('reward/index'));
}
$this->pageTitle=Yii::app()->name . ' - Reward Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'reward-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .input-group .input-group-addon{background: #eee;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Reward Apply'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('reward/index')));
		?>
<?php if ($model->scenario!='view'): ?>
            <?php if ($model->status==0): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('reward/save')));
                ?>
            <?php endif ?>
            <?php if ($model->status==0||$model->status==3): ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                    'submit'=>Yii::app()->createUrl('reward/audit')));
                ?>
            <?php endif ?>
            <?php if ($model->status==2): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Finish'), array(
                    'submit'=>Yii::app()->createUrl('reward/finish')));
                ?>
            <?php endif ?>
<?php endif ?>
        <?php if ($model->scenario=='edit'&& $model->status == 0): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('reward/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <?php if ($model->status==3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'reject_remark',
                            array('rows'=>3,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
                <legend></legend>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'employee_id',$model->getEmployeeList(),
                        array('disabled'=>($model->yesOrNo()),'class'=>"employee")
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'employee_code',
                        array('size'=>50,'readonly'=>true,'class'=>"employee_code")
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'employee_name',
                        array('size'=>50,'readonly'=>true,'class'=>"employee_name")
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'reward_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'reward_id',RewardConForm::getRewardConList(),
                        array('disabled'=>($model->yesOrNo()),'class'=>"reward_id")
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'reward_money',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'reward_money',
                        array('size'=>50,'readonly'=>true,'class'=>"reward_money")
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'reward_goods',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'reward_goods',
                        array('rows'=>3,'readonly'=>true,'class'=>"reward_goods")
                    ); ?>
                </div>
            </div>


            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->yesOrNo()))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php
$jsonRewardList = $model->getRewardConListJSON();
$js = "
var rewardArr = $jsonRewardList;
    //員工變化
    $('.employee').on('change',function(){
        var str = $(this).find('option:selected').text();
        var arr = str.split(' - ');
        if(arr.length == 2){
            $('.employee_code').val(arr[0]);
            $('.employee_name').val(arr[1]);
        }else{
            $('.employee_code').val('');
            $('.employee_name').val('');
        }
    });
    
    $('.reward_id').on('change',function(){
        var str = $(this).val();
        console.log(typeof rewardArr[str]);
        if(typeof rewardArr[str] == 'undefined'){
            $('.reward_money').val('');
            $('.reward_goods').val('');
        }else{
            console.log(rewardArr[str]);
            $('.reward_money').val(rewardArr[str]['money']);
            $('.reward_goods').val(rewardArr[str]['goods']);
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


