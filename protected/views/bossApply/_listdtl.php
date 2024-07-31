<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('BA01', 'bossApply/edit', 'bossApply/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('BA01', 'bossApply/edit','bossApply/view', array('index'=>$this->record['id'])); ?></td>


    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['audit_year']; ?></td>
    <td><?php echo $this->record['results_a']; ?>%</td>
    <td><?php echo $this->record['results_b']; ?>%</td>
    <td><?php echo $this->record['results_c']; ?></td>
    <td><?php echo $this->record['results_sum']; ?></td>
    <td><?php echo $this->record['status_type']; ?></td>
</tr>
