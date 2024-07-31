<tr class='clickable-row' data-href='<?php echo $this->getLink('ZD04', 'downForm/edit', 'downForm/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZD04', 'downForm/edit','downForm/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php
    echo TbHtml::link("<span class='glyphicon glyphicon-download-alt'></span>",Yii::app()->createUrl('downForm/downfile', array('index'=>$this->record['id'])),array("class"=>""));
    ?></td>
</tr>
