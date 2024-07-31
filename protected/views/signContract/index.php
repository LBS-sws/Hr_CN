<?php
$this->pageTitle=Yii::app()->name . ' - signContract';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'signContract-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','sign contract'); ?></strong>
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
        </div></div>
    <?php
    $search_add_html="";
    if(!Yii::app()->user->isSingleCity()){
        $search_add_html .= TbHtml::dropDownList('SignContractList[city]',$model->city,$model->getCityAllList(),
                array("class"=>"form-control","id"=>"change_city"))."<span style='display:inline-block;width:20px;'>&nbsp;</span>";
    }
    $search = array(
        'code',
        'name',
        'courier_str',
        'courier_code',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Employee List'),
        'model'=>$model,
        'viewhdr'=>'//signContract/_listhdr',
        'viewdtl'=>'//signContract/_listdtl',
        'search_add_html'=>$search_add_html,
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
$js = "
$('#change_city').change(function(){
    $('form:first').submit();
});
    ";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

