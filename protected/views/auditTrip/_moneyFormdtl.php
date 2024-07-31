<tr>
	<td>
        <?php echo TbHtml::dropDownList($this->getFieldName('money_set_id'), $this->record['money_set_id'],
            TripMoneySetForm::getTripMoneySetList($this->record['money_set_id']),
            array('readonly'=>true,'autocomplete'=>'off','class'=>'money_set_id'
            )); ?>
	</td>
	<td>
        <?php echo TbHtml::numberField($this->getFieldName('trip_money'), $this->record['trip_money'],
            array('readonly'=>true,'min'=>0,'autocomplete'=>'off','class'=>'trip_money'
            )); ?>
	</td>
</tr>
