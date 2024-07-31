<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('heartLetter-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('heartLetter-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('letter_title').$this->drawOrderArrow('a.letter_title'),'#',$this->createOrderLink('heartLetter-list','a.letter_title'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('letter_type').$this->drawOrderArrow('a.letter_type'),'#',$this->createOrderLink('heartLetter-list','a.letter_type'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('heartLetter-list','a.lcd'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('state').$this->drawOrderArrow('a.state'),'#',$this->createOrderLink('heartLetter-list','a.state'))
			;
		?>
	</th>
</tr>
