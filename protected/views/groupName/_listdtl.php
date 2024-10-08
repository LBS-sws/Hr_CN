<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC24', 'groupName/staff', 'groupName/staff', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZC24', 'groupName/staff', 'groupName/staff', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['group_code']; ?></td>
	<td><?php echo $this->record['group_name']; ?></td>
	<td><?php echo $this->record['group_remark']; ?></td>
	<td><?php echo $this->record['group_sum']; ?></td>

    <?php if (Yii::app()->user->validFunction('ZC24')): ?>
        <td>
            <?php
            echo TbHtml::link("<span class='fa fa-ellipsis-h'></span>",Yii::app()->createUrl('groupName/edit',array(
                "index"=>$this->record['id'])),array("style"=>"padding:10px;"
            )); ?>
        </td>
    <?php endif; ?>
</tr>
