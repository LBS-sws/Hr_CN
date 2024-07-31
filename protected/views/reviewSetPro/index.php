<?php
$this->pageTitle=Yii::app()->name . ' - reviewSetPro';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'reviewSetPro-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>
<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('contract','review pro list'); ?></strong>
    </h1>
</section>

<section class="content">
    <div class="box"><div class="box-body">
            <div class="btn-group" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('RE04')){
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('reviewSetPro/new',array("type"=>$model->type)),
                    ));
                }
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'),array(
                    'submit'=>Yii::app()->createUrl('reviewSet/index')));
                ?>
            </div>
        </div>
    </div>
    <?php
    $search = array(
        'id',
        'pro_name',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>$model->name,
        'model'=>$model,
        'viewhdr'=>'//reviewSetPro/_listhdr',
        'viewdtl'=>'//reviewSetPro/_listdtl',
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

