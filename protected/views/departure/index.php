<?php
$this->pageTitle=Yii::app()->name . ' - Departure';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'departure-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Departure Employee List'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                $modelName = get_class($model);
                $tableList=StaffFun::getTableTypeList(false);
                $class = ""==$model->table_type?" btn-primary active":"";
                echo TbHtml::button("全部",array("class"=>"btn_submit".$class,"data-key"=>""));
                foreach ($tableList as $key=>$value){
                    $class = $key==$model->table_type?" btn-primary active":"";
                    echo TbHtml::button($value,array("class"=>"btn_submit".$class,"data-key"=>$key));
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    $search = array(
        'code',
        'name',
        'phone',
        'department',
        'position',
        'office_name',
    );
    if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Departure Employee List'),
        'model'=>$model,
        'viewhdr'=>'//departure/_listhdr',
        'viewdtl'=>'//departure/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'table_type');
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
$url = Yii::app()->createUrl('departure/index',array("pageNum"=>1));

$js = <<<EOF
    $('.btn_submit').on('click',function(){
        var key=$(this).data('key');
        $("#DepartureList_orderField").val("");
        $("#DepartureList_table_type").val(key);
        jQuery.yii.submitForm(this,'{$url}',{});
    });
EOF;

$js.= Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

