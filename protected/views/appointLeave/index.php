<?php
$this->pageTitle=Yii::app()->name . ' - appointLeave';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'appointLeave-list',
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
        'viewhdr'=>'//appointLeave/_listhdr',
        'viewdtl'=>'//appointLeave/_listdtl',
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
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

