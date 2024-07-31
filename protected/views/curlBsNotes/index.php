<?php
$this->pageTitle=Yii::app()->name . ' - CurlBsNotes';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'curlBsNotes-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>
<style>
    td{word-break: break-all;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Curl Bs Notes'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="pull-left">
                <p style="margin: 7px 0px;">未进行：P，完成：C，错误：E。</p>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '."获取北森变更员工", array(
                    'id'=>'openBsModal'));
                ?>
            </div>
        </div>
    </div>
	<?php
    $search_add_html="";
    $modelName = get_class($model);
    $typeList=CurlBsNotesList::getInfoTypeList();
    if(!empty($typeList)){
        $typeList = array_merge(array(""=>"-- 全部 --"),$typeList);
        $search_add_html .= TbHtml::dropDownList($modelName.'[info_type]',$model->info_type,$typeList,
            array("class"=>"form-control submitBtn"));
    }

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('curl','CurlBsNotes List'),
        'model'=>$model,
        'viewhdr'=>'//curlBsNotes/_listhdr',
        'viewdtl'=>'//curlBsNotes/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'id',
            'data_content',
            'out_content',
            'message',
            'status_type',
        ),
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
<div class="modal fade" tabindex="-1" role="dialog" id="textModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">内容详情</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <textarea class="form-control" rows="7" id="textInput"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="getBsModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">获取北森变更员工</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-4 control-label required">开始时间 <span class="required">*</span></label>
                            <div class="col-sm-5">
                                <input id="startDate" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label required">结束时间 <span class="required">*</span></label>
                            <div class="col-sm-5">
                                <input id="endDate" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnGetBsModal">获取北森变更员工</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    function unicode2Ch(t) {
        if (t) {
            for (var e = 1, n = "", i = 0; i < t.length; i += e) {
                e = 1;
                var o = t.charAt(i);
                if ("\\" == o)
                    if ("u" == t.charAt(i + 1)) {
                        var r = t.substr(i + 2, 4);
                        n += String.fromCharCode(parseInt(r, 16).toString(10)),
                            e = 6
                    } else
                        n += o;
                else
                    n += o
            }
            return n
        }
    }
</script>
<?php
$url = Yii::app()->createUrl('curlBsNotes/getNow',array("index"=>'1'));
echo TbHtml::button("",array("submit"=>"#","class"=>"hide"));
$ajaxUrl =Yii::app()->createUrl('curlBsNotes/getAjaxStr');
	$js = "
	    $('.text-break').click(function(){
            $.ajax({
                type: 'post',
                url: '{$ajaxUrl}',
                data: {
                    id: $(this).data('id'),
                    type: $(this).data('type')
                },
                dataType: 'json',
                success: function (data) {
                    var text = data['content'];
                    text = unicode2Ch(text);
                    $('#textInput').val(text);
                    $('#textModal').modal('show');
                }
            });
	    });
	    
$('.submitBtn').change(function(){
    $('form:first').submit();
});

$('#openBsModal').on('click',function(){
    var interval = 1000*60*10;//10分钟
    var dateObj = new Date();
    var timer = dateObj.getTime();
    var startTime = Math.floor(timer / interval) * interval; //起始时间戳
    var endTime = startTime+interval; //结束时间戳
    dateObj.setTime(startTime);
    $('#startDate').val(dateObj.toLocaleString());
    dateObj.setTime(endTime);
    $('#endDate').val(dateObj.toLocaleString());
    $('#getBsModal').modal('show');
});
$('#btnGetBsModal').on('click',function(){
    window.location.href = '{$url}&startDate='+$('#startDate').val()+'&endDate='+$('#endDate').val();
    return false;
});
	";
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
