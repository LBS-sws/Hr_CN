
<?php
$this->pageTitle=Yii::app()->name . ' - salesStaff';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'salesStaff-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo $model->getGroupListStr("group_name"); ?></strong>
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
                'submit'=>Yii::app()->createUrl('SalesGroup/index')));
            ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('SR01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('contract','Add Staff Group'), array(
                        'submit'=>Yii::app()->createUrl('SalesGroup/staffAdd',array('index'=>$model->index))
                    ));
                ?>
            </div>
        </div>
    </div>
    <?php
    $search = array(
        'code',
        'name',
        'department_name',
        'position_name',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Sales Staff List')." - ".$model->getGroupListStr("group_name"),
        'model'=>$model,
        'viewhdr'=>'//salesGroup/staff_listhdr',
        'viewdtl'=>'//salesGroup/staff_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>
<?php
$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

