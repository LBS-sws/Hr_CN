<tr class='clickable-row' data-href='<?php echo $this->getLink('ZP03', 'recruitApply/edit', 'recruitApply/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZP03', 'recruitApply/edit', 'recruitApply/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['city']; ?></td>
	<td><?php echo $this->record['year']; ?></td>
	<td><?php echo $this->record['leader_name']; ?></td>
	<td><?php echo $this->record['dept_name']; ?></td>
	<td><?php echo $this->record['recruit_num']; ?></td>
	<td class="td-click" data-type="0" data-id="<?php echo $this->record['id']; ?>"><?php echo $this->record['now_num']; ?></td>
	<td class="td-click" data-type="-1" data-id="<?php echo $this->record['id']; ?>"><?php echo $this->record['leave_num']; ?></td>
	<td><?php echo $this->record['lack_num']; ?></td>
	<td><?php echo $this->record['completion_rate']; ?></td>
</tr>
