<?php
$this->pageTitle=Yii::app()->name . ' - ExtUpdate';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'extUpdate-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','External Update'); ?></strong>
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
        'code',
        'name',
        'phone',
        'department',
        'position',
        'office_name',
    );
    if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Update List'),
        'model'=>$model,
        'viewhdr'=>'//extUpdate/_listhdr',
        'viewdtl'=>'//extUpdate/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'table_type');
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
$url = Yii::app()->createUrl('extUpdate/index',array("pageNum"=>1));

$js = <<<EOF
    $('.btn_submit').on('click',function(){
        var key=$(this).data('key');
        $("#ExtUpdateList_orderField").val("");
        $("#ExtUpdateList_table_type").val(key);
        jQuery.yii.submitForm(this,'{$url}',{});
    });
EOF;

$js.= Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

