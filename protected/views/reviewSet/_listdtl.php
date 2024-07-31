<tr class='clickable-row' data-href='<?php echo $this->getLink('RE04', 'reviewSet/edit', 'reviewSet/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('RE04', 'reviewSet/edit', 'reviewSet/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['id']; ?></td>
    <td><?php echo $this->record['set_name']; ?></td>
    <td><?php echo $this->record['four_with']; ?></td>
    <td><?php echo $this->record['num_ratio']; ?></td>
    <td><?php echo $this->record['z_index']; ?></td>
    <td><?php echo $this->record['pro_num']; ?></td>
    <td><?php echo TbHtml::link("<span class='fa fa-ellipsis-h'></span>",Yii::app()->createUrl('reviewSetPro/index',array("type"=>$this->record['id'])),array("style"=>"padding:10px;")) ?></td>
</tr>
