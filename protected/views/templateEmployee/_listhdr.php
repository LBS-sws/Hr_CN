<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('reviewAllot-list','a.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('reviewAllot-list','a.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('templateEmployee-list','a.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('templateEmployee-list','f.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('templateEmployee-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('a.entry_time'),'#',$this->createOrderLink('templateEmployee-list','a.entry_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('a.company_id'),'#',$this->createOrderLink('templateEmployee-list','a.company_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('review_type').$this->drawOrderArrow('d.review_type'),'#',$this->createOrderLink('templateEmployee-list','d.review_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('audit_name'),'#')
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('tem_name').$this->drawOrderArrow('tem_name'),'#',$this->createOrderLink('templateEmployee-list','tem_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status_type'),'#',$this->createOrderLink('templateEmployee-list','status_type'))
        ;
        ?>
    </th>
</tr>
