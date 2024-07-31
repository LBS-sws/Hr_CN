
<?php if (!empty($model->reject_cause)): ?>
    <div class="form-group has-error">
        <?php echo $form->labelEx($model,'reject_cause',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-6">
            <?php echo $form->textArea($model, 'reject_cause',
                array('readonly'=>(true),"rows"=>4)
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($model->scenario!='new'): ?>
    <div class="form-group">
        <?php echo TbHtml::label($model->getAttributeLabel("work_code").'<span class="required">*</span>',"",array('class'=>"col-lg-2 control-label"));?>
        <div class="col-lg-4">
            <?php echo $form->textField($model, 'work_code',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("employee_id").'<span class="required">*</span>',"",array('class'=>"col-lg-2 control-label"));?>
    <div class="col-lg-4">
        <?php echo $form->textField($model, 'employee_id',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("work_type").'<span class="required">*</span>',"",array('class'=>"col-lg-2 control-label"));?>
    <div class="col-lg-3">
        <?php echo $form->dropDownList($model, 'work_type',WorkList::getWorkTypeList(),
            array('readonly'=>($model->getInputBool()),"id"=>"work_type")
        ); ?>
    </div>
</div>
<div id="work_time_div">

</div>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("log_time").'<span class="required">*</span>',"",array('class'=>"col-lg-2 control-label"));?>
    <div class="col-lg-3">
        <div class="input-group">
            <?php echo $form->numberField($model, 'log_time',
                array('readonly'=>($model->getInputBool()),"id"=>"log_time")
            ); ?>
            <span class="input-group-addon"><?php echo Yii::t("contract","Hour");?></span>
        </div>
    </div>
</div>
<?php if (!empty($model->lcd)): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'lcd',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("work_address").'<span class="required">*</span>',"",array('class'=>"col-lg-2 control-label"));?>
    <div class="col-lg-6">
        <?php echo $form->textField($model, 'work_address',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("work_cause").'<span class="required">*</span>',"",array('class'=>"col-lg-2 control-label"));?>
    <div class="col-lg-6">
        <?php echo $form->textArea($model, 'work_cause',
            array('readonly'=>($model->getInputBool()),"rows"=>4)
        ); ?>
    </div>
</div>

<?php if (!empty($model->pers_lcu)): ?>
    <legend><?php echo Yii::t("fete","Audit Info")?></legend>
    <div class="form-group">
        <?php echo AppointSetForm::getLabelHtml($model,'pers_lcu',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'pers_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo AppointSetForm::getLabelHtml($model,'pers_lcd',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'pers_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($model->user_lcu)): ?>
    <?php if (empty($model->pers_lcu)): ?>
    <legend><?php echo Yii::t("fete","Audit Info")?></legend>
    <?php endif; ?>
    <div class="form-group">
        <?php echo AppointSetForm::getLabelHtml($model,'user_lcu',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'user_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo AppointSetForm::getLabelHtml($model,'user_lcd',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'user_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($model->area_lcu)): ?>
    <div class="form-group">
        <?php echo AppointSetForm::getLabelHtml($model,'area_lcu',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'area_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo AppointSetForm::getLabelHtml($model,'area_lcd',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'area_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($model->head_lcu)): ?>
    <div class="form-group">
        <?php echo AppointSetForm::getLabelHtml($model,'head_lcu',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'head_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo AppointSetForm::getLabelHtml($model,'head_lcd',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'head_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($model->you_lcu)): ?>
    <div class="form-group">
        <?php echo AppointSetForm::getLabelHtml($model,'you_lcu',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'you_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo AppointSetForm::getLabelHtml($model,'you_lcd',array('class'=>"col-lg-2 control-label")); ?>
        <div class="col-lg-3">
            <?php echo $form->textField($model, 'you_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<script>
    $(function ($) {
        $('#work_type').on('change',function(){
            var value = $(this).val();
            $.ajax({
                type: 'post',
                url: '<?php echo Yii::app()->createUrl('work/ajaxWorkType');?>',
                data: {
                    modelStr:'<?php echo get_class($model);?>',
                    work_type:value,
                    index:'<?php echo $model->id;?>',
                    only:'<?php echo $model->getInputBool();?>',
                },
                dataType: 'json',
                success: function(data){
                    if(data.status == 1){
                        $("#work_time_div").html(data.html);
                        $('.changeDateTime').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: '<?php echo Yii::app()->language;?>'});
                    }else{
                        $("#work_time_div").html("");
                    }
                }
            });
        }).trigger('change');

        $('#work_time_div').delegate("#addWorkTime","click",function () {
            var tBody = $("#work_time_div tbody:first");
            var num = tBody.data("num");
            tBody.data("num",num+1);
            var html = $("#workTrModel").html();
            html = html.replace(/#start_time#/g, "WorkForm[addTime]["+num+"][start_time]");
            html = html.replace(/#hours#/g, "WorkForm[addTime]["+num+"][hours]");
            html = html.replace(/#end_time#/g, "WorkForm[addTime]["+num+"][end_time]");
            html = html.replace(/#hours_end#/g, "WorkForm[addTime]["+num+"][hours_end]");
            html = "<tr>"+html+"</tr>";
            tBody.append(html);
            $('.changeDateTime').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: '<?php echo Yii::app()->language;?>'});
        });

        $('#work_time_div').delegate(".delWages","click",function () {
            $(this).parents("tr:first").remove();
        });
    })
</script>