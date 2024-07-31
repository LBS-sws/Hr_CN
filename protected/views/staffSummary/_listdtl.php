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
	<td><?php echo $this->record['staff_sum']; ?></td>
	<td><?php echo $this->record['leave_sum']; ?></td>
    <td><?php echo $this->record['now_sum']; ?></td>
	<td><?php echo $this->record['leave_rate']; ?></td>
</tr>

<?php

if (count($this->record['detail'])>0) {
    foreach ($this->record['detail'] as $row) {
        $line = "<tr class='click_detail active detail_$idX' data-city_name='{$this->record['city_name']}' data-city='{$this->record['city']}' data-id='{$row['id']}' data-name='{$row['leader_name']}' style='display:none;'>";
        $line.= "<td colspan=3 style='text-align: right;'><strong>{$row['leader_name']}:&nbsp;</strong></td>";
        $line.= "<td>{$row['staff_sum']}</td>";
        $line.= "<td>{$row['leave_sum']}</td>";
        $line.= "<td>{$row['now_sum']}</td>";
        $line.= "<td>{$row['leave_rate']}</td>";
        $line.= "</tr>";
        echo $line;
    }
}
?>