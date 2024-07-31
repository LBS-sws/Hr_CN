<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('work_code').$this->drawOrderArrow('a.work_code'),'#',$this->createOrderLink('work-list','a.work_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('work-list','a.lcd'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('work-list','b.name'))
			;
		?>
	</th>
    <?php if (Yii::app()->user->validFunction('ZR03')||!Yii::app()->user->isSingleCity()): ?>
        <th>
            <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('work-list','b.city'))
            ;
            ?>
        </th>
    <?php endif; ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('work_type').$this->drawOrderArrow('a.work_type'),'#',$this->createOrderLink('work-list','a.work_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('a.start_time'),'#',$this->createOrderLink('work-list','a.start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('a.end_time'),'#',$this->createOrderLink('work-list','a.end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_time').$this->drawOrderArrow('a.log_time'),'#',$this->createOrderLink('work-list','a.log_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('work-list','a.status'))
			;
		?>
	</th>
	<th>
        <?php echo TbHtml::link($this->getLabelName('workemdoc').$this->drawOrderArrow('workemdoc'),'#',$this->createOrderLink('work-list','workemdoc'))
        ;
        ?>
	</th>
</tr>
