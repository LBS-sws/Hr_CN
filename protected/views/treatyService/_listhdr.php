<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('treaty_code').$this->drawOrderArrow('a.treaty_code'),'#',$this->createOrderLink('treatyService-list','a.treaty_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('treaty_name').$this->drawOrderArrow('a.treaty_name'),'#',$this->createOrderLink('treatyService-list','a.treaty_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('treatyService-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('treaty_num').$this->drawOrderArrow('a.treaty_num'),'#',$this->createOrderLink('treatyService-list','a.treaty_num'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('treatyService-list','a.apply_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_date').$this->drawOrderArrow('a.start_date'),'#',$this->createOrderLink('treatyService-list','a.start_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_date').$this->drawOrderArrow('a.end_date'),'#',$this->createOrderLink('treatyService-list','a.end_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('state_type').$this->drawOrderArrow('a.state_type'),'#',$this->createOrderLink('treatyService-list','a.state_type'))
			;
		?>
	</th>
    <?php if (Yii::app()->user->validFunction('ZR21')): ?>
        <th>
            <?php echo TbHtml::link($this->getLabelName('lcu').$this->drawOrderArrow('a.lcu'),'#',$this->createOrderLink('treatyService-list','a.lcu'))
            ;
            ?>
        </th>
    <?php endif ?>
</tr>
