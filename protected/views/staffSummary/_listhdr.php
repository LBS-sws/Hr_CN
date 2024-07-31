<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('staffSummary-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('staffSummary-list','a.city'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('staff_sum').$this->drawOrderArrow('a.staff_sum'),'#',$this->createOrderLink('staffSummary-list','a.staff_sum'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('leave_sum').$this->drawOrderArrow('a.leave_sum'),'#',$this->createOrderLink('staffSummary-list','a.leave_sum'))
        ;
        ?>
    </th>
    <th>
        <?php echo $this->getLabelName('now_sum');?>
    </th>
    <th>
        <?php echo $this->getLabelName('leave_rate');?>
    </th>
</tr>
