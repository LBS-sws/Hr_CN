<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('rewardCon-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('money').$this->drawOrderArrow('money'),'#',$this->createOrderLink('rewardCon-list','money'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods').$this->drawOrderArrow('goods'),'#',$this->createOrderLink('rewardCon-list','goods'))
        ;
        ?>
    </th>
</tr>
