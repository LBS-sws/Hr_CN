<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('pro_name').$this->drawOrderArrow('pro_name'),'#',$this->createOrderLink('tripMoneySet-list','pro_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('z_display').$this->drawOrderArrow('z_display'),'#',$this->createOrderLink('tripMoneySet-list','z_display'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('z_index'),'#',$this->createOrderLink('tripMoneySet-list','z_index'))
        ;
        ?>
    </th>
</tr>
