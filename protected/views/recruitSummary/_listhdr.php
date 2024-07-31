<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('recruitSummary-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('a.year'),'#',$this->createOrderLink('recruitSummary-list','a.year'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('recruit_num').$this->drawOrderArrow('a.recruit_sum'),'#',$this->createOrderLink('recruitSummary-list','a.recruit_sum'))
        ;
        ?>
    </th>
    <th>
        <?php echo $this->getLabelName('now_num');?>
    </th>
    <th>
        <?php echo $this->getLabelName('leave_num');?>
    </th>
    <th>
        <?php echo $this->getLabelName('lack_num');?>
    </th>
    <th>
        <?php echo $this->getLabelName('completion_rate');?>
    </th>
</tr>
