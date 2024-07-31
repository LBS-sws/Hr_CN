<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC15', 'configOffice/edit', 'configOffice/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZC15', 'configOffice/edit', 'configOffice/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['city']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['u_id']; ?></td>
	<td><?php echo $this->record['z_display']; ?></td>
	<td class="click_office" data-id="<?php echo $this->record['id'];?>" data-name="<?php echo $this->record['name'];?>"><?php echo $this->record['office_sum']; ?></td>
</tr>
