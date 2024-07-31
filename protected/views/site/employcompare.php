
<legend><?php echo Yii::t("contract","personal data");?></legend>
<div class="form-group">
    <?php echo $form->label($model,'name',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'name',
            array('size'=>100,'maxlength'=>100,'readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->label($model,'sex',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'sex',StaffFun::getSexList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'birth_time',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textFieldCompare($oldModel,$model, 'birth_time',
                array('class'=>'form-control pull-right','readonly'=>($readonly),"id"=>"birth_time"));
            ?>
        </div>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'age',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'age',
            array('readonly'=>true,"id"=>"age")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'address',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-5">
        <?php echo $form->textFieldCompare($oldModel,$model, 'address',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
    <div class="col-sm-2">
        <?php echo $form->textFieldCompare($oldModel,$model, 'address_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'contact_address',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-5">
        <?php echo $form->textFieldCompare($oldModel,$model, 'contact_address',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
    <div class="col-sm-2">
        <?php echo $form->textFieldCompare($oldModel,$model, 'contact_address_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'phone',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'phone',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->label($model,'phone2',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'phone2',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'user_card',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'user_card',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'user_card_date',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textFieldCompare($oldModel,$model, 'user_card_date',
                array('class'=>'form-control pull-right','readonly'=>($readonly),));
            ?>
            <span class="input-group-btn">
                <button class="btn btn-default" id="changqi" type="button">长期</button>
            </span>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'wechat',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'wechat',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->label($model,'urgency_card',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'urgency_card',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'emergency_user',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'emergency_user',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->label($model,'emergency_phone',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'emergency_phone',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'empoyment_code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'empoyment_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'recommend_user',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'recommend_user',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'nation',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'nation',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->label($model,'household',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'household',StaffFun::getNationList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'email',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'email',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'health',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'health',StaffFun::getHealthList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>

<legend><?php echo Yii::t("contract","position data");?></legend>
<div class="form-group">
    <?php if ($model->scenario!='new'): ?>
        <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textFieldCompare($oldModel,$model, 'code',
                array('size'=>20,'maxlength'=>20,'readonly'=>true)
            ); ?>
        </div>
    <?php endif; ?>

    <?php echo $form->labelEx($model,'entry_time',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textFieldCompare($oldModel,$model, 'entry_time',
                array('class'=>'form-control pull-right','readonly'=>($readonly),));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'staff_id',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'staff_id',EmployForm::getCompanyToCity($model->staff_id),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'department',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'department',DeptForm::getDeptListToCity("",$model->city),
            array('disabled'=>($readonly),"class"=>"","id"=>"department")
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->label($model,'position',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'position',DeptForm::getPosiList($model->department),
            array('disabled'=>($readonly),"class"=>"","id"=>"position")
        ); ?>
    </div>
    <!--
    <div class="col-sm-3">
        <?php
        $model_class = get_class($model);
        $departmentList = DeptForm::getDeptOneAllList();
        if($readonly){
            echo "<select class='depart form-control changeButton' name='".$model_class."[position]' disabled>";
        }else{
            echo "<select class='depart form-control changeButton' name='".$model_class."[position]'>";
        }
        foreach ($departmentList as $key =>$value){
            if($model->position == $key){
                echo "<option value='$key' data-type='".$value["type"]."' data-dept='".$value["dept_class"]."' selected>".$value["name"]."</option>";
            }else{
                echo "<option value='$key' data-type='".$value["type"]."' data-dept='".$value["dept_class"]."'>".$value["name"]."</option>";
            }
        }
        echo "</select>";
        ?>
    </div>
    -->
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'staff_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'staff_type',StaffFun::getStaffTypeList(),
            array('readonly'=>($readonly),"id"=>"staff_type")
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'staff_leader',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'staff_leader',StaffFun::getStaffLeaderList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <!--刪除工資單變化
    <?php //echo $form->labelEx($model,'price1',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
/*        echo $form->dropDownList($model, 'price1',WagesForm::getWagesList(),
            array('disabled'=>($readonly)));*/
        ?>
    </div>
    -->
    <?php if (DeptForm::getSalesTypeToId($model->department)==1): ?>
        <?php echo $form->labelEx($model,'group_type',array('class'=>"col-sm-2 control-label group_type")); ?>
        <div class="col-sm-3 group_type">
            <?php echo $form->dropDownListCompare($oldModel,$model, 'group_type',DeptForm::getGroupType(),
                array('disabled'=>($readonly),'id'=>'group_type')
            ); ?>
        </div>
    <?php else: ?>
        <?php echo $form->labelEx($model,'group_type',array('class'=>"col-sm-2 control-label group_type","style"=>"display:none")); ?>
        <div class="col-sm-3 group_type" style="display: none;">
            <?php echo $form->dropDownListCompare($oldModel,$model, 'group_type',DeptForm::getGroupType(),
                array('disabled'=>($readonly),'id'=>'group_type')
            ); ?>
        </div>
    <?php endif; ?>
    <!--分割-->
    <?php echo $form->labelEx($model,'code_old',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'code_old',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'office_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'office_id',ConfigOfficeForm::getOfficeList($model->city,$model->office_id),
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>

<legend><?php echo Yii::t("contract","contract data");?></legend>
<?php
if(empty($model->employee_id)){
    $contractNum = StaffFun::getContractNumber($model->id);
}else{
    $contractNum = StaffFun::getContractNumber($model->employee_id);
}
if (!empty($contractNum)){
    echo '<div class="form-group">';
    echo TbHtml::label(Yii::t("contract","Contract Number"),'',array('class'=>"col-sm-2 control-label"));
    echo '<div class="col-sm-3">';
    echo TbHtml::textField('contractNum', $contractNum,array('class'=>'form-control pull-right','readonly'=>(true),));
    echo '</div>';
    echo '</div>';
}
?>
<div class="form-group">
    <?php echo $form->label($model,'fix_time',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-5">
        <?php echo $form->inlineRadioButtonListCompare($oldModel,$model, 'fix_time',StaffFun::getFixTimeList(),
            array('disabled'=>($readonly),'class'=>"fixTime",'id'=>"fix_time")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'time',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textFieldCompare($oldModel,$model, 'start_time',
                array('class'=>'form-control pull-right','readonly'=>($readonly),'id'=>"start_time"));
            ?>
        </div>
    </div>
    <div class="pull-left control-label">至</div>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php
            if($model->fix_time == "nofixed"){
                $model->end_time = "";
                echo $form->textFieldCompare($oldModel,$model, 'end_time',
                    array('class'=>'form-control pull-right','readonly'=>(true),'id'=>"end_time"));
            }else{
                echo $form->textFieldCompare($oldModel,$model, 'end_time',
                    array('class'=>'form-control pull-right','readonly'=>($readonly),'id'=>"end_time"));
            }
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php if (EmployForm::validateWageInput()): ?>
        <?php echo $form->label($model,'wage',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
        <div class="col-sm-3">
            <?php echo $form->numberFieldCompare($oldModel,$model, 'wage',
                array('min'=>0,'readonly'=>($readonly))
            ); ?>
        </div>
    <?php else: ?>
        <?php echo $form->hiddenField($model, 'wage'); ?>
    <?php endif; ?>
    <!--分割-->
    <?php echo $form->label($model,'year_day',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'year_day',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'company_id',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'company_id',EmployForm::getCompanyToCity($model->company_id),
            array('disabled'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->label($model,'contract_id',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'contract_id',EmployForm::getContractToCity($model->contract_id),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'test_type',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'test_type',array(
            "1"=>Yii::t("contract","Have probation period"),
            "0"=>Yii::t("contract","No probation period")
        ),array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="test-div">
    <div class="form-group">
        <?php echo $form->label($model,'test_length',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
        <div class="col-sm-3">
            <?php echo $form->dropDownListCompare($oldModel,$model, 'test_length',StaffFun::getTestMonthLengthList(),
                array('class'=>'test_add_time','disabled'=>($readonly),'id'=>"test_length")
            ); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->label($model,'test_time',array('class'=>"col-sm-2 control-label ","required"=>true)); ?>
        <div class="col-sm-3">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <?php echo $form->textFieldCompare($oldModel,$model, 'test_start_time',
                    array('class'=>'test_add_time pull-right','readonly'=>($readonly),'id'=>'test_start_time'));
                ?>
            </div>
        </div>
        <div class="pull-left control-label">至</div>
        <div class="col-sm-3">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <?php echo $form->textFieldCompare($oldModel,$model, 'test_end_time',
                    array('class'=>'test_sum_time pull-right','readonly'=>true,'id'=>'test_end_time'));
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <!--<span class="required">*</span>-->
        <?php echo $form->label($model,'test_wage',array('class'=>"col-sm-2 control-label","required"=>true)); ?>
        <div class="col-sm-3">
            <?php echo $form->numberFieldCompare($oldModel,$model, 'test_wage',
                array('min'=>0,'readonly'=>($readonly))
            ); ?>
        </div>
    </div>
</div>

<legend><?php echo Yii::t("contract","additional information");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'education',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListCompare($oldModel,$model, 'education',StaffFun::getEducationList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'experience',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textFieldCompare($oldModel,$model, 'experience',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'english',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textAreaCompare($oldModel,$model, 'english',
            array('rows'=>3,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'technology',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textAreaCompare($oldModel,$model, 'technology',
            array('rows'=>3,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'other',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textAreaCompare($oldModel,$model, 'other',
            array('rows'=>3,'readonly'=>($readonly))
        ); ?>
    </div>
</div>

<legend><?php echo Yii::t("contract","archives");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_user',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_user)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_user"))."'></div>";
            }
        }else{
            if(!empty($model->image_user)){
                echo TbHtml::fileField('image_user',"",array("class"=>"file-update form-control","style"=>"display:none"));
                echo $form->hiddenField($model, 'image_user');
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_user"))."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_user',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_code)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_code"))."'></div>";
            }
        }else{
            if(!empty($model->image_code)){
                echo TbHtml::fileField('image_code',"",array("class"=>"file-update form-control","style"=>"display:none"));
                echo $form->hiddenField($model, 'image_code');
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_code"))."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_code',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_work',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_work)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_work"))."'></div>";
            }
        }else{
            if(!empty($model->image_work)){
                echo TbHtml::fileField('image_work',"",array("class"=>"file-update form-control","style"=>"display:none"));
                echo $form->hiddenField($model, 'image_work');
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_work"))."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_work',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_other',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_other)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img height='80px' class='openBigImg' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_other"))."'></div>";
            }
        }else{
            if(!empty($model->image_other)){
                echo TbHtml::fileField('image_other',"",array("class"=>"file-update form-control","style"=>"display:none"));
                echo $form->hiddenField($model, 'image_other');
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".Yii::app()->createUrl('employ/printImage',array("id"=>$model->id,"staff"=>$model->employee_id,"str"=>"image_other"))."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_other',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
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

        $("#changqi").on("click",function () {
            $(this).parents(".input-group:first").find("input").val("2999/12/31");
        })

        //年齡計算
        $('#birth_time').on('change',function(){
            var birth_time = $(this).val();
            if(birth_time != ''){
                var age = jsGetAge(birth_time);
                $('#age').val(age);
            }
        }).trigger("change");

        //職位-變化
        $("#position,#department,#change_city").on("change",function () {
            var type = $(this).attr("id");
            $.ajax({
                type: 'post',
                url: '<?php echo Yii::app()->createUrl('employ/changeDepart');?>',
                data: {
                    department: $("#department").val(),
                    position: $("#position").val(),
                    change_city: $("#change_city").val(),
                    type: type,
                },
                dataType: 'json',
                success: function (data) {
                    if(data.status == 1){
                        var jsonList = data.data;
                        if(type=="department"){
                            $("#position").html("<option value=''></option>");
                            for(var key in jsonList){
                                $("#position").append("<option value='"+key+"'>"+jsonList[key]+"</option>");
                            }
                            if(data.sales_type == 1){
                                $(".group_type").show();
                            }else{
                                $("#group_type").val(0);
                                $(".group_type").hide();
                            }
                        }else if(type=="change_city"){
                            $("#department,#position").html("<option value=''></option>");
                            for(var key in jsonList){
                                $("#department").append("<option value='"+key+"'>"+jsonList[key]+"</option>");
                            }
                            if(data.sales_type == 1){
                                $(".group_type").show();
                            }else{
                                $("#group_type").val(0);
                                $(".group_type").hide();
                            }
                        }else{
                            $("#staff_type").val(jsonList['dept_class']);
                        }
                    }
                }
            });
        });

        //$("#position").trigger("change");
        //後續添加的无用要求
        $(".fixTime,#start_time,#end_time").change(changeTestMonthLength);
    });
    function changeTestMonthLength() {
        var value = $("#test_length").val();
        if($("#end_time").is(":disabled")||$("#start_time").val()==""||$("#end_time").val()==""){
            $("#test_length>option").show();
        }else{
            $("#test_length>option").hide();
            $("#test_length>option[value='']").show();
            var startDate = new Date($("#start_time").val());
            var endDate = new Date($("#end_time").val());
            var Year = endDate.getFullYear()-startDate.getFullYear();
            var Month = endDate.getMonth()-startDate.getMonth();
            var dateLeng = Year*12+Month;
            if(dateLeng<12){
                $("#test_length>option[value='1']").show();
            }else if(dateLeng<36){
                $("#test_length>option[value='1']").show();
                $("#test_length>option[value='2']").show();
            }else{
                $("#test_length>option").show();
            }

            if(value==""||$("#test_length>option[value='"+value+"']").css("display")=="none"){
                $("#test_length").val(1);
            }
            $('.test_add_time:first').trigger("change");
        }
    }
</script>