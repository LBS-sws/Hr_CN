
<?php if ($model->scenario!='new'): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php if ($model->status==3||$model->status==2): ?>
            <?php echo $form->labelEx($model,'lud',array('class'=>"col-sm-2 control-label")); ?>
            <div class="col-sm-3">
                <?php echo $form->textField($model, 'lud',
                    array('readonly'=>(true))
                ); ?>
            </div>
        <?php endif ?>
    </div>
<?php endif ?>
<div class="form-group">
    <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'city',PrizeForm::getSingleCityToList(),
            array('disabled'=>($model->getInputBool()),'id'=>"city")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'employee_id',PrizeForm::getEmployeeList($model->city),
            array('disabled'=>($model->getInputBool()),'id'=>"staff")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'work_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'work_type',
            array('readonly'=>(true),"id"=>"work_type")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_date',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'prize_date',
                array('readonly'=>($model->getInputBool()),"id"=>"prize_date")
            );
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_num',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->numberField($model, 'prize_num',
            array('readonly'=>($model->getInputBool()),"id"=>"staffNum")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_pro',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'prize_pro',PrizeList::getPrizeList(),
            array('disabled'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'prize_type',array(Yii::t("fete","testimonial"),Yii::t("fete","prize")),
            array('disabled'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'type_num',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->numberField($model, 'type_num',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'type_num_ex',array('class'=>"col-sm-2 control-label text-warning")); ?>
</div>
<div class="form-group">
    <?php echo $form->hiddenField($model, 'customer_name',array("id"=>"customer_name")); ?>
    <?php echo $form->labelEx($model,'customer_name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php if ($model->getInputBool()): ?>
            <?php echo $form->textField($model, 'customer_dis',
                array('readonly'=>(true),'id'=>"customer_dis")
            ); ?>
        <?php else: ?>
        <div class="input-group">
            <?php echo $form->textField($model, 'customer_dis',
                array('readonly'=>(true),'id'=>"customer_dis")
            ); ?>
            <span class="input-group-btn">
                <?php echo TbHtml::button(Yii::t('dialog','Select'), array(
                        'name'=>'customer_btn','id'=>'customer_btn','data-toggle'=>'modal','data-target'=>'#customerdialog',)
                );
                ?>
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'contact',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'contact',
            array('readonly'=>($model->getInputBool()),'id'=>"cont_name")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'phone',
            array('readonly'=>($model->getInputBool()),'id'=>"cont_phone")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'posi',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'posi',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'photo1',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($model->getInputBool()){
            if(empty($model->photo1)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".Yii::app()->createUrl('prize/printImage',array("id"=>$model->id,"str"=>"photo1"))."'></div>";
            }
        }else{
            if(!empty($model->photo1)){
                echo TbHtml::fileField('photo1',"",array("class"=>"file-update form-control","style"=>"display:none"));
                echo $form->hiddenField($model, 'photo1');
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".Yii::app()->createUrl('prize/printImage',array("id"=>$model->id,"str"=>"photo1"))."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'photo1',
                    array('readonly'=>($model->getInputBool()),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'photo2',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($model->getInputBool()){
            if(empty($model->photo2)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".Yii::app()->createUrl('prize/printImage',array("id"=>$model->id,"str"=>"photo2"))."'></div>";
            }
        }else{
            if(!empty($model->photo2)){
                echo TbHtml::fileField('photo2',"",array("class"=>"file-update form-control","style"=>"display:none"));
                echo $form->hiddenField($model, 'photo2');
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".Yii::app()->createUrl('prize/printImage',array("id"=>$model->id,"str"=>"photo2"))."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'photo2',
                    array('readonly'=>($model->getInputBool()),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>

<div class="form-group">
    <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'remark',
            array('rows'=>3,'readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>

<script>
    $(function ($) {
        $("body").append('<div class="modal fade text-center" style="padding-top: 30px;" id="bigImgDiv"></div>');
        $("body").delegate(".openBigImg,.fileImgShow img","click",function () {
            var imgSrc = $(this).attr("src");
            var width = $(this).width();
            var height = $(this).height();
            var max_width= $(window).width()-100;
            var max_height= $(window).height()-100;
            var new_width = width/height*max_height;
            var new_height = height/width*new_width;
            if(new_width>max_width){
                new_width = max_width;
                new_height = height/width*new_width;
            }
            if(new_height>max_height){
                new_height = max_height;
                new_width = width/height*new_height;
            }
            $('#bigImgDiv').html("<img src='"+imgSrc+"' height='"+new_height+"px' width='"+new_width+"px'>");
            $('#bigImgDiv').modal('show');
        });
    })
</script>