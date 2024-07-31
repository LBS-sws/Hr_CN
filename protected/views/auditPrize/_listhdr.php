<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('prize-list','b.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('prize-list','b.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('prize-list','b.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_pro').$this->drawOrderArrow('a.prize_pro'),'#',$this->createOrderLink('prize-list','a.prize_pro'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('contact').$this->drawOrderArrow('a.contact'),'#',$this->createOrderLink('prize-list','a.contact'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('phone').$this->drawOrderArrow('a.phone'),'#',$this->createOrderLink('prize-list','a.phone'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_date').$this->drawOrderArrow('a.prize_date'),'#',$this->createOrderLink('prize-list','a.prize_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('prize-list','a.status'))
        ;
        ?>
    </th>
</tr>
