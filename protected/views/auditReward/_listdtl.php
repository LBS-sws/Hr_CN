<tr class='clickable-row <?php echo $this->record["status"]["style"]?>' data-href='<?php echo $this->getLink('ZG06', 'auditReward/edit', 'auditReward/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZG06', 'auditReward/edit', 'auditReward/view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['employee_code']; ?></td>
	<td><?php echo $this->record['employee_name']; ?></td>
	<td><?php echo $this->record['city']; ?></td>
	<td><?php echo $this->record['reward_name']; ?></td>
	<td><?php echo $this->record['reward_money']; ?></td>
	<td><?php echo $this->record['reward_goods']; ?></td>
	<td><?php echo $this->record['lcd']; ?></td>
	<td><?php echo $this->record['status']['status']; ?></td>
</tr>
