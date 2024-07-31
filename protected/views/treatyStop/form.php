<?php
$this->pageTitle=Yii::app()->name . ' - TreatyStop Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'TreatyStop-form',
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
                    'submit'=>Yii::app()->createUrl('treatyStop/index')));
                ?>
            </div>
            <?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-backward"></span> '.Yii::t('treaty',"black"), array(
                            'data-toggle'=>'modal','data-target'=>'#blackDialog',)
                    );
                    ?>
                </div>
            <?php endif ?>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'city'); ?>
            <?php echo $form->hiddenField($model, 'state_type'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'treaty_code',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'treaty_code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
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
                    <?php
                    echo TbHtml::textField("city_name",CGeneral::getCityName($model->city),
                        array('readonly'=>(true)));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'apply_date',
                        array('readonly'=>(true),'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'start_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'start_date',
                        array('readonly'=>(true),'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'end_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'end_date',
                        array('readonly'=>(true),'prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'state_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo TbHtml::textField("state_type",TreatyServiceList::getStateStr($model->state_type),
                        array('readonly'=>(true)));
                    ?>
                </div>
                <?php echo $form->labelEx($model,'treaty_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'treaty_num',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <?php if ($model->scenario!='new'): ?>
                <div class="box">
                    <div class="box-body table-responsive">
                        <legend>
                            <?php
                            echo Yii::t("treaty","treaty history")
                            ?>
                        </legend>
                        <?php
                        echo TreatyServiceForm::getHistoryTable($model->id,true)
                        ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</section>

<?php $this->renderPartial('_black'); ?>

<?php
$link = Yii::app()->createUrl('treatyStop/black');
$js="
    $('#btnBlackData').click(function(){
        jQuery.yii.submitForm(this,'$link',{});
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


