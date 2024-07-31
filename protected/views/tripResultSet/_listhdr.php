<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('pro_name').$this->drawOrderArrow('pro_name'),'#',$this->createOrderLink('tripResultSet-list','pro_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('pro_num').$this->drawOrderArrow('pro_num'),'#',$this->createOrderLink('tripResultSet-list','pro_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('z_display').$this->drawOrderArrow('z_display'),'#',$this->createOrderLink('tripResultSet-list','z_display'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('z_index'),'#',$this->createOrderLink('tripResultSet-list','z_index'))
        ;
        ?>
    </th>
</tr>
