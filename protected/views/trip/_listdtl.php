<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('ZA10', 'trip/edit', 'trip/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZA10', 'trip/edit', 'trip/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['trip_code']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city_name']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['trip_address']; ?></td>
    <td><?php echo $this->record['trip_cost']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
