<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC06', 'rewardCon/edit', 'rewardCon/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZC06', 'rewardCon/edit', 'rewardCon/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['money']; ?></td>
	<td><?php echo $this->record['goods']; ?></td>
</tr>
