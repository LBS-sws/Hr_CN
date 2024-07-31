<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('company/index'));
}
$this->pageTitle=Yii::app()->name . ' - Company Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'company-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Company Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('company/index')));
		?>

        <?php if ($model->scenario!='view'&&$model->readyCityAndPix()): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('company/save')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Copy'), array(
                'submit'=>Yii::app()->createUrl('company/copy',array("index"=>$model->id))));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php
                    if($model->readyCityAndPix()){
                        $counter = ($model->no_of_attm['company3'] > 0) ? ' <span id="doccompany3" class="label label-info">'.$model->no_of_attm['company3'].'</span>' : ' <span id="doccompany3"></span>';
                        echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('contract','Insurance Documents').$counter, array(
                                'name'=>'btnCompany3','id'=>'btnCompany3','data-toggle'=>'modal','data-target'=>'#fileuploadcompany3',)
                        );
                    }
                ?>
                <?php
                $counter = ($model->no_of_attm['company2'] > 0) ? ' <span id="doccompany2" class="label label-info">'.$model->no_of_attm['company2'].'</span>' : ' <span id="doccompany2"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('contract','Qualification  Documents').$counter, array(
                        'name'=>'btnCompany2','id'=>'btnCompany2','data-toggle'=>'modal','data-target'=>'#fileuploadcompany2',)
                );
                ?>
                <?php
                if($model->readyCityAndPix()) {
                    $counter = ($model->no_of_attm['company'] > 0) ? ' <span id="doccompany" class="label label-info">' . $model->no_of_attm['company'] . '</span>' : ' <span id="doccompany"></span>';
                    echo TbHtml::button('<span class="fa  fa-file-text-o"></span> ' . Yii::t('misc', 'Attachment') . $counter, array(
                            'name' => 'btnCompany', 'id' => 'btnCompany', 'data-toggle' => 'modal', 'data-target' => '#fileuploadcompany',)
                    );
                }
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-5">
					<?php echo $form->textField($model, 'name',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-5">
                    <?php echo $form->dropDownList($model, 'city',CompanyList::getSingleCityToList($model->city),
                        array('disabled'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'head',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'head',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
				<?php echo $form->labelEx($model,'head_email',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'head_email',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'legal',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'legal',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'legal_email',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'legal_email',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'legal_city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'legal_city',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'taxpayer_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'taxpayer_num',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
            </div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'agent',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'agent',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
				<?php echo $form->labelEx($model,'agent_email',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'agent_email',
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'address',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-8">
					<?php echo $form->textField($model, 'address',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'phone',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
				<?php echo $form->labelEx($model,'postal',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'postal',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'address2',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-8">
					<?php echo $form->textField($model, 'address2',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'postal2',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'postal2',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'phone_two',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'phone_two',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'mie',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'mie',array(""=>"","A"=>"A","B"=>"B","C"=>"C"),
                        array('disabled'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'security_code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'security_code',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'organization_code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'organization_code',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
                <?php echo $form->labelEx($model,'organization_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'organization_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()),));
                        ?>
                    </div>
                </div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'license_code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'license_code',
						array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
					); ?>
				</div>
			</div>
			<div class="form-group">
                <?php echo $form->labelEx($model,'license_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'license_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()),));
                        ?>
                    </div>
                </div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'tacitly',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->inlineRadioButtonList($model, 'tacitly',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'share_bool',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->inlineRadioButtonList($model, 'share_bool',array(Yii::t("contract","not share"),Yii::t("contract","share")),
                        array('readonly'=>($model->scenario=='view'||!$model->readyCityAndPix()))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-7 col-sm-offset-2">
                    <p class="form-control-static text-danger">注：选择共享后，公司资料信息及资质资料附件其他城市能查看并下载</p>
                </div>
            </div>


            <legend><?php echo Yii::t("contract","JD System Curl");?></legend>
            <?php
            $html = "";
            $className = get_class($model);
            foreach (CompanyForm::$jd_set_list as $num=>$item){
                $field_value = key_exists($item["field_id"],$model->jd_set)?$model->jd_set[$item["field_id"]]:null;
                if($num%2==0){
                    $html.='<div class="form-group">';
                }
                $html.=TbHtml::label(Yii::t("contract",$item["field_name"]),'',array('class'=>"col-sm-2 control-label"));
                $html.='<div class="col-lg-3">';
                $html.=TbHtml::textField("{$className}[jd_set][{$item["field_id"]}]",$field_value,array('readonly'=>($model->scenario=='view')));
                $html.="</div>";
                if($num%2==1){
                    $html.='</div>';
                }
            }
            if(count(CompanyForm::$jd_set_list)%2==0){
                $html.='</div>';
            }
            echo $html;
            ?>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'COMPANY',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'||!$model->readyCityAndPix()),
));
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'COMPANY2',
    'header'=>Yii::t('contract','Qualification  Documents'),
    'ronly'=>($model->scenario=='view'||!$model->readyCityAndPix()),
));
$this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'COMPANY3',
    'header'=>Yii::t('contract','Insurance Documents'),
    'ronly'=>($model->scenario=='view'||!$model->readyCityAndPix()),
));
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
Script::genFileUpload($model,$form->id,'COMPANY');
Script::genFileUpload($model,$form->id,'COMPANY2');
Script::genFileUpload($model,$form->id,'COMPANY3');

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'CompanyForm_organization_time',
        'CompanyForm_license_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('company/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

