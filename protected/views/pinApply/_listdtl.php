<tr class='clickable-row' data-href='<?php echo $this->getLink('PI01', 'pinApply/edit', 'pinApply/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('PI01', 'pinApply/edit', 'pinApply/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['pin_code']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['employee_id']; ?></td>
    <td><?php echo $this->record['position']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['name_id']; ?></td>
    <td><?php echo $this->record['pin_num']; ?></td>
</tr>
