<?php
$this->pageTitle=Yii::app()->name . ' - templateEmployee';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'templateEmployee-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Review Template Employee'); ?></strong>
    </h1>
</section>

<section class="content">
    <?php
    $search = array(
        'code',
        'name',
        'department',
        'position',
        'city_name',
        'status',
    );

    if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Employee List'),
        'model'=>$model,
        'viewhdr'=>'//templateEmployee/_listhdr',
        'viewdtl'=>'//templateEmployee/_listdtl',
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

