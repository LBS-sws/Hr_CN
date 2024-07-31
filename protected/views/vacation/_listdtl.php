<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC04', 'vacation/edit', 'vacation/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZC04', 'vacation/edit','vacation/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['ass_bool']; ?></td>
    <td><?php echo $this->record['ass_id_name']; ?></td>
    <td><?php echo $this->record['only']; ?></td>
</tr>
