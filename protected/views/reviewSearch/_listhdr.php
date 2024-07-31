<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('c.code'),'#',$this->createOrderLink('reviewSearch-list','c.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('c.name'),'#',$this->createOrderLink('reviewSearch-list','c.name'))
        ;
        ?>
    </th>

    <?php if (!Yii::app()->user->isSingleCity()||Yii::app()->user->validFunction('ZR15')): ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('c.city'),'#',$this->createOrderLink('reviewSearch-list','c.city'))
        ;
        ?>
    </th>
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('reviewSearch-list','f.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('reviewSearch-list','e.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('c.entry_time'),'#',$this->createOrderLink('reviewSearch-list','c.entry_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('c.company_id'),'#',$this->createOrderLink('reviewSearch-list','c.company_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('review_type').$this->drawOrderArrow('b.review_type'),'#',$this->createOrderLink('reviewSearch-list','b.review_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('b.year'),'#',$this->createOrderLink('reviewSearch-list','b.year'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year_type').$this->drawOrderArrow('b.year_type'),'#',$this->createOrderLink('reviewSearch-list','b.year_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name_list').$this->drawOrderArrow('b.name_list'),'#',$this->createOrderLink('reviewSearch-list','b.name_list'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('review_sum').$this->drawOrderArrow('b.review_sum'),'#',$this->createOrderLink('reviewSearch-list','b.review_sum'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('b.status_type'),'#',$this->createOrderLink('reviewSearch-list','b.status_type'))
        ;
        ?>
    </th>
    <th>
    </th>
</tr>
