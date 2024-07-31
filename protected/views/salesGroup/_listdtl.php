
<tr class='clickable-row' data-href='<?php echo $this->getLink('SR01', 'salesGroup/staff', 'salesGroup/staff', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('SR01', 'salesGroup/staff', 'salesGroup/staff', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['group_name']; ?></td>

    <td><?php echo $this->record['staff_num']; ?></td>


    <?php if (Yii::app()->user->validFunction('ZR14')): ?>
        <td>
            <?php
            echo TbHtml::link("<span class='fa fa-ellipsis-h'></span>",Yii::app()->createUrl('salesGroup/edit',array(
                    "index"=>$this->record['id'])),array("style"=>"padding:10px;"
            )); ?>
        </td>
    <?php endif; ?>
</tr>
