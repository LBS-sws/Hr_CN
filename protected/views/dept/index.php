<?php
$this->pageTitle=Yii::app()->name . ' - Dept';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'dept-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong>
            <?php
            if($model->type == 1){
                echo Yii::t('app','Leader');
            }else{
                echo Yii::t('app','Department');
            }
            ?>
        </strong>
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
                if (Yii::app()->user->validRWFunction($model->getTypeAcc()))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('dept/new',array("type"=>$model->type)),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'name',
    );
    if($model->type == 1){
        $search[] = 'review_type';
    }
    //if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>$model->getTypeName().Yii::t('contract',' List'),
        'model'=>$model,
        'viewhdr'=>'//dept/_listhdr',
        'viewdtl'=>'//dept/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'searchlinkparam'=>array('type'=>$model->type),
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

