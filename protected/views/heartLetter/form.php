<?php
$this->pageTitle=Yii::app()->name . ' - heartLetter Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'heartLetter-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    .changeIcon{ margin-left: 5px;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Heart letter form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('heartLetter/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='new'||$model->state == 0): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('heartLetter/save')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','click send'), array(
                    'submit'=>Yii::app()->createUrl('heartLetter/audit')));
                ?>
            <?php endif ?>
            <?php if ($model->scenario=='edit'&&$model->state == 0): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
            <?php if ($model->scenario=='edit'&&$model->state == 4): ?>
                <?php
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('heartLetter/new'),
                ));
                ?>
            <?php endif; ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario=='edit'&&$model->state == 4): ?>
                    <?php
                    echo TbHtml::button('<span class="fa fa-indent"></span> '.Yii::t('contract','Go on'), array(
                        'submit'=>Yii::app()->createUrl('heartLetter/new',array('letter_id'=>$model->id)),
                    ));
                    ?>
                <?php endif; ?>
                <?php
                $counter = ($model->no_of_attm['letter'] > 0) ? ' <span id="docletter" class="label label-info">'.$model->no_of_attm['letter'].'</span>' : ' <span id="docletter"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadletter',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'state'); ?>
			<?php echo $form->hiddenField($model, 'letter_id'); ?>


            <?php if ($model->scenario!='new'): ?>
                <?php if (!empty($model->letter_id)): ?>
                <div class="form-group">
                    <div class="col-sm-6 col-sm-offset-2">
                        <p class="form-control-static">
                            <?php
                            echo TbHtml::link(Yii::t("contract","Relevant letter"),
                                Yii::app()->createUrl('heartLetter/view',array('index'=>$model->letter_id)),
                                array("target"=>"_blank"));
                            ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textField($model, 'lcd',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!in_array($model->state,array(1,0))): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'letter_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'letter_type',HeartLetterForm::getLetterTypeList(),
                        array('disabled'=>($model->getInputBool()))
                    ); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'letter_title',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textField($model, 'letter_title',
                        array('readonly'=>$model->getInputBool())
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'letter_body',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 'letter_body',
                        array('readonly'=>($model->getInputBool()),"rows"=>empty($model->letter_id)?4:8)
                    ); ?>
                </div>
            </div>

            <?php if ($model->state==4&&!empty($model->letter_reply)): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'letter_num',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <p class="form-control-static">
                            <?php echo HeartLetterAuditForm::getLetterNumToIcon($model->letter_num); ?>
                        </p>
                    </div>
                </div>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'letter_reply',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6 error">
                        <?php echo $form->textArea($model, 'letter_reply',
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
    'doctype'=>'LETTER',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->getInputBool())
));
//$model->getInputBool()
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
Script::genFileUpload($model,$form->id,'LETTER');

$js = "


";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('heartLetter/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

