<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('leave_code').$this->drawOrderArrow('a.leave_code'),'#',$this->createOrderLink('leave-list','a.leave_code'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('leave-list','a.lcd'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('leave-list','b.name'))
			;
		?>
	</th>
    <?php if (Yii::app()->user->validFunction('ZR04')||!Yii::app()->user->isSingleCity()): ?>
        <th>
            <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('leave-list','b.city'))
            ;
            ?>
        </th>
    <?php endif; ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('vacation_id').$this->drawOrderArrow('a.vacation_id'),'#',$this->createOrderLink('leave-list','a.vacation_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('a.start_time'),'#',$this->createOrderLink('leave-list','a.start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('a.end_time'),'#',$this->createOrderLink('leave-list','a.end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_time').$this->drawOrderArrow('a.log_time'),'#',$this->createOrderLink('leave-list','a.log_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('leave-list','a.status'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('leavedoc').$this->drawOrderArrow('leavedoc'),'#',$this->createOrderLink('leave-list','leavedoc'))
        ;
        ?>
    </th>
</tr>
