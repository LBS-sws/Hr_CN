<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('ZG02', 'auditHistory/edit', 'auditHistory/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('ZG02', 'auditHistory/edit', 'auditHistory/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['table_type']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['phone']; ?></td>
    <td><?php echo $this->record['department']; ?></td>
    <td><?php echo $this->record['position']; ?></td>
    <td><?php echo $this->record['entry_time']; ?></td>
    <td><?php echo $this->record['company_id']; ?></td>
    <td><?php echo Yii::t("contract",$this->record['operation']); ?></td>
    <td><?php echo $this->record['staff_status']; ?></td>
</tr>
