<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('bossApply/index'));
}
$this->pageTitle=Yii::app()->name . ' - Boss Apply Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'bossApply-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    .table-responsive th{white-space: nowrap;}
    .table-responsive>table{table-layout:fixed}
    #moveCofWindow{position: absolute;top: 0px;left: 0px;width: 300px;box-shadow: -4px 3px 5px rgba(0,0,0,0.1);background: #fff;z-index: 999}
    #moveCofWindow>.arrow-left{position: absolute;top: 50%;left: -5px;margin-top:-3px;border-right: 6px solid #fff;border-top: 6px solid transparent;border-bottom: 6px solid transparent;width: 0px;height: 0px;}
    #moveCofWindow>table{margin: 0px;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Boss Apply Form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('bossApply/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='new'||in_array($model->status_type,array(0,3,4))): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('bossApply/save')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','For Audit'), array(
                    'id'=>'btnConfirm','data-toggle'=>'modal','data-target'=>'#confirmDialog',));
                ?>
            <?php endif ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    // 下载
                    echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('bossApply/downExcel',array("index"=>$model->id))));
                } ?>
                <?php if ($model->scenario=='edit'&&in_array($model->status_type,array(0,3,4))): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                    ?>
                <?php endif; ?>
                <?php if ($model->scenario!='new'){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                        'name'=>'btnBossFlow','id'=>'btnBossFlow','data-toggle'=>'modal','data-target'=>'#bossflowinfodialog'));
                } ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>

           <?php if ($model->status_type == 3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6 error">
                        <?php echo $form->textArea($model, 'reject_remark',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
               <legend>&nbsp;</legend>
           <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group">
                        <?php echo $form->textField($model, 'audit_year',
                            array('readonly'=>(true))
                        ); ?>
                        <span class="input-group-addon"><?php echo Yii::t("contract"," year");?></span>
                    </div>
                </div>
            </div>
            <?php
            $tabs = $model->getTabList($model,$model->status_type == 2);
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/bossflow',array('model'=>$model));
$this->renderPartial('//site/removedialog');
?>
<script>
    function resetTableSum() {
        if($("#sum_label").data("num") =="no"){
            return false;
        }
        var sum = 0;
	var ratio_a = "<?php echo $model->ratio_a?>";
	var ratio_b = "<?php echo $model->ratio_b?>";
        var sum_a = 0;
        var sum_b = 0;
        var sum_c = (isNaN($("#three_sum").val())||$("#three_sum").val()=='')?0:parseFloat($("#three_sum").val());
	ratio_a = parseFloat(ratio_a)*0.01;
	ratio_b = parseFloat(ratio_b)*0.01;
        $('#table_id_BossReviewA').find('input[name$="[one_12]"]').each(function () {
            sum_a+=$(this).val()==''?0:parseFloat($(this).val());
        });
        $('#table_id_BossReviewB').find('input[name$="[two_9]"]').each(function () {
            sum_b+=$(this).val()==''?0:parseFloat($(this).val());
        });
        if($("#bossRewardType").data("num")==1){
            sum = sum_a*ratio_a+sum_b*ratio_b;
            sum_a = sum_a.toFixed(2);
            sum_b = sum_b.toFixed(2);
            sum = sum.toFixed(2);
            $("#sum_label").text(sum_a+"*<?php echo $model->ratio_a?>% + "+sum_b+"*<?php echo $model->ratio_b?>% = "+sum+"%");
        }else{
            sum = sum_a*ratio_a+sum_b*ratio_b+sum_c;
            sum_a = sum_a.toFixed(2);
            sum_b = sum_b.toFixed(2);
            sum = sum.toFixed(2);
            $("#sum_label").text(sum_a+"*<?php echo $model->ratio_a?>% + "+sum_b+"*<?php echo $model->ratio_b?>% + "+sum_c+"% = "+sum+"%");
        }
    }
    function changeCofWindow() {
        $(".changeCofWindow").mouseout(function () {
            $("#moveCofWindow").remove();
        });
        $(".changeCofWindow").mouseover(function () {
            $("#moveCofWindow").remove();
            var dataName = $(this).data("name");
            var kpiData = $(this).data("kpi");
            var kpiType = $(this).data("size");
            var html="<div id='moveCofWindow'>";
            html+="<span class='arrow-left'></span><table class='table table-bordered table-hover'><thead><tr>";
            var num=0,left,top;
            var oldCof = $(this).val();
            var nowCof = $(this).parents('tr:first').find('input[name$="[cofNow]"]').eq(0).val();
            var nameArr =["one_eight","two_one","two_two","two_three","two_five","one_nine","two_eight","two_service"];
            var ratio_value = "<?php echo Yii::t('contract','ratio value');?>";
            html+="<th width='75%' class='text-center'>"+ratio_value+"</th><th class='text-center'>";
            html+="<?php echo Yii::t('contract','one_5');?>";
            html+="</th></tr></thead><tbody>";
            kpiData = kpiData.split(",");
            $.each(kpiData,function (i,val) {
                var list = val.split(":");
                var str = "",className="";
                if(nameArr.indexOf(dataName)<0){
                    list[0]+="%";
                }
                if(kpiType == 1){//從大到小排序
                    var nextList = kpiData.length-1==i?val:kpiData[i+1];
                    nextList = nextList.split(":");
                    if(nameArr.indexOf(dataName)<0){
                        nextList[0]+="%";
                    }
                    if(i==0){//第一行
                        str =ratio_value+">="+list[0];
                    }else if(i == kpiData.length-1){//最後一行
                        str =ratio_value+"<"+num;
                    }else{//中間循環
                        str =list[0]+"<= "+ratio_value+" <"+num;
                    }
                }else{
                    if(i==0){//第一行
                        str = ratio_value+"<="+list[0];
                    }else if(i == kpiData.length-1){//最後一行
                        str =ratio_value+">"+num;
                    }else{//中間循環
                        str =num+"< "+ratio_value+" <="+list[0];
                    }
                }
                if(list[1] == oldCof){
                    className = "success";
                }
                if(list[1] == nowCof){
                    className = "warning";
                }
                html+="<tr class='"+className+"'><td class='text-center'>"+str+"</td><td class='text-center'>"+list[1]+"</td></tr>";
                num = list[0];
            });
            html+="</tbody></table></div>";
            html = $(html);
            $("body").append(html);
            left = $(this).offset().left+$(this).outerWidth()+5;
            top = $(this).offset().top+($(this).outerHeight()-html.outerHeight())/2;
            var maxTop = $("body").outerHeight();
            var delTop=0;
            if(top+html.outerHeight()>maxTop){
                delTop = top+html.outerHeight()-maxTop;
            }
            top -=delTop;
            $("#moveCofWindow>.arrow-left").css({"margin-top":delTop+"px"})
            html.css({
                "left":left+"px",
                "top":top+"px"
            });
        });
    }
</script>
<?php
$content = "<p>".Yii::t('contract','confirmed and submitted, it cannot be modified after submission?')."</p>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'confirmDialog',
    'header'=>Yii::t('contract','Confirm Audit'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnConfirmData','submit'=>Yii::app()->createUrl('bossApply/audit'),'color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>
<?php
$js = Script::genDeleteData(Yii::app()->createUrl('bossApply/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = "
    $('.bossHintTitle').popover();
changeCofWindow();
    $('.planYearA,.planYearB').on('keyup',function(){
        var name = $(this).data('name');
        var tr = $(this).parents('tr:first');
        var one_1 = $('input[name=\"BossApplyForm[json_text][one_one][one_1]\"]').eq(0).val();
        var cofNow = tr.find('input[name$=\"[cofNow]\"]').eq(0).val();
        var data,one_11,one_0,type;
        one_0 = tr.find('input[name$=\"[one_1]\"]').eq(0).val();
        if($(this).hasClass('planYearA')){
            type = 'planYearA';
            one_11 = tr.find('input[name$=\"[one_11]\"]').eq(0).val();
        }else{
            type = 'planYearB';
            one_11 = tr.find('input[name$=\"[two_8]\"]').eq(0).val();
        }
        var data ={
                city:'$model->city',
                name:name,
                value:$(this).val(),
                one_1:one_1,
                one_11:one_11,
                one_0:one_0,
                type:type,
                cofNow:cofNow
            };
        $.ajax({
            type: 'POST',
            url: '".Yii::app()->createUrl('bossApply/ajaxPlanYear')."',
            data: data,
            dataType: 'json',
            success: function(data) {
                $.each(data,function(key,val){
                    var td = tr.find('td.'+key);
                    if(td.children('span').length<1){
                        tr.find('td.'+key).eq(0).children('input:first').val(val);
                    }else{
                        tr.find('td.'+key).eq(0).children('span').text(val);
                        if(key == 'one_12'||key=='two_9'){
                            tr.find('td.'+key).eq(0).children('input:first').val(parseFloat(val));
                        }
                    }
                });
                resetTableSum();
            }
        });
    });
    
    $('#addRow').click(function(){
        var num = $('#table_three>tbody').data('num');
        var html = $('#trTemplate').html();
        num = isNaN(num)?0:num;
        num++;
        $('#table_three>tbody').data('num',num);
        num = 'BossApplyForm[json_text][three][list]['+num+']';
        html = html.replace(/:inputName:/g,num);
        $('#table_three>tbody').append('<tr>'+html+'</tr>');
    });
    $('#table_three>tbody').delegate('.deleteRow','click',function(){
        $(this).parents('tr').eq(0).remove();
    });
    $('#table_three').delegate('.changeThreeFour,#three_count','keyup',function(){
        var sum = 0;
        var count = $('#three_count').val()==''?0:parseFloat($('#three_count').val());
        $('#table_three .changeThreeFour').each(function(){
            sum+=$(this).val()==''?0:parseFloat($(this).val());
        });
        sum = count==0?0:(sum/count)*{$model->ratio_c};
        $('#three_sum').val(sum.toFixed(2));
        resetTableSum();
    });
    $('#three_count').trigger('keyup');
    resetTableSum();
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

