<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('AY05', 'supportEmail/edit', 'supportEmail/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->drawEditButton('AY05', 'supportEmail/edit',  'supportEmail/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['department']; ?></td>
    <td><?php echo $this->record['position']; ?></td>
    <td><?php echo $this->record['support_city']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
