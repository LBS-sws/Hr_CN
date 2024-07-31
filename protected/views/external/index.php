<?php
$this->pageTitle=Yii::app()->name . ' - External';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'external-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','External Info'); ?></strong>
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
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('EL01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('contract','Add Employee'), array(
                        'submit'=>Yii::app()->createUrl('external/new'),
                    ));
                ?>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                $modelName = get_class($model);
                $tableList=StaffFun::getTableTypeListIndex();
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
        'title'=>Yii::t('contract','External List'),
        'model'=>$model,
        'viewhdr'=>'//external/_listhdr',
        'viewdtl'=>'//external/_listdtl',
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
$url = Yii::app()->createUrl('external/index',array("pageNum"=>1));

$js = <<<EOF
    $('.btn_submit').on('click',function(){
        var key=$(this).data('key');
        $("#ExternalList_orderField").val("");
        $("#ExternalList_table_type").val(key);
        jQuery.yii.submitForm(this,'{$url}',{});
    });
EOF;

$js.= Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

