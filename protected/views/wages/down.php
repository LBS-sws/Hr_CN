<?php
$this->pageTitle=Yii::app()->name . ' - Wages Down';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'down-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Wages Down'); ?></strong>
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
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('contract','Down'), array(
				'submit'=>Yii::app()->createUrl('wages/downFinish')));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>

			<div class="form-group">
                <?php echo $form->labelEx($model,'checkId',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <table class="table table-bordered table-striped" id="StaffTable">
                        <thead>
                        <tr>
                            <th class='text-center'>
                                <?php echo TbHtml::checkBox("EmployeeDown[checkAll]",($model->checkAll == "All"),array("value"=>"All"));?>
                            </th>
                            <th><?php echo Yii::t('contract','Employee Code');?></th>
                            <th><?php echo Yii::t('contract','Employee Name');?></th>
                            <th><?php echo Yii::t('contract','Sex');?></th>
                            <th><?php echo Yii::t('contract','Employee Phone');?></th>
                            <th><?php echo Yii::t('contract','Position');?></th>
                            <th><?php echo Yii::t('contract','Wage');?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                if (!empty($model->staffList)){
                                    foreach ($model->staffList as $staff){
                                        echo "<tr>";
                                        if(in_array($staff["id"],$model->checkId)){
                                            echo "<td class='text-center'>".TbHtml::checkBox("EmployeeDown[checkId][]",true,array("value"=>$staff["id"]))."</td>";
                                        }else{
                                            echo "<td class='text-center'>".TbHtml::checkBox("EmployeeDown[checkId][]",false,array("value"=>$staff["id"]))."</td>";
                                        }
                                        echo "<td>".$staff["code"]."</td>";
                                        echo "<td>".$staff["name"]."</td>";
                                        echo "<td>".$staff["sex"]."</td>";
                                        echo "<td>".$staff["phone"]."</td>";
                                        echo "<td>".$staff["position"]."</td>";
                                        echo "<td>".$staff["wage"]."</td>";
                                        echo "</tr>";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
			</div>

		</div>
	</div>
</section>
<?php

$js = '
$("#EmployeeDown_checkAll").on("change",function(){
    var bool = $(this).prop("checked");
    $("#StaffTable tbody input").prop("checked",bool);
});
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

/*if ($model->scenario!='view') {
    $js = Script::genDatePickerOnlyMonth(array(
        'EmployeeDown_month',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}*/
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

