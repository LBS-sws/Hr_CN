<?php
$this->pageTitle=Yii::app()->name . ' - Trip';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'trip-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Application for trip'); ?></strong>
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
                if (Yii::app()->user->validRWFunction('ZA10'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('trip/new'),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'trip_code',
        'trip_address',
        'employee_name',
        'city_name',
    );
    $search_add_html="";
    $modelName = get_class($model);
    if (Yii::app()->user->validFunction('ZA10')){
        $search[] = 'city_name';
        $search_add_html .= TbHtml::textField($modelName.'[searchTimeStart]',$model->searchTimeStart,
            array('size'=>15,'autocomplete'=>'off','placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control dateSubmit","id"=>"start_time"));
        $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
        $search_add_html .= TbHtml::textField($modelName.'[searchTimeEnd]',$model->searchTimeEnd,
            array('size'=>15,'autocomplete'=>'off','placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control dateSubmit","id"=>"end_time"));
    }
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('fete','trip list'),
        'model'=>$model,
        'viewhdr'=>'//trip/_listhdr',
        'viewdtl'=>'//trip/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
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
$url = Yii::app()->createUrl('trip/index',array("pageNum"=>1));
$js = "
$('.dateSubmit').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('.dateSubmit').change(function(){
    jQuery.yii.submitForm(this,'{$url}',{});
    return false;
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

