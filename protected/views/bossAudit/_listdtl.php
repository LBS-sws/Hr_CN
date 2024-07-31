<?php
if(isset($this->searchlinkparam["type"])&&$this->searchlinkparam["type"]==2){
    $assStr = "BA05";
}elseif(isset($this->searchlinkparam["type"])&&$this->searchlinkparam["type"]==3){
    $assStr = "BA06";
}else{
    $assStr = "BA03";
}
?>
<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink($assStr, 'bossAudit/edit', 'bossAudit/view', array('index'=>$this->record['id'],'type'=>$this->record['link_type']));?>'>


	<td><?php echo $this->drawEditButton($assStr, 'bossAudit/edit','bossAudit/view', array('index'=>$this->record['id'],'type'=>$this->record['link_type'])); ?></td>


    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city_name']; ?></td>
    <td><?php echo $this->record['audit_year']; ?></td>
    <td><?php echo $this->record['results_a']; ?>%</td>
    <td><?php echo $this->record['results_b']; ?>%</td>
    <td><?php echo $this->record['results_c']; ?></td>
    <td><?php echo $this->record['results_sum']; ?></td>
    <td><?php echo $this->record['status_type']; ?></td>
</tr>
