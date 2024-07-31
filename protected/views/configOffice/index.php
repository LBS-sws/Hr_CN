<?php
$this->pageTitle=Yii::app()->name . ' - ConfigOffice Info';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'configOffice-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Config office'); ?></strong>
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
            <div class="btn-group" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('ZC15'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('configOffice/new'),
                    ));
                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('contract','Office List'),
			'model'=>$model,
				'viewhdr'=>'//configOffice/_listhdr',
				'viewdtl'=>'//configOffice/_listdtl',
				'search'=>array(
							'name',
							'city',
							'staff',
							'u_id',
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


<?php
$this->renderPartial('//configOffice/_office',array('id'=>0));
?>
<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
	$url = Yii::app()->createUrl('configOffice/ajaxOffice');
$js = "
    $('.click_office').on('click',function(e){
        e.stopPropagation();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#office_title').text('（'+name+'）');
        $.ajax({
            type: 'GET',
            url: '{$url}',
            data: {id:id},
            dataType: 'json',
            success: function(data) {
                $('#officeModel').modal('show');
                $('#office_body').html(data.html);
            },
            error: function(data) { // if error occured
                alert('Error occured.please try again');
            }
        });
    });
";
Yii::app()->clientScript->registerScript('officeClass',$js,CClientScript::POS_READY);
?>

