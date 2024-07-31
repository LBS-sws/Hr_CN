<?php
$this->pageTitle=Yii::app()->name . ' - heartLetterAudit Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'heartLetterAudit-form',
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
				'submit'=>Yii::app()->createUrl('heartLetterAudit/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->state == 1||$model->state == 3): ?>
                <?php echo TbHtml::button('<span class="fa fa-bug"></span> '.Yii::t('contract','reply'), array(
                        'name'=>'btnReply','id'=>'btnReply','data-toggle'=>'modal','data-target'=>'#replyDialog',)
                );
                //Yii::app()->createUrl('heartLetterAudit/audit')
                ?>
            <?php endif; ?>
            <?php if ($model->state == 1): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','To be processed'), array(
                        'name'=>'btnWait','id'=>'btnWait','data-toggle'=>'modal','data-target'=>'#waitDialog',)
                );
                ?>
            <?php endif; ?>
            <?php if ($model->state == 1||$model->state == 3): ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','ready end'), array(
                    'submit'=>Yii::app()->createUrl('heartLetterAudit/end')));
                ?>
            <?php endif; ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
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
                                    Yii::app()->createUrl('heartLetterAudit/view',array('index'=>$model->letter_id)),
                                    array("target"=>"_blank"));
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'lcd',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'employee_code',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'employee_code',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'employee_name',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'letter_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'letter_type',HeartLetterForm::getLetterTypeList(),
                        array('disabled'=>(!in_array($model->state,array(1,3))),"id"=>"letter_type")
                    ); ?>
                </div>
            </div>

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

            <?php if ($model->state==3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'wait_date',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3 error">
                        <?php echo $form->textField($model, 'wait_date',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
		</div>
	</div>
</section>
<?php if (in_array($model->state,array(1,3))): ?>
<!-- Modal -->
<div class="modal fade" id="replyDialog" tabindex="-1" role="dialog" aria-labelledby="myModalDialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo Yii::t('contract','reply');?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-lg-11 col-lg-offset-1">
                        <ul class="list-unstyled">
                            <li>评分规则：</li>
                            <li>1星：一般的建议，出于鼓励视情况给予1星或没星</li>
                            <li>2星：提出不错的建议但暂不适合的给2星</li>
                            <li>3星：系统及完善类的建议给3星</li>
                            <li>4星：视为地区采纳（同事可申请“心意信封”慈善分，1分）</li>
                            <li>5星：视为集团采纳（同事可申请“心意信封”慈善分，3分）</li>
                        </ul>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'letter_num',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-9">
                        <?php echo $form->hiddenField($model, 'letter_num',array("id"=>"letter_num")); ?>

                        <p class="form-control-static">
                            <?php echo HeartLetterAuditForm::getLetterNumToIcon($model->letter_num); ?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'letter_reply',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-9">
                        <?php echo $form->textArea($model, 'letter_reply',
                            array('readonly'=>(false),"rows"=>4)
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php
                $submit = Yii::app()->createUrl('heartLetterAudit/audit');
                echo TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
                echo TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => $submit));
                ?>
            </div>
            <script type="text/javascript">
                $(function ($) {
                    $(".changeIcon").click(function () {
                        if($(this).hasClass("fa-star")){
                            $(this).removeClass("fa-star").nextAll(".changeIcon").removeClass("fa-star");
                            $(this).addClass("fa-star-o").nextAll(".changeIcon").addClass("fa-star-o");
                        }else{
                            $(this).removeClass("fa-star-o").prevAll(".changeIcon").removeClass("fa-star-o");
                            $(this).addClass("fa-star").prevAll(".changeIcon").addClass("fa-star");
                        }
                        var num = $(this).parent().children(".fa-star").length;
                        $("#letter_num").val(num);
                        $("#num_icon").text(num+"分");
                    })
                })
            </script>
        </div>
    </div>
</div>
<?php endif; ?>
<!-- Modal -->
<div class="modal fade" id="waitDialog" tabindex="-1" role="dialog" aria-labelledby="myModalDialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo Yii::t('contract','To be processed');?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'wait_date',array('class'=>"col-lg-3 control-label")); ?>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>

                            <?php echo $form->textField($model, 'wait_date',
                                array('class'=>'form-control','autocomplete'=>'off','id'=>"wait_date",'readonly'=>false));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php
                $submit = Yii::app()->createUrl('heartLetterAudit/save');
                echo TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
                echo TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => $submit));
                ?>
            </div>
        </div>
    </div>
</div>

<?php $this->renderPartial('//site/fileupload',array(
    'model'=>$model,
    'form'=>$form,
    'doctype'=>'LETTER',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->getInputBool())
));
//$model->getInputBool()
?>
<?php
Script::genFileUpload($model,$form->id,'LETTER');

$js = "

";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDatePicker(array(
    'wait_date'
));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

