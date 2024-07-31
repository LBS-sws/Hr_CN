<!--詳情彈窗-->
<div class="modal fade" tabindex="-1" role="dialog" id="detailDialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <p>加载中....</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$url = Yii::app()->createUrl('recruitApply/ajaxDetail');
$js = <<<EOF
$('.td-click').on('click',function(e){
    e.stopPropagation();
    $('#detailDialog').find('.modal-title').text('');
    $('#detailDialog').find('.modal-body').html('<p>加载中....</p>');
    $('#detailDialog').modal('show');
    $.ajax({
        type: 'GET',
        url: '{$url}',
        data: {
            'id':$(this).data('id'),
            'type':$(this).data('type')
        },
        dataType: 'json',
        success: function(data) {
            $('#detailDialog').find('.modal-title').html(data['title']);
            $('#detailDialog').find('.modal-body').html(data['html']);
        },
        error: function(data) { // if error occured
            alert('Error occured.please try again');
        }
    });
});
EOF;
Yii::app()->clientScript->registerScript('tdClick',$js,CClientScript::POS_READY);
?>