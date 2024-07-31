
<div class="form-group">
    <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'lcd',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<legend><?php echo  Yii::t("contract","courier detail");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'sign_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'sign_type',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'send_date',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'send_date',
                array('class'=>'form-control pull-right','readonly'=>($readonly),"id"=>"send_date"));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'courier_str',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'courier_str',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'courier_code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'courier_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php
        echo $form->textArea($model, 'remark',
            array('rows'=>4,'cols'=>80,'maxlength'=>1000,'readonly'=>($readonly),)
        );
        ?>
    </div>
</div>
