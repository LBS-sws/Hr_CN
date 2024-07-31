<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('contract/index'));
}
$this->pageTitle=Yii::app()->name . ' - Contract Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'contract-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Contract Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('contract/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('contract/save')));
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

			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'name',
						array('size'=>50,'maxlength'=>50,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'city',WordForm::getCityListAll(),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'retire',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'retire',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
			</div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'local_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->inlineRadioButtonList($model, 'local_type',ContractList::getWordType(),
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'word_arr',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-6">
                    <table class="table table-bordered table-striped" id="wordArrTable">
                        <thead>
                        <tr>
                            <th width="50%"><?php echo Yii::t("contract","Word Name");?></th>
                            <th width="30%"><?php echo Yii::t("contract","Level");?></th>
                            <th width="20%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $num = 0;
                            foreach ($model->word_arr as $word){
                                $num++;
                                if(!empty($word['id'])){
                                    $num = $word['id'];
                                }
                                //<input type='text' class='form-control' name='ContractForm[word_arr][$num][name]'>
                                echo "<tr datanum ='$num'><td><select class='form-control' name='ContractForm[word_arr][$num][name]'><option value=''></option>";
                                foreach ($model->getWordList() as $key => $list){
                                    if($word["name"] == $key){
                                        echo "<option value='$key' selected>$list</option>";
                                    }else{
                                        echo "<option value='$key'>$list</option>";
                                    }
                                }
                                echo "</select></td><td><input type='number' class='form-control' name='ContractForm[word_arr][$num][index]' value='".$word["index"]."'></td>";
                                if(!empty($word['id'])){
                                    echo "<td><button type='button' class='btn btn-danger delWord'>".Yii::t("misc","Delete")."</button>
                                        <input type='hidden' name='ContractForm[word_arr][$num][id]' value='$num'></td>";
                                }else{
                                    echo "<td><button type='button' class='btn btn-danger delWord'>".Yii::t("misc","Delete")."</button></td>";
                                }
                                echo "</tr>";
                            }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2"></td>
                            <td><button type="button" class="btn btn-primary" id="addWord"><?php echo Yii::t("misc","Add")?></button></td>
                        </tr>
                        </tfoot>
                    </table>
				</div>
			</div>

		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$wordList = json_encode($model->getWordList());
$js = '
$("#addWord").on("click",{btnStr:"'.Yii::t("misc","Delete").'",wordList:'.$wordList.'},addWord);
$("body").delegate(".delWord","click","'.Yii::t("contract","Are you sure you want to delete this data?").'",delWordTable);
';
if($model->scenario=='view'){
    $js.='
        $("#wordArrTable>tfoot,#wordArrTable>tbody>tr>td:last,#wordArrTable>thead>tr>th:last").remove();
        $(".delWord,#addWord").css("pointer-events","none");
    ';
}
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('contract/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wordChange.js", CClientScript::POS_END);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

