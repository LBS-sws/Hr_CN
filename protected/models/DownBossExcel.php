<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/3/14 0014
 * Time: 11:57
 */
class DownBossExcel{

    protected $objPHPExcel;

    protected $current_row = 0;
    protected $year;
    protected $user_name;
    protected $city_name;

    public function SetYear($invalue) {
        $this->year = $invalue;
    }

    public function SetUserName($invalue) {
        $this->user_name = $invalue;
    }

    public function SetCityName($invalue) {
        $this->city_name = $invalue;
    }

    public function init() {
        //Yii::$enableIncludePath = false;
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
        spl_autoload_unregister(array('YiiBase','autoload'));
        include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->objPHPExcel = new PHPExcel();
        $this->setReportFormat();
        //$this->outHeader();
    }

    public function setBossExcelBody($downData){
        $list = array(
            array("name"=>"（A） 目标订立部分","class"=>"BossReviewA","endStr"=>"N","width"=>"20","height"=>"25"),
            array("name"=>"（B） 其他细节部分","class"=>"BossReviewB","endStr"=>"K","width"=>"20","height"=>"25"),
            array("name"=>"（C） 自选项目部分","class"=>"BossReviewC","endStr"=>"D","width"=>"54","height"=>"146")
        );
        $this->current_row = 0;
        $sheetIndex = 0;
        foreach ($list as $sheetRow){
            if(!empty($sheetIndex)){
                $this->objPHPExcel->createSheet();
            }
            $this->objPHPExcel->setActiveSheetIndex($sheetIndex);
            $sheetIndex++;
            $this->insertPublicHeader($sheetRow["name"],$sheetIndex);
            $body = key_exists($sheetRow["class"],$downData)?$downData[$sheetRow["class"]]:array();

            $this->insertBody($body,$sheetRow);
        }
    }

    private function insertBody($sheet,$sheetRow){
        if(!empty($sheet)){
            $startRow = $this->current_row+1;
            foreach ($sheet as $key =>$list){
                $this->current_row++;
                $height = $key=="count"||$key=="title"?25:$sheetRow["height"];
                $this->objPHPExcel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight($height);
                $j=0;
                foreach ($list as $numKey=>$item){
                    $width = $sheetRow["class"]=="BossReviewC"&&$numKey==count($list)-1?10:$sheetRow["width"];
                    if($key=="title"){
                        $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($j)->setWidth($width);
                        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $this->current_row,$item);

                        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
                    }elseif($key=="count"){
                        if($j==0){
                            $this->mergeForNumber(0,1);
                            $j+=2;
                        }
                        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $this->current_row,$item);
                        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
                        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    }else{
                        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $this->current_row,$item);
                        if($sheetRow["class"]!="BossReviewC"){
                            $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        }
                    }
                    $j++;
                }
            }
            $endRow = $this->current_row;
            $style_array = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                ) );
            $this->objPHPExcel->getActiveSheet()->getStyle("A{$startRow}:{$sheetRow["endStr"]}{$endRow}")->applyFromArray($style_array);
        }
    }

    private function mergeForNumber($startNum,$len){
        if(!empty($len)){
            $start=PHPExcel_Cell::stringFromColumnIndex($startNum);
            $start.=$this->current_row;
            $end=PHPExcel_Cell::stringFromColumnIndex($startNum+$len);
            $end.=$this->current_row;
            $this->objPHPExcel->getActiveSheet()->mergeCells("{$start}:{$end}");
        }
    }

    private function insertPublicHeader($title,$index){
        $this->current_row = 1;
        $this->objPHPExcel->getActiveSheet()->setTitle($title);
        if($index!=3){
            if($index==1){
                $this->objPHPExcel->getActiveSheet()->freezePane('G6');
            }else{
                $this->objPHPExcel->getActiveSheet()->freezePane('F6');
            }
        }
        $this->setTextForTop(1,"年份：{$this->year}年");
        $this->objPHPExcel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
        $this->current_row++;
        $this->setTextForTop(1,"员工：{$this->user_name}");
        $this->objPHPExcel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
        $this->current_row++;
        $this->setTextForTop(1,"城市：{$this->city_name}");
        $this->objPHPExcel->getActiveSheet()->getRowDimension($this->current_row)->setRowHeight(25);
        $this->current_row++;
        $this->objPHPExcel->getActiveSheet()->mergeCells('B1:D1');
        $this->objPHPExcel->getActiveSheet()->mergeCells('B2:D2');
        $this->objPHPExcel->getActiveSheet()->mergeCells('B3:D3');
    }

    private function setTextForTop($j,$text){
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $this->current_row,$text);
        $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($j)->setWidth(15);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j,$this->current_row)->getFont()->setBold(true);
    }

    protected function setReportFormat() {
        $this->objPHPExcel->getDefaultStyle()->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->objPHPExcel->getDefaultStyle()->getFont()
            ->setSize(10);
        $this->objPHPExcel->getDefaultStyle()->getAlignment()
            ->setWrapText(true);
        $this->objPHPExcel->getActiveSheet()->getDefaultRowDimension()
            ->setRowHeight(-1);
    }

    protected function outHeader($sheetid=0){
        $this->objPHPExcel->setActiveSheetIndex($sheetid)
            ->setCellValueByColumnAndRow(0, 1, $this->header_title)
            ->setCellValueByColumnAndRow(0, 2, $this->header_string)
            ->setCellValueByColumnAndRow(0, 3, $this->city_name);
        $this->objPHPExcel->getActiveSheet()->mergeCells("A1:C1");
        $this->objPHPExcel->getActiveSheet()->mergeCells("A2:C2");
        $this->objPHPExcel->getActiveSheet()->mergeCells("A3:C3");
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getFont()
            ->setSize(14)
            ->setBold(true);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getAlignment()
            ->setWrapText(false);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getFont()
            ->setSize(12)
            ->setBold(true)
            ->setItalic(true);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getAlignment()
            ->setWrapText(false);

        $this->current_row = 5;
    }

    public function outExcel($name="summary"){
        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $output = ob_get_clean();
        spl_autoload_register(array('YiiBase','autoload'));
        $time=time();
        $str="{$name}_".$time.".xlsx";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$str.'"');
        header("Content-Transfer-Encoding:binary");
        echo $output;
    }

    protected function setHeaderStyleTwo($cells,$color="AFECFF") {
        $styleArray = array(
            'font'=>array(
                'bold'=>true,
            ),
            'alignment'=>array(
                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders'=>array(
                'allborders'=>array(
                    'style'=>PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill'=>array(
                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor'=>array(
                    'argb'=>$color,
                ),
            ),
        );
        $this->objPHPExcel->getActiveSheet()->getStyle($cells)
            ->applyFromArray($styleArray);
    }
    protected function getColumn($index){
        $index++;
        $mod = $index % 26;
        $quo = ($index-$mod) / 26;

        if ($quo == 0) return chr($mod+64);
        if (($quo == 1) && ($mod == 0)) return 'Z';
        if (($quo > 1) && ($mod == 0)) return chr($quo+63).'Z';
        if ($mod > 0) return chr($quo+64).chr($mod+64);
    }
}