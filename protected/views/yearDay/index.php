<?php
$this->pageTitle=Yii::app()->name . ' - YearDay';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'yearDay-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','cumulative annual leave'); ?></strong>
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
                if (Yii::app()->user->validRWFunction('ZC07'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('YearDay/new'),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'employee_code',
        'employee_name',
        'year',
        'add_num',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('fete','cumulative annual leave List'),
        'model'=>$model,
        'viewhdr'=>'//yearDay/_listhdr',
        'viewdtl'=>'//yearDay/_listdtl',
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

