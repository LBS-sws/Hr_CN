<tr class='clickable-row' data-href='<?php echo $this->getLink('CL01', 'company/edit', 'company/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('CL01', 'company/edit', 'company/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['head']; ?></td>
	<td><?php echo $this->record['agent']; ?></td>
	<td>
        <?php
        if ($this->record['tacitly'] == 1){
            echo Yii::t("contract","Tacitly Company");
        }else{
            echo "&nbsp;";
        }
        ?>
    </td>
    <td><?php echo $this->record['share_bool']; ?></td>
</tr>
