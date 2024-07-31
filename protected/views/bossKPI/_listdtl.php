<tr class='clickable-row' data-href='<?php echo $this->getLink('BA04', 'bossKPI/edit', 'bossKPI/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('BA04', 'bossKPI/edit','bossKPI/view', array('index'=>$this->record['id'])); ?></td>


    <td><?php echo $this->record['kpi_name']; ?></td>
    <td><?php echo $this->record['city_name']; ?></td>
    <td><?php echo $this->record['tacitly']; ?></td>
    <td><?php echo $this->record['sum_bool']; ?></td>
</tr>
