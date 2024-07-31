<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/3/14 0014
 * Time: 11:57
 */
class DownArrExcel{

    protected $objPHPExcel;

    protected $current_row = 0;
    protected $header_title;
    protected $header_string;
    public $colTwo=2;
    public $th_num=0;

    public function SetHeaderTitle($invalue) {
        $this->header_title = $invalue;
    }

    public function SetHeaderString($invalue) {
        $this->header_string = $invalue;
    }

    public function init() {
        //Yii::$enableIncludePath = false;
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
        include_once($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->objPHPExcel = new PHPExcel();
        $this->setReportFormat();
        $this->outHeader();
    }

    private function setCellValueForSummary($col,$row,$text){
        $this->objPHPExcel->getActiveSheet()
            ->setCellValueByColumnAndRow($col, $row, $text);
        if (strpos($text,'%')!==false){
            $number = floatval($text);
            if($number>=100){
                $str = $this->getColumn($col);
                $this->objPHPExcel->getActiveSheet()
                    ->getStyle($str.$row)->applyFromArray(
                        array(
                            'font'=>array(
                                'color'=>array('rgb'=>'00a65a')
                            )
                        )
                    );
            }
        }
    }

    protected function setReportFormat() {
        $this->objPHPExcel->getDefaultStyle()->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
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
            ->setCellValueByColumnAndRow(0, 2, $this->header_string);
        $this->objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
        $height = $this->colTwo==2?20:50;
        $this->objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight($height);
        $this->objPHPExcel->getActiveSheet()->mergeCells("A1:C1");
        $this->objPHPExcel->getActiveSheet()->mergeCells("A2:C2");
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
            ->setWrapText(true);

        $this->current_row = 4;
    }

    public function outExcel($name="summary"){
        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $output = ob_get_clean();
        spl_autoload_register(array('YiiBase','autoload'));
        $filename= iconv('utf-8','gbk//ignore',$name);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$filename.'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        echo $output;
    }

    protected function setHeaderStyleTwo($cells,$background="AFECFF",$textColor="000000") {
        $styleArray = array(
            'font'=>array(
                'bold'=>true,
                'color'=>array(
                    'argb'=>$textColor,
                )
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
                    'argb'=>$background,
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

    private function setWidth(){
        for ($col=0;$col<$this->th_num;$col++){
            $width=20;
            $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setWidth($width);
        }
    }

    public function setHeight($row,$height){
        $this->objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($height);

    }

    public function setHeaderForOneList($headerArr){
        $this->th_num=0;
        if(!empty($headerArr)){
            $colOne = 0;
            foreach ($headerArr as $list){
                $background="FFFFFF";
                $textColor="000000";
                $oneStr = $this->getColumn($colOne);
                if(key_exists("background",$list)){
                    $background = $list["background"];
                    $background = end(explode("#",$background));
                }
                if(key_exists("color",$list)){
                    $textColor = $list["color"];
                    $textColor = end(explode("#",$textColor));
                }
                if(key_exists("width",$list)){
                    $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($colOne)->setWidth($list['width']);
                }
                $this->objPHPExcel->getActiveSheet()
                    ->setCellValueByColumnAndRow($colOne, $this->current_row, $list["name"]);
                $colOne++;
                $this->th_num++;
                $endStr = $this->getColumn($colOne-1);
                $this->setHeaderStyleTwo("{$oneStr}{$this->current_row}:{$endStr}".$this->current_row,$background,$textColor);
                //$colOne++;
                $this->objPHPExcel->getActiveSheet()->getStyle("A{$this->current_row}:{$endStr}".$this->current_row)->applyFromArray(
                    array(
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
                        )
                    )
                );
            }

            $this->current_row++;
        }
        //$this->setWidth();
    }

    public function setArrData($data){
        if(!empty($data)){
            $startTr = $this->current_row;
            foreach ($data as $row){
                $td = 0;
                $tr = $this->current_row;
                foreach ($row as $key=>$item){
                    $text = $item;
                    $this->setCellValueForSummary($td, $tr, $text);
                    $td++;
                }
                $this->current_row++;
            }

            $endStr = $this->getColumn($this->th_num-1);
            $this->objPHPExcel->getActiveSheet()
                ->getStyle("A{$startTr}:{$endStr}".($this->current_row-1))
                ->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    )
                );
        }
    }

    public function setGroupData($data,$group){
        if(!empty($data)){
            $startTr = $this->current_row;
            foreach ($data as $row){
                $td = 0;
                $tr = $this->current_row;
                $maxTr=1;
                foreach ($group as $key=>$item){
                    if(is_array($item)){//
                        $groupList = key_exists($key,$row)?$row[$key]:array();
                        if($maxTr<count($groupList)){
                            $maxTr = count($groupList);
                        }
                        $groupTr = $tr;
                        $groupTd = $td;
                        if(!empty($groupList)){
                            foreach ($groupList as $oup){
                                $gd = $groupTd;
                                foreach ($item as $tm){
                                    $text = key_exists($tm,$oup)?$oup[$tm]:"";
                                    $this->setCellValueForSummary($gd, $groupTr, $text);
                                    $gd++;
                                }
                                $groupTr++;
                            }
                        }
                        $td+=count($item);

                        if($tr!==$groupTr){//合并
                            //$startStr = $this->getColumn($td);
                            //$this->objPHPExcel->getActiveSheet()->mergeCells($startStr.$this->current_row.':'.$startStr.($this->current_row+2));
                        }
                    }else{
                        $text = key_exists($item,$row)?$row[$item]:"";
                        $this->setCellValueForSummary($td, $tr, $text);
                        $td++;
                    }
                }
                $this->current_row+=$maxTr;
            }

            $endStr = $this->getColumn($this->th_num-1);
            $this->objPHPExcel->getActiveSheet()
                ->getStyle("A{$startTr}:{$endStr}".($this->current_row-1))
                ->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    )
                );
        }
    }

    public function setHeaderForErrorList($headerArr){
        $this->th_num=0;
        if(!empty($headerArr)){
            $colOne = 0;
            foreach ($headerArr as $list){
                if(is_array($list)){
                    $background="FFFFFF";
                    $textColor="000000";
                    $oneStr = $this->getColumn($colOne);
                    if(key_exists("background",$list)){
                        $background = $list["background"];
                        $background = end(explode("#",$background));
                    }
                    if(key_exists("color",$list)){
                        $textColor = $list["color"];
                        $textColor = end(explode("#",$textColor));
                    }
                    if(key_exists("width",$list)){
                        $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($colOne)->setWidth($list['width']);
                    }
                    $this->objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow($colOne, $this->current_row, $list["name"]);
                    $colOne++;
                    $this->th_num++;
                    $endStr = $this->getColumn($colOne-1);
                    $this->setHeaderStyleTwo("{$oneStr}{$this->current_row}:{$endStr}".$this->current_row,$background,$textColor);
                    //$colOne++;
                    $this->objPHPExcel->getActiveSheet()->getStyle("A{$this->current_row}:{$endStr}".$this->current_row)->applyFromArray(
                        array(
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
                            )
                        )
                    );
                }else{
                    $this->objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow($colOne, $this->current_row, $list);
                    $colOne++;
                    $this->th_num++;
                    $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($colOne)->setWidth(14);
                }
            }

            $this->current_row++;
        }
        //$this->setWidth();
    }


    public function setErrorData($data,$error){
        if(!empty($data)){
            $startTr = $this->current_row;
            foreach ($data as $row){
                $td = 0;
                $tr = $this->current_row;
                foreach ($error as $item){
                    $key = $item["sql"];
                    $errorText = key_exists($key,$row)?$row[$key]:"";
                    $this->setCellValueForSummary($td, $tr, $errorText);
                    $td++;
                    unset($row[$key]);
                }

                foreach ($row as $bodyText){
                    $this->setCellValueForSummary($td, $tr, $bodyText);
                    $td++;
                }

                $this->current_row++;
            }

            $endStr = $this->getColumn($this->th_num-1);
            $this->objPHPExcel->getActiveSheet()
                ->getStyle("A{$startTr}:{$endStr}".($this->current_row-1))
                ->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    )
                );
        }
    }
}