<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('downForm/index'));
}
$this->pageTitle=Yii::app()->name . ' - downForm Form';
?>

<style>
    td,td>p{word-break:break-all;word-wrap:break-word;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'downForm-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
        <strong><?php echo Yii::t('app','Common forms download'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('downForm/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('downForm/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>

	<div class="btn-group pull-right" role="group">
        <?php if ($model->scenario!='new'): ?>
            <?php echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('contract','Down'), array(
                'submit'=>Yii::app()->createUrl('downForm/downfile?index='.$model->id)));
            ?>
        <?php endif; ?>
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

            <?php if ($model->scenario=='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'file',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3 word">
                        <?php echo $form->fileField($model, 'file',
                            array('disabled'=>($model->scenario=='view'),'class'=>'form-control')
                        ); ?>
                    </div>
                </div>
            <?php elseif($model->scenario=='edit'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'file',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3 word">
                        <?php echo $form->hiddenField($model, 'docx_url'); ?>
                        <?php echo TbHtml::button('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('contract','update'), array("id"=>"updateWord"));
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>5,'readonly'=>($model->scenario=='view'),'class'=>'form-control')
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php

$this->renderPartial('//site/removedialog');
?>
<?php
$wordFileInput="<div class='col-sm-3 word'><input type='hidden' value='' name='DownFormForm[file]'><input class='form-control' name='DownFormForm[file]' type='file'></div>";
$js = '
    $("#updateWord").on("click",function(){
        var $div = $(this).parents("div.word");
        $div.after("'.$wordFileInput.'").remove();
    });
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('downForm/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

