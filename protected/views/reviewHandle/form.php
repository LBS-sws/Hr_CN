<?php
$this->pageTitle=Yii::app()->name . ' - ReviewHandle Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'reviewHandle-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    tbody>tr{position: relative;}
    select[readonly="readonly"]{pointer-events: none;}
    .reviewSumDiv{position:relative;display: inline-block;padding: 7px;}
    .reviewSumDiv_hint{position: absolute;top: 100%;left: 50%;margin-top: 8px;margin-left:-160px;width: 320px;height: 50px;line-height: 50px;overflow: visible;text-align: center;background: #000;color: #fff;z-index: 1;border-radius: 10px;display: none;}
    .reviewSumDiv_hint.active{display: block;}
    .reviewSumDiv_hint:after{content: " ";position: absolute;top: 0px;left: 50%;margin-top: -9px;margin-left: -4.5px;border-bottom: 9px solid #000;border-left: 9px solid transparent;border-right: 9px solid transparent;}
    /*td.remark{position: absolute;min-width: 300px;}*/
    textarea.form-control{margin: 0px !important;}
</style>
<tr ></tr>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','reviewHandle Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('reviewHandle/index')));
		?>

        <?php if (!$model->getReadonly()): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('app','review'), array(
                    'id'=>'btnConfirm','data-toggle'=>'modal','data-target'=>'#confirmDialog',)
            );
            ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Draft'), array(
                'submit'=>Yii::app()->createUrl('reviewHandle/draft')));
            ?>
        <?php endif ?>
        <div class="reviewSumDiv"><?php echo Yii::t("contract","Score of proportion of reviewers");?>：
            <span id="reviewSum">...</span>
            <div class="reviewSumDiv_hint">......</div>
        </div>
	</div>

                <div class="btn-group pull-right" role="group">
                    <?php if (!$model->getReadonly()): ?>
                    <?php echo TbHtml::button('<span class="fa fa-copy"></span> '.Yii::t('contract','Copy review'), array(
                        'id'=>'reviewCopy'));
                    ?>
                    <?php endif ?>
                    <?php
                    $counter = ($model->no_of_attm['review'] > 0) ? ' <span id="docreview" class="label label-info">'.$model->no_of_attm['review'].'</span>' : ' <span id="docreview"></span>';
                    echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                            'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadreview',)
                    );
                    ?>
                </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'year_type'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'review_type'); ?>

            <?php
            $this->renderPartial('//site/reviewStaff',array(
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <?php if (!empty($model->employee_remark)): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'employee_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'employee_remark',
                            array('readonly'=>(true),'rows'=>3)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <legend><?php echo Yii::t("contract","reviewAllot project");?></legend><!--考核项目-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'handle_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'handle_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'handle_per',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'handle_per',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php
                echo $model->reviewHandleDiv();
            ?><legend><?php echo Yii::t("contract","reviewAllot comments");?></legend><!--考核評語-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'review_remark',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'strengths',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'strengths',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'target',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'target',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'improve',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'improve',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
		</div>

	</div>
</section>
<?php
$review_id = $model->id;
$model->id = $model->review_id;
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'REVIEW',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>true,
));
$model->id = $review_id;
?>
<xmp id="xmpText">
        <textarea rows="1" name=":name" class="form-control" placeholder="<?php echo Yii::t("contract","Scoring remark")?>"></textarea>
