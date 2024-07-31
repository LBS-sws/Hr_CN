<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC12', 'vacationType/edit', 'vacationType/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZC12', 'vacationType/edit','vacationType/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['vaca_code']; ?></td>
    <td><?php echo $this->record['vaca_name']; ?></td>
</tr>
