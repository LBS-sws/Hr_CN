<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('EL02', 'extUpdate/edit', 'extUpdate/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('EL02', 'extUpdate/edit', 'extUpdate/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['office_name']; ?></td>
    <td><?php echo $this->record['phone']; ?></td>
	<td><?php echo $this->record['department']; ?></td>
	<td><?php echo $this->record['position']; ?></td>
	<td><?php echo $this->record['entry_time']; ?></td>
	<td><?php echo $this->record['company_id']; ?></td>
	<td><?php echo $this->record['table_type']; ?></td>
	<td><?php echo $this->record['status']; ?></td>
    <td>
        <?php if (!empty($this->record['extUpdatedoc'])): ?>
            <span class="fa fa-paperclip"></span>
        <?php endif; ?>
    </td>
</tr>
