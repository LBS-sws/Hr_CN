<?php
$this->pageTitle=Yii::app()->name . ' - AuditWages Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'autitWages-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong>
            <?php
            echo Yii::t('app','Wages Audit');
            ?>
        </strong>
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
                    'submit'=>Yii::app()->createUrl('auditWages/index')));
                ?>

                <?php if ($model->scenario!='view' && $model->wages_status == 1): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Audit'), array(
                        'submit'=>Yii::app()->createUrl('auditWages/audit')));
                    ?>
                <?php endif ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='view' && $model->wages_status == 1): ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Rejected'), array(
                        'submit'=>Yii::app()->createUrl('auditWages/reject')));
                    ?>
                <?php endif ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo $form->hiddenField($model, 'wages_status'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'wages_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'wages_date',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <td><?php echo Yii::t("contract","Employee Code");?></td>
                            <td><?php echo Yii::t("contract","Employee Name");?></td>
                            <td><?php echo Yii::t("contract","Department");?></td>
                            <td><?php echo Yii::t("contract","City");?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo $model->staff_list["code"];?></td>
                            <td><?php echo $model->staff_list["name"];?></td>
                            <td><?php echo $model->staff_list["position"];?></td>
                            <td><?php echo CGeneral::getCityName($model->staff_list["city"]);?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'sum',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'sum',
                        array('readonly'=>true,'min'=>0)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'wages_arr',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <td><?php echo Yii::t("contract","Wage Name");?></td>
                            <td><?php echo Yii::t("contract","Wage Number");?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!empty($model->wages_arr)){
                            foreach ($model->wages_arr as $row){
                                echo "<tr>";
                                echo "<td>".$row[0]."</td>";
                                echo "<td>".$row[1]."</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'just_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'just_remark',
                        array('rows'=>3,'readonly'=>($model->wages_status != 1 || $model->scenario == "view"))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php


$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

