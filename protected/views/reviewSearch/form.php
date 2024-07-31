<?php
$this->pageTitle=Yii::app()->name . ' - ReviewSearch Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ReviewSearch-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    tbody>tr{position: relative;}
    select[readonly="readonly"]{pointer-events: none;}
    td.remark{ width:1px;display: none;}
    span.text-danger{ word-break: break-all;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','reviewHandle Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('ReviewSearch/index')));
                ?>

                <?php if ($model->status_type != 3 && $model->login_id == $model->employee_id): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                        'submit'=>Yii::app()->createUrl('ReviewSearch/save')));
                    ?>
                <?php endif ?>
            </div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->status_type == 2 || $model->status_type == 3): ?>
                    <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('contract','Down'), array(
                        'submit'=>Yii::app()->createUrl('ReviewSearch/downExcel')));
                    ?>
                <?php endif ?>
                <?php
                $counter = ($model->no_of_attm['review'] > 0) ? ' <span id="docreview" class="label label-info">'.$model->no_of_attm['review'].'</span>' : ' <span id="docreview"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadreview',)
                );
                ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'city'); ?>
            <?php echo $form->hiddenField($model, 'year_type'); ?>

            <?php
            $this->renderPartial('//site/reviewStaff',array(
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'name_list',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::textField("review_type", DeptForm::getReviewType($model->review_type),
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php if ($model->status_type != 3 && $model->login_id == $model->employee_id): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'employee_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'employee_remark',
                            array('readonly'=>(false),'rows'=>3)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <legend><?php echo Yii::t("contract","reviewAllot project");?></legend><!--考核项目-->
            <?php
            $tabs = $model->getTabList();
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
            <legend><?php echo Yii::t("contract","reviewAllot comments");?></legend><!--考核評語-->
            <?php if (!($model->status_type != 3 && $model->login_id == $model->employee_id)): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'employee_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'employee_remark',
                            array('readonly'=>(true),'rows'=>3)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'review_remark',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'strengths',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'strengths',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'target',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'target',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'improve',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'improve',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
        </div>

    </div>
</section>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'REVIEW',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>$model->getReadonly(),
));
?>
<?php

Script::genFileUpload($model,$form->id,'REVIEW');

$js = "
$(function(){
    $('td.remark').each(function(){
        var showObj = $(this).parent('tr').find('td:first');
        var num = $(this).parent('tr').find('td.showNum').length;
        var className = $(this).parent('tr').attr('class');
        showObj.prop('rowspan',2);
        html='<tr class='+className+'><td colspan=\"'+num+'\">'+$(this).html()+'</td></tr>';
        $(this).parent('tr').after(html);
        $(this).remove();
    });
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

