<?php
class RptBossPlanList extends CReport {

    protected $sheetname="无";
    private $city="HK";
    public $year="2021";
    public $month=0;
    public $userName="";
    public $cityName="";
    private $headList = array();
    private $bodyList = array();

	public function genReport($bool=true) {
	    if($bool){
            $this->retrieveData();
        }
		return $this->exportExcel();
	}

	public function setBodyList($list){
	    $this->bodyList = $list;
    }

	protected function retrieveData() {
        $this->year = $this->criteria['YEAR'];
        $this->month = $this->criteria['MONTH'];
        $this->city = $this->criteria['CITY'];
        $this->cityName = $this->criteria['CITY_NAME'];

        $bossModel = new BossSearchForm();
        $bool = $bossModel->setDataToCityAndYear($this->city,$this->year);
        if($bool){
            $this->userName = $bossModel->name." - ".$bossModel->code;
            //$this->cityName = CGeneral::getCityName($city);
            $list = array(
                array("name"=>"（A） 目标订立部分","class"=>"BossReviewA","width"=>"auto","colspan"=>8),
                array("name"=>"（B） 其他细节部分","class"=>"BossReviewB","width"=>"auto","colspan"=>6),
                array("name"=>"（C） 自选项目部分","class"=>"BossReviewC","width"=>"800px","colspan"=>4)
            );
            foreach ($list as $key=>$item){
                $className = $item["class"];
                $bossReviewModel = new $className($bossModel,true);
                $bossReviewModel->resetListX($bossModel->json_listX);
                $bossReviewModel->search_month = $this->month;
                $bossReviewModel->getTableHtmlToEmail();
                $this->bodyList[$key]["title"]=$item["name"];
                $this->bodyList[$key]["list"]=$bossReviewModel->getDetailList();
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
	    if(!empty($this->bodyList)){
            $this->current_row = 0;
            $sheetIndex = 0;
	        foreach ($this->bodyList as $sheet){
	            if(!empty($sheetIndex)){
                    $this->excel->createSheet();
                }
                $this->excel->setActiveSheet($sheetIndex);
                $sheetIndex++;
                $this->insertPublicHeader($sheet,$sheetIndex);
                if($sheetIndex!=3){
                    $this->insertBody($sheet);
                }else{
                    $this->insertBodyThree($sheet);//C部分
                }
            }
        }
    }

    private function insertBodyThree($sheet){
        if(!empty($sheet["list"])) {
            $startRow = $this->current_row+1;
            foreach ($sheet["list"] as $bodyKey => $bodyList) {
                $this->current_row++;
                $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
                $j=0;
                if($bodyKey=="footer"){
                    $this->excel->writeCell(0, $this->current_row,$bodyList[0],array("align"=>"R","valign"=>"C"));
                    $this->excel->writeCell(3, $this->current_row,$bodyList[1],array("align"=>"L","valign"=>"C"));
                    $this->mergeForNumber(0,2);
                }else{
                    foreach ($bodyList as $value){
                        $this->excel->setColWidth($j, 30);
                        $this->excel->writeCell($j, $this->current_row,$value,array("align"=>"L","valign"=>"C"));
                        if($bodyKey=="title"){
                            $this->excel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
                        }
                        $j++;
                    }
                }
            }
            $endRow = $this->current_row;
            $style_array = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                ) );
            $this->excel->getActiveSheet()->getStyle("A{$startRow}:D{$endRow}")->applyFromArray($style_array);
        }
    }
    private function insertBody($sheet){
        if(!empty($sheet["list"])){
            foreach ($sheet["list"] as $bodyKey=> $bodyList){
                $this->current_row++;
                $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
                $j=0;
                $listCount = 0;
                if(key_exists("list",$bodyList)&&!empty($bodyList["list"])){
                    foreach ($bodyList["list"] as $key=>$row){
                        $this->excel->setColWidth($j, 20);
                        $this->excel->writeCell($j, $this->current_row,$row,array("align"=>"C","valign"=>"C"));

                        if($bodyKey=="title"||$key==0){
                            $this->excel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
                        }
                        $j++;
                    }
                    $listCount = count($bodyList["list"]);
                }
                if(key_exists("info",$bodyList)&&!empty($bodyList["info"])){
                    foreach ($bodyList["info"] as $row){
                        $j=$listCount+$row["num"];
                        $this->excel->setColWidth($j, 10);
                        $this->excel->writeCell($j, $this->current_row,$row["text"],array("align"=>"L","valign"=>"C"));
                        $this->mergeForNumber($j,$row["len"]);
                        if($bodyKey=="title"){
                            $this->excel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
                        }
                    }
                }
            }
        }
    }

    private function mergeForNumber($startNum,$len){
        if(!empty($len)){
            $start=PHPExcel_Cell::stringFromColumnIndex($startNum);
            $start.=$this->current_row;
            $end=PHPExcel_Cell::stringFromColumnIndex($startNum+$len);
            $end.=$this->current_row;
            $this->excel->getActiveSheet()->mergeCells("{$start}:{$end}");
        }
    }

    private function insertPublicHeader($sheet,$index){
        $this->current_row = 1;
        $this->excel->getActiveSheet()->setTitle($sheet["title"]);
        if($index!=3){
            if($index==1){
                $this->excel->getActiveSheet()->freezePane('G6');
            }else{
                $this->excel->getActiveSheet()->freezePane('F6');
            }
        }
        $this->setTextForTop(1,"年份：{$this->year}   月份：1月至{$this->month}月");
        $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
        $this->current_row++;
        $this->setTextForTop(1,"员工：{$this->userName}");
        $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
        $this->current_row++;
        $this->setTextForTop(1,"城市：{$this->cityName}");
        $this->excel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
        $this->current_row++;
        $this->excel->getActiveSheet()->mergeCells('B1:D1');
        $this->excel->getActiveSheet()->mergeCells('B2:D2');
        $this->excel->getActiveSheet()->mergeCells('B3:D3');
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
        return;
    }
}
?>