<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('bossKPI/index'));
}
$this->pageTitle=Yii::app()->name . ' - Boss Apply Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'bossKPI-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    *[readonly]{pointer-events: none;}
    .table-responsive th{white-space: nowrap;}
    .table-responsive>table{table-layout:fixed}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','kpi form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('bossKPI/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('bossKPI/save')));
            ?>
        <?php endif; ?>
	</div>
            <?php if ($model->scenario=='edit'): ?>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-file"></span> '.Yii::t('misc','Copy'), array(
                    'submit'=>Yii::app()->createUrl('bossKPI/copy',array("index"=>$model->id))));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            </div>
            <?php endif; ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'kpi_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'kpi_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'city',CGeneral::getCityList(),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'tacitly',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->inlineRadioButtonList($model, 'tacitly',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'size_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'size_type',$model->getSizeTypeList(),
                        array('readonly'=>($model->getReadonly()),"id"=>"size_type")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'sum_bool',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'sum_bool',$model->getSumBoolList(),
                        array('readonly'=>($model->getReadonly()),"id"=>"sum_bool")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <p class="form-control-static text-danger">注意：老总年度考核未完成之前，考核分数都会根据kpi配置而发生变化</p>
                </div>
            </div>
            <legend><?php echo Yii::t("contract","kpi detail");?></legend>
            <div class="json_one" <?php if ($model->sum_bool==1): ?>style="display: none" <?php endif; ?> >
                <?php
                echo $model->getJsonOneTable();
                ?>
            </div>
            <div class="json_two" <?php if (empty($model->sum_bool)): ?>style="display: none" <?php endif; ?> >
                <?php
                $tabs = $model->getJsonTwoTable();
                $this->widget('bootstrap.widgets.TbTabs', array(
                    'tabs'=>$tabs,
                    'htmlOptions'=>array('data-num'=>count($tabs),'id'=>"jsonTwo_maxDiv")
                ));
                ?>
            </div>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/removedialog');
?>
<script>
    function changeJsonTwoMaxDiv(e) {
        if($(this).attr("href")=="#addJsonTwo"){
            $("#jsonTwo_maxDiv>.tab-content>div").removeClass("active in");
            $("#jsonTwo_maxDiv>ul>li").removeClass("active");
            var num =$("#jsonTwo_maxDiv").data("num");
            var htmlLi = $("#templateTwo").html();
            var htmlTable = $("#templateTable").html();
            var objLi = $("<li class='active' role='menuitem'>"+htmlLi+"</li>");
            num = isNaN(num)?0:num;
            num++;
            var name ='json_two]['+num;
            $("#jsonTwo_maxDiv").data("num",num);
            objLi.find("a").eq(0).attr("href","#tab_"+num);
            $("#beforeLi").before(objLi);
            htmlTable = htmlTable.replace(/:nameTwo:/g,name);
            htmlTable = htmlTable.replace(/:num:/g,num);
            $("#templateTable").before("<div id='tab_"+num+"' class='tab-pane fade active in'>"+htmlTable+"<div>");
            return false;
        }
    }
    function changeSumKey(e) {
        var value = $(this).val();
        var hrefStr = $(this).parents(".tab-pane.fade:first").attr("id");
        $("#jsonTwo_maxDiv>ul").find("a[href='#"+hrefStr+"']>.changeSum").text(value);
    }
    function jsonTwoDelFun(e) {
        var hrefStr = $(this).parents(".tab-pane.fade:first").attr("id");
        $(this).parents(".tab-pane.fade:first").remove();
        var listObj = $("#jsonTwo_maxDiv>ul").find("a[href='#"+hrefStr+"']").parent("li");
        listObj.next().children('a').trigger('click');
        listObj.remove();
    }
</script>
<?php
$js = "

    $('#jsonTwo_maxDiv>ul>li>a').on('click',changeJsonTwoMaxDiv);
    $('#bossKPI-form').delegate('.tableAdd','click',function(){
        var table = $(this).parents('table').eq(0);
        //.children('tbody')
        var html = table.find('tbody>.addReadyRow').html();
        var num = table.data('num');
        var nameStr = table.data('str');
        num = isNaN(num)?0:num;
        num++;
        table.data('num',num);
        num = 'BossKPIForm['+nameStr+']['+num+']';
        html = html.replace(/:name:/g,num);
        table.find('.otherValue').eq(0).before('<tr>'+html+'</tr>');
    });
    
    $('#bossKPI-form').delegate('.changeSumKey','keyup',changeSumKey);
    $('#bossKPI-form').delegate('.changeSumKey','change',changeSumKey);
    $('#bossKPI-form').delegate('.jsonTwoDel','click',jsonTwoDelFun);
    
    $('#bossKPI-form').delegate('.tableDel','click',function(){
        $(this).parents('tr').eq(0).remove();
    });
    
    $('#size_type').change(function(){
        if($(this).val() == 1){
            $('.changeSize').text('>=');
        }else{
            $('.changeSize').text('<=');
        }
    });

    $('#sum_bool').change(function(){
        if($(this).val()==1){
            $('.json_two').show();
            $('.json_one').hide();
        }else{
            $('.json_one').show();
            $('.json_two').hide();
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('bossKPI/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

