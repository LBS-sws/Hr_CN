
<form class="form-horizontal" method="post" enctype="multipart/form-data">
<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','class'=>'pull-left','color'=>TbHtml::BUTTON_COLOR_DEFAULT));
	$ftrbtn[] = TbHtml::link(Yii::t('contract','down temp'),Yii::app()->createUrl('employ/downTemp'), array('class'=>"pull-left btn btn-".TbHtml::BUTTON_COLOR_INFO,'target'=>'_blank'));
	$ftrbtn[] = TbHtml::button(Yii::t('contract','import'), array(
        'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
        'submit'=>Yii::app()->createUrl('employ/importExcel')
    ));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'importdialog',
					'header'=>Yii::t('contract','import'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>
<div>
    <div class="form-group">
        <?php echo TbHtml::label(Yii::t("contract","file"),'',array('class'=>"col-lg-4 control-label")); ?>
        <div class="col-lg-5">
            <?php
            echo TbHtml::hiddenField("EmployDown[id]",'1');
            echo TbHtml::fileField("EmployDown[file]",'',array("readonly"=>false,'class'=>'form-control'));
            ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12 text-danger">
            <p class="form-control-static">
                <span>注1：可以先下载“导入模板”然后修改模板里的内容，最后导入修改的模板</span><br/>
                <span>注2：一次最多导入500条数据，如果多于500条请分多个Excel导入</span>
            </p>
        </div>
    </div>
</div>

<?php
	$this->endWidget();
?>
<?php
$js = <<<EOF
EOF;
Yii::app()->clientScript->registerScript('importEmploy',$js,CClientScript::POS_READY);

?>
</form>