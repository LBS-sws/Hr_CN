<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('configOffice-list','f.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('configOffice-list','a.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('u_id').$this->drawOrderArrow('a.u_id'),'#',$this->createOrderLink('configOffice-list','a.u_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('z_display').$this->drawOrderArrow('a.z_display'),'#',$this->createOrderLink('configOffice-list','a.z_display'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('office_sum').$this->drawOrderArrow('office_sum'),'#',$this->createOrderLink('configOffice-list','office_sum'))
        ;
        ?>
    </th>
</tr>
