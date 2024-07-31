<?php
$this->pageTitle=Yii::app()->name . ' - reviewAllot';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'reviewAllot-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Review Allot'); ?></strong>
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-body">
            <div class="btn-group pull-right" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-edit"></span> '.Yii::t('contract','bulk allot'), array(
                    'id'=>'btnExportData',
                    'submit'=>Yii::app()->createUrl('reviewAllot/bulkAllot'),
                ));
                ?>
            </div>
        </div>
    </div>
    <?php
    $search = array(
        'code',
        'name',
        'phone',
        'position',
        'department',
        'city_name',
        'status',
    );
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= TbHtml::dropDownList($modelName.'[year]',$model->year,$model->getYearList(),
        array("class"=>"form-control submit_year"));
    $search_add_html.="<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::dropDownList($modelName.'[year_type]',$model->year_type,$model->getYearTypeList(-1,$model->year),
        array("class"=>"form-control submit_year_type"));

    if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Employee List'),
        'model'=>$model,
        'viewhdr'=>'//reviewAllot/_listhdr',
        'viewdtl'=>'//reviewAllot/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
        'search_add_html'=>$search_add_html,
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
$('.che').on('click', function(e){
e.stopPropagation();
});

$('body').on('click','#all',function() {
	var val = $(this).prop('checked');
	$('input[type=checkbox][name*=\"ReviewAllotList[attr][]\"]').prop('checked',val);
});
";
Yii::app()->clientScript->registerScript('selectAll',$js,CClientScript::POS_READY);
$js = "
    $('.submit_year,.submit_year_type').on('change',function(){
        $('form:first').submit();
    });
";
if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){
    $js.= "
    function resetYearType(){
        var year = $('.submit_year:first').val();
        if(year == 2020){
            $('.submit_year_type>option:last').hide();
        }else{
            $('.submit_year_type>option:last').show();
        }
    }
    resetYearType();
";
}
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

