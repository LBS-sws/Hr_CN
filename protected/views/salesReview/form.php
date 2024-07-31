<?php
$this->pageTitle=Yii::app()->name . ' - SalesReview';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'SalesReview-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    select[readonly="readonly"]{pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Sales Review Search')." - ".$model->getGroupListStr("group_name"); ?></strong>
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
				'submit'=>Yii::app()->createUrl('SalesReview/index')));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::dropDownList("year",$model->year,ReviewAllotList::getYearList(),array("readonly"=>true)) ?>
                </div>
                <div class="col-sm-2">
                    <?php echo TbHtml::dropDownList("year_type",$model->year_type,ReviewAllotList::getYearTypeList(-1,$model->year),array("readonly"=>true)) ?>
                </div>
            </div>
            <?php
            $tabs = $model->getTabList();
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
		</div>
	</div>
</section>
<?php

$js = "
    $('#prompt_button').on('click',function(){
        if($('#prompt').hasClass('active')){
            //打開
            $('#prompt').removeClass('active');
            localStorage['salesReview'] = 0;
        }else{
            //關閉
            $('#prompt').addClass('active');
            localStorage['salesReview'] = 1;
        }
    });
    if(localStorage['salesReview']==1){ 
        $('#prompt_button').trigger('click');
    }
    //全选
    $('#allCheck').on('click',function(){
        if($(this).is(':checked')){
            $('.onlyCheck').prop('checked',true);
        }else{
            $('.onlyCheck').prop('checked',false);
        }
        tableChange();
    });
    //选中事件
    $('.onlyCheck').change(tableChange);
    
    function tableChange(){
        $('.showTable>tbody>tr').hide();
        $('.onlyCheck:checked').each(function(){
            var code = $(this).data('code');
            console.log(code);
            $('tr[data-code=\"'+code+'\"]').show();
        });
    }
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->



<style>
    .prompt{position: fixed;top:20%;right: 10px;border-radius:4px;min-width:25px;min-height:25px;box-shadow:0px 0px 2px rgba(0,0,0,0.4);z-index: 1;background: #fff;}
    .prompt_div{padding: 25px;width: 530px;}
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
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th width="23%"><?php echo Yii::t("contract","deviation");?></th>
                <th><?php echo Yii::t("contract","instructions");?></th>
                <th width="12%"><?php echo Yii::t("contract","review score");?></th>
            </tr>
            </thead>
            <tbody>
            <?php echo $model->getInstructionsList();?>
            </tbody>
        </table>
    </div>
</div>