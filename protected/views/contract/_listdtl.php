<tr class='clickable-row' data-href='<?php echo $this->getLink('ZD02', 'contract/edit', 'contract/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('ZD02', 'contract/edit', 'contract/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['retire']; ?></td>
    <td><?php echo $this->record['local_type']; ?></td>
</tr>
