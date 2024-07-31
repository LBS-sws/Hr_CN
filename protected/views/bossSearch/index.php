<?php
$this->pageTitle=Yii::app()->name . ' - bossSearch';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'bossSearch-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Boss Search'); ?></strong>
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
        </div></div>
    <?php
    $search = array(
        'code',
        'name',
        'audit_year',
        'city_name',
    );

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Boss Apply List'),
        'model'=>$model,
        'viewhdr'=>'//bossSearch/_listhdr',
        'viewdtl'=>'//bossSearch/_listdtl',
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

