<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('ZA04', 'makeWages/edit', 'makeWages/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZA04', 'makeWages/edit', 'makeWages/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
	<td><?php echo $this->record['position']; ?></td>
	<td><?php echo $this->record['wages_date']; ?></td>
	<td><?php echo $this->record['staff_status']; ?></td>
</tr>
