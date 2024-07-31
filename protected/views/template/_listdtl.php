<tr class='clickable-row' data-href='<?php echo $this->getLink('RE05', 'template/edit', 'template/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('RE05', 'template/edit', 'template/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['tem_name']; ?></td>
    <td><?php echo $this->record['city_name']; ?></td>
</tr>
