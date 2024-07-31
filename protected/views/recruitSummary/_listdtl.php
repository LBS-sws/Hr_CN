<?php
$labels = $this->model->attributeLabels();
$withrow = count($this->record['detail'])>0;
$idX = $this->record['city'];
?>
<tr>
    <td>
        <?php
        $iconX = $withrow ? "<span id='btn_$idX' class='fa fa-plus-square'></span>" : "<span class='fa fa-square'></span>";
        $lnkX = $withrow ? "javascript:showdetail('$idX');" : '#';
        echo TbHtml::link($iconX, $lnkX);
        ?>
    </td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['year']; ?></td>
	<td><?php echo $this->record['recruit_sum']; ?></td>
	<td><?php echo $this->record['now_sum']; ?></td>
	<td><?php echo $this->record['leave_sum']; ?></td>
	<td><?php echo $this->record['lack_sum']; ?></td>
	<td><?php echo $this->record['sum_rate']; ?></td>
</tr>

<?php

if (count($this->record['detail'])>0) {
    foreach ($this->record['detail'] as $row) {
        $tdId = $row["id"];
        $line = "<tr class='active detail_$idX' style='display:none;'>";
        $line.= "<td colspan=3 style='text-align: right;'><strong>{$row['leader_name']} - {$row['dept_name']}:&nbsp;</strong></td>";
        $line.= "<td>{$row['recruit_num']}</td>";
        $line.= "<td class='td-click' data-type='0' data-id='{$tdId}' >{$row['now_num']}</td>";
        $line.= "<td class='td-click' data-type='-1' data-id='{$tdId}' >{$row['leave_num']}</td>";
        $line.= "<td>{$row['lack_num']}</td>";
        $line.= "<td>{$row['completion_rate']}</td>";
        $line.= "</tr>";
        echo $line;
    }
}
?>