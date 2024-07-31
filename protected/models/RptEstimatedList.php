<?php
class RptEstimatedList extends CReport {

    protected $sheetname="全国";
    private $year="2021";
    private $headList = array();
    private $bodyList = array();

	public function genReport() {
		$this->retrieveData();
		return $this->exportExcel();
	}

	public function retrieveData() {
	    $listX = BossSetAForm::getListX();
        $suffix = Yii::app()->params['envSuffix'];
        $this->year = $this->criteria['YEAR'];
        //相同城市保留最後申請的申請記錄
        $exprSql = " and (a.city,a.id) in (SELECT city,max(id) FROM hr_boss_audit where audit_year=:year GROUP BY city)";
        $rows = Yii::app()->db->createCommand()->select("a.json_text,b.name,c.name as city_name")->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->leftJoin("security{$suffix}.sec_city c","a.city=c.code")
            ->where("a.audit_year=:year $exprSql",array(":year"=>$this->year))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->headList[] = $row["city_name"];
                //$this->headList[] = $row["city_name"]."\n（{$row['name']}）";
                $json_text = json_decode($row["json_text"],true);
                foreach ($listX as $item){
                    if(!key_exists($item["value"],$this->bodyList)){
                        $this->bodyList[$item["value"]]=array(
                            "list"=>array(),
                            "show"=>1,
                            "sum"=>0,
                            "name"=>Yii::t("contract",$item["value"]),
                            "count"=>0
                        );
                    }
                    $value = "";
                    $num = 0;
                    if(is_array($json_text)&&key_exists($item["value"],$json_text)){
                        $value=$json_text[$item["value"]]["one_3"];
                        $num = floatval($value);
                    }
                    $this->bodyList[$item["value"]]["list"][] = $value;
                    $this->bodyList[$item["value"]]["sum"]+=$num;
                    $this->bodyList[$item["value"]]["count"]++;
                    if(in_array($item["value"],array("one_eight","one_seven","one_six"))){
                        $this->bodyList[$item["value"]]["show"]=0;
                    }
                }
            }
        }
		return true;
	}

    protected function setReportDefaultFormat() {
        $this->excel->getObjPHPExcel()->getDefaultStyle()->getFont()->setName('宋体');//设置默认的字体
        $this->excel->getObjPHPExcel()->getDefaultStyle()->getFont()->setSize(11);//设置默认的字体大小
    }

    private function setTextColor($j,$row="",$color="ff0000"){
        $row = empty($row)?$this->current_row:$row;
        $this->excel->getActiveSheet()->getStyleByColumnAndRow($j,$row)->getFont()->getColor()->setRGB($color);
    }

    protected function printHeader(){
	    $this->setReportDefaultFormat();
        $this->current_row = 1;
        $j = 1;
        $this->excel->getActiveSheet()->freezePane('B1');
        $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(39);
        $this->excel->writeCell($j, $this->current_row,"{$this->year}年大陆地区预计目标数据",array("align"=>"C","valign"=>"C"));
        $this->excel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true)->getColor()->setRGB('ff0000');

        $this->current_row++;
        $this->setTextForTop(0,"事项");
        $this->excel->setColWidth(0, 30);
        $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(55);
        if(!empty($this->headList)){
            foreach ($this->headList as $text){
                $this->setTextForTop($j,$text);
                $j++;
            }
        }
        $j = $j==1?2:$j;
        $str = PHPExcel_Cell::stringFromColumnIndex($j-1);
        $this->excel->getActiveSheet()->mergeCells("B1:{$str}1");
        $this->setTextForTop($j,"总和");
        $this->setTextColor($j);
        $j++;
        $this->setTextForTop($j,"平均数");
        $this->setTextColor($j);
    }

    private function setTextForTop($j,$text){
        $this->excel->writeCell($j, $this->current_row,$text,array("align"=>"C","valign"=>"C"));
        $this->excel->setColWidth($j, 15);
        $this->excel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
    }

    private function setTextForLeft($text){
        $this->excel->writeCell(0, $this->current_row,$text,array("align"=>"L","valign"=>"C"));
        $this->excel->getActiveSheet()->getStyleByColumnAndRow(0,$this->current_row)->getFont()->setBold(true);
    }

    private function setTextForNumber($j,$text,$bold=false){
        $this->excel->writeCell($j, $this->current_row,$text,array("align"=>"R","valign"=>"C"));
        if($bold){
            $this->excel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
        }
    }

    protected function printDetail(){
        foreach ($this->bodyList as $key=>$row){
            $this->current_row++;
            $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(34);
            $this->setTextForLeft($row["name"]);
            $j=1;
            foreach ($row["list"] as $number){
                $this->setTextForNumber($j,$number);
                $j++;
            }
            if($row["show"] == 1){
                $this->setTextForNumber($j,$row["sum"],true);
                $this->setTextColor($j);
            }
            $j++;
            $svg = empty($row["count"])?0:$row["sum"]/$row["count"];
            $svg = sprintf("%.2f",$svg);
            $this->setTextForNumber($j,$svg,true);
        }
    }
}
?>