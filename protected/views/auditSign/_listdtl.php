<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('ZG08', 'auditSign/edit', 'auditSign/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZG08', 'auditSign/edit', 'auditSign/view', array('index'=>$this->record['id'])); ?></td>


    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['sign_type']; ?></td>
    <td><?php echo $this->record['position']; ?></td>
    <td><?php echo $this->record['company_id']; ?></td>
    <td><?php echo $this->record['entry_time']; ?></td>
    <td><?php echo $this->record['courier_str']; ?></td>
    <td><?php echo $this->record['courier_code']; ?></td>
    <td><?php echo $this->record['status_type']; ?></td>
</tr>
