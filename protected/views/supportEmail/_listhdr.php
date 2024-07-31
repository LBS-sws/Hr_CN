<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('supportEmail-list','a.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('supportEmail-list','a.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('supportEmail-list','e.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('supportEmail-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('support_city').$this->drawOrderArrow('support_city'),'#',$this->createOrderLink('supportEmail-list','support_city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('supportEmail-list','status'))
        ;
        ?>
    </th>
</tr>
