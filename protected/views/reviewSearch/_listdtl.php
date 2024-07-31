<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('RE03', 'reviewSearch/view', 'reviewSearch/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->needHrefButton('RE03', 'reviewSearch/view', 'view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <?php if (!Yii::app()->user->isSingleCity()||Yii::app()->user->validFunction('ZR15')): ?>
    <td><?php echo $this->record['city']; ?></td>
    <?php endif ?>
    <td><?php echo $this->record['department']; ?></td>
    <td><?php echo $this->record['position']; ?></td>
    <td><?php echo $this->record['entry_time']; ?></td>
    <td><?php echo $this->record['company_id']; ?></td>
    <td><?php echo $this->record['review_type']; ?></td>
    <td><?php echo $this->record['year']; ?></td>
    <td><?php echo $this->record['year_type']; ?></td>
    <td><?php echo $this->record['name_list']; ?></td>
    <td><?php echo $this->record['review_sum']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
    <td>
        <?php if (!empty($this->record['reviewdoc'])): ?>
            <span class="fa fa-paperclip"></span>
        <?php endif; ?>
    </td>
</tr>
