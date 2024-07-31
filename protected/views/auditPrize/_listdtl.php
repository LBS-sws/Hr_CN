<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('ZG07', 'auditPrize/edit', 'auditPrize/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->drawEditButton('ZG07', 'auditPrize/edit', 'auditPrize/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['prize_pro']; ?></td>
    <td><?php echo $this->record['contact']; ?></td>
    <td><?php echo $this->record['phone']; ?></td>
    <td><?php echo $this->record['prize_date']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
