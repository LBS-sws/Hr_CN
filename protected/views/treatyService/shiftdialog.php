<?php


$content = '<div class="form-group">';
$content.=TbHtml::label(Yii::t("treaty","treaty shift(ago) "),"",array('class'=>"col-lg-4 control-label"));
$content.='<div class="col-lg-5">';
$content.=TbHtml::textField("lcu",$model->lcu,array('readonly'=>(true)));
$content.='</div>';
$content.='</div>';
$content.= '<div class="form-group">';
$content.=TbHtml::label(Yii::t("treaty","treaty shift(now)"),"",array('class'=>"col-lg-4 control-label"));
$content.='<div class="col-lg-5">';
$content.=TbHtml::dropDownList("treaty_lcu","",TreatyServiceForm::getTreatyAllUser($model->city));
$content.='</div>';
$content.='</div>';
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'treatyDialog',
    'header'=>Yii::t('treaty','treaty shift'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnTreatyData','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>