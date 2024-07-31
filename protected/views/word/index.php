<?php
$this->pageTitle=Yii::app()->name . ' - Word';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'word-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php Yii::t('app','Contract Word'); ?></strong>
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
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('ZD01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('contract','Add Word'), array(
                        'submit'=>Yii::app()->createUrl('word/new'),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'name',
        'city',
    );
    if (!Yii::app()->user->isSingleCity()) $search[] = 'word_name';
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Word List'),
        'model'=>$model,
        'viewhdr'=>'//word/_listhdr',
        'viewdtl'=>'//word/_listdtl',
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

