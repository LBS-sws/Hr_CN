<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('c.code'),'#',$this->createOrderLink('reviewHandle-list','c.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('c.name'),'#',$this->createOrderLink('reviewHandle-list','c.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('c.city'),'#',$this->createOrderLink('reviewHandle-list','c.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('reviewHandle-list','f.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('reviewHandle-list','e.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('c.entry_time'),'#',$this->createOrderLink('reviewHandle-list','c.entry_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('c.company_id'),'#',$this->createOrderLink('reviewHandle-list','c.company_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('b.year'),'#',$this->createOrderLink('reviewHandle-list','b.year'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year_type').$this->drawOrderArrow('b.year_type'),'#',$this->createOrderLink('reviewHandle-list','b.year_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status_type'),'#',$this->createOrderLink('reviewHandle-list','a.status_type'))
        ;
        ?>
    </th>
    <th>
    </th>
</tr>
