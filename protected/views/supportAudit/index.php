<?php
$this->pageTitle=Yii::app()->name . ' - supportAudit';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'supportAudit-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Audit technical support'); ?></strong>
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
                if (Yii::app()->user->validRWFunction("AY02"))
                    echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Batch Finish'), array(
                        'submit'=>Yii::app()->createUrl('supportAudit/batchFinish'),
                    ));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction("AY02"))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('supportAudit/new'),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'support_code',
        'employee_id',
        'apply_city',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','support audit list'),
        'model'=>$model,
        'viewhdr'=>'//supportAudit/_listhdr',
        'viewdtl'=>'//supportAudit/_listdtl',
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
$js="
    $('.checkBox').on('click',function(e){
        e.stopPropagation();
        if(!$(this).is(':checked')){
            $('#selectAll').prop('checked',false);
        }
    });
    $('#selectAll').on('click',function(){
        var bool = $(this).is(':checked');
        $('.checkBox').prop('checked',bool);
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

