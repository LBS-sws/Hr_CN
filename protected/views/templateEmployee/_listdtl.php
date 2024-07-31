<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('RE06', 'templateEmployee/edit', 'templateEmployee/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->drawEditButton('RE06', 'templateEmployee/edit',  'templateEmployee/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['department']; ?></td>
    <td><?php echo $this->record['position']; ?></td>
    <td><?php echo $this->record['entry_time']; ?></td>
    <td><?php echo $this->record['company_id']; ?></td>
    <td><?php echo $this->record['review_type']; ?></td>
    <td><?php echo $this->record['audit_name']; ?></td>
    <td><?php echo $this->record['tem_name']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
