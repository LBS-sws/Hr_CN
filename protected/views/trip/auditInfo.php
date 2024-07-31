
<?php if (!empty($model->pers_lcu)): ?>
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
