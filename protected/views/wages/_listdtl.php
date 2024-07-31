<tr class='clickable-row' data-href='<?php echo $this->getLink('ZA03', 'wages/edit', 'wages/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZA03', 'wages/edit',  'wages/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['wages_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
</tr>
