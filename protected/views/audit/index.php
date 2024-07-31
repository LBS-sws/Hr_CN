<?php
$this->pageTitle=Yii::app()->name . ' - Audit';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'audit-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php Yii::t('app','Audit Info'); ?></strong>
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
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Audit List'),
        'model'=>$model,
        'viewhdr'=>'//audit/_listhdr',
        'viewdtl'=>'//audit/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>array(
            'code',
            'name',
            'phone',
            'department',
            'position',
        ),
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

