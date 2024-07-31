
<tr class='clickable-row' data-href='<?php echo $this->getLink($this->record['acc'], 'dept/edit', 'dept/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton($this->record['acc'], 'dept/edit', 'dept/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['z_index']; ?></td>
    <?php
    if($this->model->type == 1){
        echo "<td>";
        echo $this->record['dept_class'];
        echo "</td>";
        echo "<td>";
        echo $this->record['review_status'];
        echo "</td>";
        echo "<td>";
        echo $this->record['review_type'];
        echo "</td>";
        echo "<td>";
        echo $this->record['manager_type'];
        echo "</td>";
        echo "<td>";
        echo $this->record['manager_leave'];
        echo "</td>";
        echo "<td>";
        echo $this->record['level_type'];
        echo "</td>";
    }
    ?>
</tr>
