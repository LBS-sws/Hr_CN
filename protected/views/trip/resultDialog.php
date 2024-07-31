<?php
$content="";
/*
$content.= "<div class=\"form-group\">";
$content.=$form->labelEx($model,'result_id',array('class'=>"col-lg-3 control-label"));
$content.= "<div class=\"col-lg-7\">";
$content.=$form->dropDownList($model, 'result_id',TripResultSetForm::getTripResultSetList(),
    array('readonly'=>(false))
);
$content.="</div>";
$content.="</div>";
*/
$content.= "<div class=\"form-group\">";
$content.=$form->labelEx($model,'result_text',array('class'=>"col-lg-3 control-label"));
$content.= "<div class=\"col-lg-7\">";
$content.=$form->textArea($model, 'result_text',
    array('readonly'=>(false),"rows"=>4)
);
$content.="</div>";
$content.="</div>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'resultDialog',
    'header'=>Yii::t('fete','trip result'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array(
            'data-dismiss'=>'modal',
            'submit'=>Yii::app()->createUrl('trip/result'),
            'color'=>TbHtml::BUTTON_COLOR_PRIMARY
        )),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,'class'=>'pull-left')),
    ),
    'show'=>false,
));
?>