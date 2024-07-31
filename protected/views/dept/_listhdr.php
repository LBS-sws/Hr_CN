<tr>
	<th></th>
	<th>
		<?php
        echo TbHtml::link($this->model->getTypeName().$this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('dept-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('z_index'),'#',$this->createOrderLink('dept-list','z_index'))
			;
		?>
	</th>
    <?php
    if($this->model->type == 1){
        echo "<th>";
        echo TbHtml::link($this->getLabelName('dept_class').$this->drawOrderArrow('dept_class'),'#',$this->createOrderLink('dept-list','dept_class'));
        echo "</th>";
        echo "<th>";
        echo TbHtml::link($this->getLabelName('review_status').$this->drawOrderArrow('review_status'),'#',$this->createOrderLink('dept-list','review_status'));
        echo "</th>";
        echo "<th>";
        echo TbHtml::link($this->getLabelName('review_type').$this->drawOrderArrow('review_type'),'#',$this->createOrderLink('dept-list','review_type'));
        echo "</th>";
        echo "<th>";
        echo TbHtml::link($this->getLabelName('manager_type').$this->drawOrderArrow('manager_type'),'#',$this->createOrderLink('dept-list','manager_type'));
        echo "</th>";
        echo "<th>";
        echo TbHtml::link($this->getLabelName('manager_leave').$this->drawOrderArrow('manager_leave'),'#',$this->createOrderLink('dept-list','manager_leave'));
        echo "</th>";
        echo "<th>";
        echo TbHtml::link($this->getLabelName('level_type').$this->drawOrderArrow('level_type'),'#',$this->createOrderLink('dept-list','level_type'));
        echo "</th>";
    }
    ?>
</tr>
