<?php
$this->pageTitle=Yii::app()->name . ' - Work';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'work-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','apply for work overtime'); ?></strong>
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
                if (Yii::app()->user->validRWFunction('ZA05'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('work/new'),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'work_code',
        'work_type',
        'employee_name',
    );
    if(Yii::app()->user->validFunction('ZR03')||!Yii::app()->user->isSingleCity()){
        $search[] = 'city_name';
    }
    $search_add_html="";
    $modelName = get_class($model);
    if (Yii::app()->user->validFunction('ZR03')){
        $search[] = 'city_name';
        $search_add_html .= TbHtml::textField($modelName.'[searchTimeStart]',$model->searchTimeStart,
            array('size'=>15,'autocomplete'=>'off','placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time"));
        $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
        $search_add_html .= TbHtml::textField($modelName.'[searchTimeEnd]',$model->searchTimeEnd,
            array('size'=>15,'autocomplete'=>'off','placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time"));
    }

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('fete','Overtime work List'),
        'model'=>$model,
        'viewhdr'=>'//work/_listhdr',
        'viewdtl'=>'//work/_listdtl',
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
$url = Yii::app()->createUrl('work/index',array("pageNum"=>1));
$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#start_time,#end_time').change(function(){
    jQuery.yii.submitForm(this,'{$url}',{});
    return false;
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

