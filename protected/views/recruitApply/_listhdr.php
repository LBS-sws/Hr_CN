<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('recruitApply-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('a.year'),'#',$this->createOrderLink('recruitApply-list','a.year'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('leader_name').$this->drawOrderArrow('g.name'),'#',$this->createOrderLink('recruitApply-list','g.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('dept_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('recruitApply-list','f.name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('recruit_num').$this->drawOrderArrow('a.recruit_num'),'#',$this->createOrderLink('recruitApply-list','a.recruit_num'))
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
