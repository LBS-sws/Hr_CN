<?php

class RecruitApplyList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'year'=>Yii::t('recruit','year'),
			'city'=>Yii::t('recruit','city'),
			'leader_name'=>Yii::t('recruit','leader name'),
			'dept_name'=>Yii::t('recruit','dept name'),
			'recruit_num'=>Yii::t('recruit','recruit num'),
			'now_num'=>Yii::t('recruit','now num'),
			'leave_num'=>Yii::t('recruit','leave num'),
			'lack_num'=>Yii::t('recruit','lack num'),
			'completion_rate'=>Yii::t('recruit','completion rate'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.name as city_name,f.name as dept_name,g.name as leader_name 
				from hr_recruit a 
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				LEFT JOIN hr_dept f ON a.dept_id=f.id 
				LEFT JOIN hr_dept g ON f.dept_id=g.id 
				where a.city='{$city}' 
			";
		$sql2 = "select count(a.id)
				from hr_recruit a 
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				LEFT JOIN hr_dept f ON a.dept_id=f.id 
				LEFT JOIN hr_dept g ON f.dept_id=g.id 
				where a.city='{$city}' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'year':
					$clause .= General::getSqlConditionClause('a.year',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'dept_name':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
				case 'leader_name':
					$clause .= General::getSqlConditionClause('g.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $arr = self::recruitLoading($record);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'year'=>$record['year'],
                    'city'=>$record['city_name'],
                    'dept_id'=>$record['dept_id'],
                    'recruit_num'=>$record['recruit_num'],
                    'dept_name'=>$record['dept_name'],
                    'leader_name'=>$record['leader_name'],
                    'now_num'=>$arr['now_num'],
                    'leave_num'=>$arr['leave_num'],
                    'lack_num'=>$arr['lack_num'],
                    'completion_rate'=>$arr['completion_rate'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['recruitApply_c01'] = $this->getCriteria();
		return true;
	}

    public static function recruitLoading($record){
        $arr=array(
            'now_num'=>0,
            'leave_num'=>0,
            'lack_num'=>0,
            'completion_rate'=>0,
            'staff_list'=>array()
        );
        $rows = Yii::app()->db->createCommand()->select("id,staff_status")->from("hr_employee")
            ->where("position=:position and entry_time like'{$record['year']}%' and staff_status!=1",array(":position"=>$record["dept_id"]))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr["staff_list"][]=$row["id"];
                $arr["now_num"]++;
                if($row["staff_status"]==-1){
                    $arr["leave_num"]++;
                }
            }
        }
        $arr["lack_num"] = $record["recruit_num"] - ($arr["now_num"]-$arr["leave_num"]);
        $arr["completion_rate"] = empty($record["recruit_num"])?"error":round(($arr["now_num"]-$arr["leave_num"])/$record["recruit_num"],2)*100;
        $arr["completion_rate"].= empty($record["recruit_num"])?"":"%";
        return $arr;
    }

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $id = key_exists("id",$_GET)?$_GET["id"]:0;
        $type = key_exists("type",$_GET)?$_GET["type"]:0;
        $row = Yii::app()->db->createCommand()
            ->select("a.id,a.dept_id,a.year,a.city,f.name as dept_name")
            ->from("hr_recruit a")
            ->leftJoin("hr_dept f","a.dept_id=f.id ")
            ->where("a.id=:id",array(":id"=>$id))->queryRow();
        if(!$row){
            return array('html'=>"<p>数据异常，请刷新重试</p>",'title'=>"");
        }
        $title = CGeneral::getCityName($row["city"])." ({$row["year"]}) - ".$row["dept_name"];
        $html = "<table class='table table-bordered table-striped table-hover'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","Employee Code")."</th>";
        $html.="<th>".Yii::t("contract","Employee Name")."</th>";
        $html.="<th>".Yii::t("contract","Position")."</th>";
        $html.="<th>".Yii::t("contract","Entry Time")."</th>";
        $html.="</tr></thead><tbody>";
        if($type==-1){//離職
            $whereSql = " and staff_status=-1";
            $title.=" (".Yii::t('recruit','leave num').")";
        }else{
            $whereSql = "";
            $title.=" (".Yii::t('recruit','now num').")";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.entry_time,a.code,a.name")
            ->from("hr_employee a")
            ->where("position=:position {$whereSql} and entry_time like'{$row['year']}%' and staff_status!=1",array(":position"=>$row["dept_id"]))
            ->queryAll();
        if($rows){
            foreach ($rows as $staff){
                $html.="<tr>";
                $html.="<td>".$staff["code"]."</td>";
                $html.="<td>".$staff["name"]."</td>";
                $html.="<td>".$row["dept_name"]."</td>";
                $html.="<td>".$staff["entry_time"]."</td>";
                $html.="</tr>";
            }
        }else{
            $html.="<tr><td colspan='4'>无</td></tr>";
        }
        $html.="</tbody></table>";
        return array('html'=>$html,'title'=>$title);
    }
}
