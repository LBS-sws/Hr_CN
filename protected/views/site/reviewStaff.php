
<legend><?php echo Yii::t("contract","Staff View");?></legend> <!--員工信息-->
<div class="form-group">
    <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'code',
            array('readonly'=>(true))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'name',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'entry_time',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'entry_time',
            array('readonly'=>(true))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo TbHtml::textField('city', CGeneral::getCityName($model->city),
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'company_name',
            array('readonly'=>(true))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'dept_name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'dept_name',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'year',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'year',
            array('readonly'=>(true))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'year_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php
        echo TbHtml::textField("year_type",ReviewAllotList::getYearTypeList($model->year_type,$model->year),array('readonly'=>(true)))
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'phone',
            array('readonly'=>(true))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'status_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-2">
        <?php echo TbHtml::textField('status_type',ReviewAllotList::getReviewStatuts($model->status_type)["status"],
            array('readonly'=>(true))
        ); ?>
    </div>
</div>