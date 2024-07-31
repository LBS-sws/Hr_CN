<?php
$this->pageTitle=Yii::app()->name . ' - MakeWages Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'makeWages-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    select[readonly]{pointer-events: none;}
</style>
<section class="content-header">
    <h1>
        <strong>
            <?php
            if(empty($model->id)){
                echo Yii::t('app','Wages Make');
            }else{
                echo Yii::t('contract','Wages Detail');
            }
            ?>
        </strong>
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
    <div class="box"><div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('makeWages/index')));
                ?>

                <?php if ($model->scenario!='view'): ?>
                    <?php if ($model->scenario=='new' || $model->wages_status == 0 || $model->wages_status == 2): ?>
                        <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                            'submit'=>Yii::app()->createUrl('makeWages/save')));
                        ?>
                        <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                            'submit'=>Yii::app()->createUrl('makeWages/audit')));
                        ?>
                    <?php endif ?>
                <?php endif ?>
            </div>

        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo $form->hiddenField($model, 'wages_status'); ?>

            <?php if ($model->wages_status == 2): ?>
            <div class="form-group has-error">
                <?php echo $form->labelEx($model,'just_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'just_remark',
                        array('rows'=>3,'readonly'=>true)
                    ); ?>
                </div>
            </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'wages_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'wages_date',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'employee_id',$model->getEmployeeList(),
                        array('readonly'=>($model->getOnly()),'id'=>"staff_id")
                    ); ?>
                </div>
            </div>
            <div class="form-group" id="staff_detail">
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'sum',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'sum',
                        array('readonly'=>$model->getOnly(),'min'=>0)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'wages_arr',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php
                    $addHtml = $model->getAddHtmlTr();
                    $html = '<table class="table table-bordered table-striped">';
                    $html .= '<thead><tr>';
                    $html .= '<td>'.Yii::t("contract","Wage Name").'</td>';
                    $html .= '<td>'.Yii::t("contract","Wage Number").'</td>';
                    if(!$model->getOnly()){
                        $html .= '<td width="5%"></td>';
                    }
                    $html .= '</tr></thead><tbody id="wage_body">';
                    if(!empty($model->wages_arr)){
                        $key=0;
                        foreach ($model->wages_arr as $row){
                            $key++;
                            $html .= strtr($addHtml, array(":key" =>$key,":wage_name" =>$row[0],":wage_num" =>$row[1]));
                            $html .= '</tr>';
                        }

                    }
                    $html .= '</tbody>';
                    if(!$model->getOnly()){
                        $html.="<tfoot><tr><td colspan=\"2\"></td><td>";
                        $html.=TbHtml::button(Yii::t('app','New'), array("id"=>"addWage",'color'=>TbHtml::BUTTON_COLOR_PRIMARY));
                        $html.="</td></tr></tfoot>";
                    }
                    $html.='</table>';
                    echo $html;
                    ?>
                </div>
            </div>

            <?php if (!$model->getOnly()): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'wages_body',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'wages_body',WagesForm::getWagesList(),
                            array('readonly'=>($model->getOnly()),"id"=>"change_wage")
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

        </div>
    </div>
</section>

<?php
$js = "
$('#change_wage').on('change',function(){
    if($(this).val() != ''){
        $.ajax({
            type: 'post',
            url: '".Yii::app()->createUrl('makeWages/ajaxChangeWages')."',
            data: {
                con_id:$(this).val(),
            },
            dataType: 'json',
            success: function(data){
                if(data.status == 1){
                    $('#wage_body').html(data.html);
                }else{
                    $('#wage_body').html('');
                }
            }
        });
    }
});

$('#staff_id').on('change',function(){
    if($(this).val() != ''){
        $.ajax({
            type: 'post',
            url: '".Yii::app()->createUrl('makeWages/ajaxEmployeeHtml')."',
            data: {
                staff_id:$(this).val(),
            },
            dataType: 'json',
            success: function(data){
                if(data.status == 1){
                    $('#staff_detail').html(data.html);
                }else{
                    $('#staff_detail').html('');
                }
            }
        });
    }else{
        $('#staff_detail').html('');
    }
}).trigger('change');

$('#addWage').on('click',function(){
    var key = $('#wage_body>tr:last').data('key');
    if(key == undefined){
        key = 0;
    }
    key++;
    html='$addHtml';
    html=html.replace(/:key/g,key);
    html=html.replace(/:wage_name/g,'');
    html=html.replace(/:wage_num/g,'');
    $('#wage_body').append(html);
});

$('#wage_body').delegate('.delWage','click',function(){
    $(this).parents('tr:first').remove();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

