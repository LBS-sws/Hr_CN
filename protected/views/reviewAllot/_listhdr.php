<tr>
    <th>  <input name="Fruit"  type="checkbox"  id="all"></th>
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
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('reviewAllot-list','a.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('reviewAllot-list','e.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('reviewAllot-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('a.entry_time'),'#',$this->createOrderLink('reviewAllot-list','a.entry_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('a.company_id'),'#',$this->createOrderLink('reviewAllot-list','a.company_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('review_type').$this->drawOrderArrow('d.review_type'),'#',$this->createOrderLink('reviewAllot-list','d.review_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('a.id'),'#',$this->createOrderLink('reviewAllot-list','a.id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year_type').$this->drawOrderArrow('a.id'),'#',$this->createOrderLink('reviewAllot-list','a.id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status_type'),'#',$this->createOrderLink('reviewAllot-list','status_type'))
        ;
        ?>
    </th>
    <th>
    </th>
</tr>
