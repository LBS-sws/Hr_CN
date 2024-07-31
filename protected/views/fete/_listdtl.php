<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC03', 'fete/edit', 'fete/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZC03', 'fete/edit', 'fete/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['only']; ?></td>
    <td><?php echo $this->record['cost_num']; ?></td>
</tr>
