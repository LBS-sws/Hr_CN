<?php
$this->pageTitle=Yii::app()->name . ' - auditLeave';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'auditLeave-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','audit for leave'); ?></strong>
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
    <?php
    $search = array(
        'leave_code',
        'employee_name',
        'city_name',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('fete','Ask leave List'),
        'model'=>$model,
        'viewhdr'=>'//auditLeave/_listhdr',
        'viewdtl'=>'//auditLeave/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'searchlinkparam'=>array('only'=>$model->only),
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
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

