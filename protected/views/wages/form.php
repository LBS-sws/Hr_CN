<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('wages/index'));
}
$this->pageTitle=Yii::app()->name . ' - Wages Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'wages-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Wages Type Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('wages/index')));
		?>

        <?php if ($model->scenario!='view' && $model->bool): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('wages/save')));
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
			<?php echo $form->hiddenField($model, 'bool'); ?>

            <?php if (!$model->bool): ?>
            <div class="form-group">
                <div class="col-sm-5 col-sm-offset-2">
                    <label class="text-danger"><?php echo Yii::t('dialog','This record is already in use')."，".Yii::t('contract','No modification is allowed');?></label>
                </div>
            </div>
            <?php endif ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'wages_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-5">
					<?php echo $form->textField($model, 'wages_name',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->dropDownList($model, 'city',CompanyList::getSingleCityToList(),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'wages_list',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
                    <table class="table table-bordered table-striped" id="wagesTable">
                        <thead>
                        <tr>
                            <td><?php echo Yii::t("contract","Wages Type Name");?></td>
                            <td><?php echo Yii::t("contract","Wages Type Index");?></td>
                            <?php if ($model->scenario!='view'&&$model->bool): ?>
                            <td>&nbsp;</td>
                            <?php endif ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (!empty($model->wages_list)){
                                $max = 0;
                                foreach ($model->wages_list as $key => $value){
                                    $num = empty($value['id'])?$key:$value['id'];
                                    if ($max<$num){
                                        $max = $num;
                                    }
                                    echo "<tr data-num='$max'>";
                                    echo "<td><input class='form-control' name='WagesForm[wages_list][$num][type_name]' type='text' value='".$value['type_name']."'></td>";
                                    echo "<td><input class='form-control' name='WagesForm[wages_list][$num][z_index]' type='text' value='".$value['z_index']."'></td>";

                                    if ($model->scenario!='view'&&$model->bool){
                                        if(!empty($value['id'])){
                                            echo "<td><button type='button' class='btn btn-danger delWages'>".Yii::t("misc","Delete")."</button>
                                        <input type='hidden' name='WagesForm[wages_list][$num][id]' value='$num'></td>";
                                        }else{
                                            echo "<td><button type='button' class='btn btn-danger delWages'>".Yii::t("misc","Delete")."</button></td>";
                                        }
                                    }
                                    echo '</tr>';
                                }
                            }
                        ?>
                        </tbody>

                        <?php if ($model->scenario!='view'&&$model->bool): ?>
                            <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td><button type="button" class="btn btn-primary" id="addWages"><?php echo Yii::t("misc","Add")?></button></td>
                            </tr>
                            </tfoot>
                        <?php endif ?>
                    </table>
				</div>
                <div class="col-sm-5 col-sm-offset-2">
                    <small class="text-danger">注：層級越高，排名越靠前</small>
                </div>
			</div>

		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php

$js = '
$("#addWages").on("click",{btnStr:"'.Yii::t("misc","Delete").'"},addWagesType);
$("#wagesTable").delegate(".delWages","click","'.Yii::t("contract","Are you sure you want to delete this data?").'",delWordTable);
if("'.$model->scenario.'"=="view"){
    $("#wagesTable input").prop("disabled",true);
    $("#wagesTable tr>td:last-child").remove();
}
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('wages/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js", CClientScript::POS_END);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

