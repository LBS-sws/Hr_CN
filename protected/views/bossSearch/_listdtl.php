<tr class='clickable-row' data-href='<?php echo $this->getLink('BA02', 'bossSearch/edit', 'bossSearch/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('BA02', 'bossSearch/edit','bossSearch/view', array('index'=>$this->record['id'])); ?></td>


    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city_name']; ?></td>
    <td><?php echo $this->record['audit_year']; ?></td>
    <td><?php echo $this->record['results_a']; ?></td>
    <td><?php echo $this->record['results_b']; ?></td>
    <td><?php echo $this->record['results_c']; ?></td>
    <td><?php echo $this->record['results_sum']; ?></td>
</tr>
