<?php
$this->pageTitle=Yii::app()->name . ' - StaffSummary';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'staffSummary-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','staff summary'); ?></strong>
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
    <?php
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= TbHtml::dropDownList($modelName.'[year]',$model->year,StaffSummaryList::getYearList(),
        array("class"=>"form-control submit_year"));
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','staff summary'),
        'model'=>$model,
        'viewhdr'=>'//staffSummary/_listhdr',
        'viewdtl'=>'//staffSummary/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>array(
            'city'
        ),
        'search_add_html'=>$search_add_html,
    ));
    ?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>
<div class="modal fade bs-example-modal-lg" id="deptModel" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deptTitle"></h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th><?php echo Yii::t("contract","Employee Code");?></th>
                        <th><?php echo Yii::t("contract","Employee Name");?></th>
                        <th><?php echo Yii::t("contract","Department");?></th>
                        <th><?php echo Yii::t("contract","Position");?></th>
                        <th><?php echo Yii::t("contract","Entry Time");?></th>
                        <th><?php echo Yii::t("contract","Leave Time");?></th>
                        <th><?php echo Yii::t("contract","Status");?></th>
                    </tr>
                    </thead>
                    <tbody id="deptBody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t("dialog","Close");?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php

$js = <<<EOF
function showdetail(id) {
	var icon = $('#btn_'+id).attr('class');
	if (icon.indexOf('plus') >= 0) {
		$('.detail_'+id).show();
		$('#btn_'+id).attr('class', 'fa fa-minus-square');
	} else {
		$('.detail_'+id).hide();
		$('#btn_'+id).attr('class', 'fa fa-plus-square');
	}
}
EOF;
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_HEAD);
$js = "
    $('.submit_year,.submit_year_type').on('change',function(){
        $('form:first').submit();
    });
    
    $('.click_detail').click(function(){
        var city_name=$(this).data('city_name');
        var city=$(this).data('city');
        var id=$(this).data('id');
        var name=$(this).data('name');
        $('#deptTitle').html(city_name+'<small>（'+name+'）</small>'+' - {$model->year}年');
        $.ajax({
            type: 'post',
            url: '".Yii::app()->createUrl('StaffSummary/ajaxDetail')."',
            data: {city:city,id:id,year:'{$model->year}'},
            dataType: 'json',
            success: function(data){
                $('#deptBody').html(data.html);
                $('#deptModel').modal('show');
            }
        });
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>
