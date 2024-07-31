<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('oldContract/index'));
}
$this->pageTitle=Yii::app()->name . ' - oldContract Form';
?>

<style>
    input[readonly="readonly"]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'oldContract-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Review of old contracts'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('oldContract/index')));
                ?>
                <?php if ($model->scenario=='edit'): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                    ?>
                <?php endif; ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-refresh"></span> '.Yii::t('contract','examine'), array(
                    'submit'=>Yii::app()->createUrl('oldContract/checked',array("index"=>$model->id))));
                ?>
            </div>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-body" style="position: relative">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'old_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'old_type',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <legend><?php echo Yii::t("contract","");?></legend>
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <p class="form-control-static">有待合同寄出：要求地區人事寄出合同（地區同事已了解香港沒有該同事的合同）。如果已有合同，請點擊“撤回”按鈕</p>
                    <p class="form-control-static">合同已簽收：默認香港已有該同事的合同。如果沒有該同事的合同，請點擊旁邊的“未簽收”按鈕</p>
                    <p class="form-control-static">合同已寄出：地區人事已將合同寄往香港，您可以在本系統的“審核”-> “合同簽收”進行簽收</p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th><?php echo Yii::t("contract","contract type");?></th>
                            <th><?php echo Yii::t("contract","Employee Code");?></th>
                            <th><?php echo Yii::t("contract","Employee Name");?></th>
                            <th><?php echo Yii::t("contract","ID Card");?></th>
                            <th><?php echo Yii::t("contract","Department");?></th>
                            <th><?php echo Yii::t("contract","Position");?></th>
                            <th><?php echo Yii::t("contract","Company Name");?></th>
                            <th><?php echo Yii::t("contract","contract deadline");?></th>
                            <th><?php echo Yii::t("contract","Contract Start Time");?></th>
                            <th><?php echo Yii::t("contract","Contract End Time");?></th>
                            <th><?php echo Yii::t("contract","sign type");?></th>
                            <th width="1%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tableModel = new OldContractForm();
                        echo $tableModel->printTableBody($model->id,false,0,1);
                        ?>
                        </tbody>
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
$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('oldContract/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);

?>

<?php $this->endWidget(); ?>

</div><!-- form -->

