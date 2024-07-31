<tr>
    <th></th>
    <th>
        <?php
        echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('employee_name'),'#',$this->createOrderLink('auditHoliday-list','employee_name'))
        ;
        ?>
    </th>
    <th>
        <?php
        echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('auditHoliday-list','city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->model->getTypeName().$this->getLabelName('holiday_name').$this->drawOrderArrow('holiday_name'),'#',$this->createOrderLink('auditHoliday-list','holiday_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('auditHoliday-list','start_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('auditHoliday-list','end_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('auditHoliday-list','status'))
        ;
        ?>
    </th>
</tr>
