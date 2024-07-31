<tr class='clickable-row <?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink('TH02', 'treatyStop/edit', 'treatyStop/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('TH02', 'treatyStop/edit', 'treatyStop/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['treaty_code']; ?></td>
	<td><?php echo $this->record['treaty_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['treaty_num']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['start_date']; ?></td>
    <td><?php echo $this->record['end_date']; ?></td>
    <td><?php echo $this->record['state_type']; ?></td>
    <?php if (Yii::app()->user->validFunction('ZR21')): ?>
        <td><?php echo $this->record['lcu']; ?></td>
    <?php endif ?>
</tr>
