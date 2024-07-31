<?php
$this->pageTitle=Yii::app()->name . ' - bossApply';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'bossApply-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Boss Apply'); ?></strong>
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
                if (Yii::app()->user->validRWFunction('BA01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('bossApply/add'),
                    ));
                ?>
            </div>

            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['bosskpi'] > 0) ? ' <span id="docbosskpi" class="label label-info">'.$model->no_of_attm['bosskpi'].'</span>' : ' <span id="docbosskpi"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('contract','KPI Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadbosskpi',)
                );
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'code',
        'name',
        'audit_year',
    );

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Boss Apply List'),
        'model'=>$model,
        'viewhdr'=>'//bossApply/_listhdr',
        'viewdtl'=>'//bossApply/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
    ));
    ?>
</section>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'BOSSKPI',
    'header'=>Yii::t('contract','KPI Attachment'),
    'ronly'=>($model->scenario=='view'||!Yii::app()->user->validRWFunction('BA04')),
));
?>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
Script::genFileUpload($model,$form->id,'BOSSKPI');
$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

