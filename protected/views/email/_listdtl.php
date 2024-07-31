<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('RE07', 'email/edit', 'email/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('RE07', 'email/edit', 'email/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['subject']; ?></td>
    <td><?php echo $this->record['city_str']; ?></td>
    <td><?php echo $this->record['staff_str']; ?></td>
    <td><?php echo $this->record['status_type']; ?></td>
</tr>