</xmp>
<?php
$content = "<p>".Yii::t('contract','assessment score is confirmed and submitted, it cannot be modified after submission?')."</p>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'confirmDialog',
    'header'=>Yii::t('contract','Confirm review'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnConfirmData','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>
<?php

Script::genFileUpload($model,$form->id,'REVIEW');
$link = Yii::app()->createUrl('reviewHandle/save');
$js = "
$('#btnConfirmData').on('click',function() {
	$('#removedialog').modal('hide');
	deletedata();
});

function deletedata() {
	var elm=$('#btnConfirm');
	jQuery.yii.submitForm(elm,'$link',{});
}

var xmpText = $('#xmpText').html();
$('#xmpText').remove();
    $('.changeSelect').change(function(){
        var num = $(this).val();
        var tr = $(this).parents('tr').eq(0);
        var name = tr.data('name')+'[remark]';
        var html = '';
        if(num!=6&&num!=7&&num!=8){
            html = xmpText.replace(/:name/g,name);
            tr.find('td.remark').html(html);
        }else{
            tr.find('td.remark').html('<button class=\"addRemark btn btn-default\" type=\"button\"><span class=\"glyphicon glyphicon-plus\"></span></button>');
        }
        reviewSum();
    });
    $('#prompt_button').on('click',function(){
        if($('#prompt').hasClass('active')){
            $('#prompt').removeClass('active');
        }else{
            $('#prompt').addClass('active');
        }
    });
    $('.remark').delegate('.addRemark','click',function(){
        var tr = $(this).parents('tr').eq(0);
        var name = tr.data('name')+'[remark]';
        var html = xmpText.replace(/:name/g,name);
        tr.find('td.remark').html(html);
    });
    
    
$('#reviewCopy').on('click',function(){
	$.ajax({
		type: 'POST',
		url: '".Yii::app()->createUrl('reviewHandle/copy')."',
		data: $('#reviewHandle-form').serialize(),
		dataType: 'json',
		success: function(data) {
		    if(data.status == 1){
		        data = data.list;
                $.each(data, function(index, element) {
                    num = element['value'];
                    $('#'+index).val(num);
                    $('#'+index).trigger('change');
                    if(element.hasOwnProperty('remark')){
                        $('#'+index).parents('tr:first').find('.remark>textarea').val(element['remark']);
                    }
                });
		    }
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});

$('.reviewSumDiv').hover(function(){
    $('.reviewSumDiv_hint').addClass('active');
},function(){
    $('.reviewSumDiv_hint').removeClass('active');
});

function reviewSum(){
    var reviewNum = 0;
    var reviewSum = 0;
    var fourNum = 0;
    var fourSum = 0;
    var handle_per = parseInt('".$model->handle_per."',10);
    $('.reviewTable').each(function(){
        var num_ratio = parseInt($(this).data('ratio'),10);
        var four_with = parseInt($(this).data('four'),10);
        var length = $(this).find('.changeSelect').length;
        reviewSum += length*10*num_ratio;
        if(four_with == 1){
            fourSum += length*10*num_ratio;
        }
        $(this).find('.changeSelect').each(function(){
            reviewNum+=parseInt($(this).val(),10)*num_ratio;
            if(four_with == 1){
                fourNum += parseInt($(this).val(),10)*num_ratio;
            }
        });
    });
    if('".$model->review_type."' == 4){
        var sumOne = ((reviewNum-fourNum)/(reviewSum-fourSum))*100;
        sumOne = sumOne.toFixed(2);
        var sumTwo = fourSum == 0?0:(fourNum/fourSum)*100;
        sumTwo = sumTwo.toFixed(2);
        var sumOne_per = sumOne*handle_per/100;
        sumOne_per = sumOne_per.toFixed(2);
        var sumTwo_per = sumTwo*handle_per/100;
        sumTwo_per = sumTwo_per.toFixed(2);
        var sum = sumTwo_per*0.1+sumOne_per*0.9;
        sum = sum.toFixed(2);
        $('#reviewSum').text(sum);
        $('.reviewSumDiv_hint').eq(0).text(sumOne+'*'+handle_per+'%*90% + '+sumTwo+'*'+handle_per+'%*10% = '+sum);
    }else{
        var sum = (reviewNum/reviewSum)*100;
        sum = sum.toFixed(2);
        var sum_per = sum*handle_per/100;
        sum_per = sum_per.toFixed(2);
        $('#reviewSum').text(sum_per);
        $('.reviewSumDiv_hint').eq(0).text(sum+' * '+handle_per+'% = '+sum_per);
    }
}
reviewSum();
";//
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<style>
    .prompt{position: fixed;top:20%;right: 10px;border-radius:4px;min-width:25px;min-height:25px;box-shadow:0px 0px 2px rgba(0,0,0,0.4);z-index: 1;background: #fff;}
    .prompt_div{padding: 25px;width: 480px;}
    .prompt_div>p{margin-bottom: 3px;}
    #prompt_button{position: absolute;left: 0px;top: 0px;bottom: 0px;width: 25px;cursor:pointer;}
    #prompt_button>span{position: absolute;top:50%;left: 50%;margin-top: -7px;margin-left: -4px;}
    .prompt.active .fa-angle-double-right:before{content: "\f100";}
    .prompt.active>.prompt_div{display: none;}
    @media (max-width: 768px){
        .prompt_div{width: 100%;}
    }
</style>
<div id="prompt" class="prompt">
    <div id="prompt_button"><span class="fa fa-angle-double-right"></span></div>
    <div class="prompt_div">
        <p><?php echo Yii::t("fete","Assessment score score reference:");?></p>
        <p><?php echo Yii::t("fete","The starting point of each score is 6, with 6 as the central axis");?></p>
        <p><br><?php echo Yii::t("fete","0-3: a 0-3 rating is given for even one example of an action that touches or undermines the group's philosophy and principles");?></p>
        <p><br><?php echo Yii::t("fete","4 points: there are obvious examples");?></p>
        <p><?php echo Yii::t("fete","5 points: give 5 points if you don't do well in a project");?></p>
        <p><?php echo Yii::t("fete","6 points: the generic example has no obvious example, and there is room for further improvement, can give 6 points");?></p>
        <p><?php echo Yii::t("fete","7 points: 7 points for doing better than expected");?></p>
        <p><br><?php echo Yii::t("fete","8-9 points: for the city level managed by a colleague, he/she will be given 8 or 9 points according to the size of the city he/she manages, and 8 or 9 points must be supported by substantial examples in the evaluation period at that time");?></p>
        <p><br><?php echo Yii::t("fete","10 points: if there are more than 3 outstanding examples, you can give 10 points (although this is not easily achieved by colleagues, so it is very rare).");?></p>

    </div>
</div>

