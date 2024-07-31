<?php

class JsonController extends Controller
{
    /**
     * type 		0部门1职位
     * city 		城市
     * dept_id 		部门id
     * dept_class 	职位类别
     */
    public function actionIndex()
    {
        if($_GET['user']!='admin'){
            exit;
        }
        if($_GET['ac']=='1')
        {
            // 部门
            $items = Yii::app()->db->createCommand('SELECT id,name FROM hr_dept WHERE type = 0 ORDER BY id ASC ')->queryAll();

            // 职位
            $list = Yii::app()->db->createCommand('SELECT id,name,dept_id FROM hr_dept WHERE type = 1 ORDER BY id ASC ')->queryAll();

            $data['items'] = $items;
            $data['list'] = $list;

            echo json_encode($data);
            exit;
        }
        if($_GET['ac']=='2')
        {
            // entry_time入 職時間
            // staff_status 員工狀態：0（已經入職）
            // position	职位

            //$data['list'] = Yii::app()->db->createCommand('SELECT id, name, code, sex, city, phone, entry_time, staff_status, position, office_id FROM hr_employee WHERE 1=1 ORDER BY id ASC ')->queryAll();
            $from_employee =  'hr'.Yii::app()->params['envSuffix'].'.hr_employee';
            $data['list'] = Yii::app()->db->createCommand()->select("id, name, code, sex, city, phone, entry_time, staff_status, position, office_id ")->from($from_employee)->where('1=1')->queryAll();

            $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
            $data['city'] = Yii::app()->db->createCommand()->select("code, name, region")->from($from)->where('1=1')->queryAll();


            $fromx =  'hr'.Yii::app()->params['envSuffix'].'.hr_office';
            $data['office'] = Yii::app()->db->createCommand()->select("id, name, z_display, city")->from($fromx)->where('1=1')->queryAll();

            echo json_encode($data);
            exit;
        }
        if($_GET['ac']=='3'){
            exit;
            echo "<pre>";
            $from =  'security'.Yii::app()->params['envSuffix'].'.sec_user_access';
            $rows = Yii::app()->db->createCommand()->select("*")->from($from)->where(array('like', 'username', "Grace"))->queryAll();
            print_r($rows);

            $fromuser =  'security'.Yii::app()->params['envSuffix'].'.sec_user';
            $row = Yii::app()->db->createCommand()->select("*")->from($fromuser)->where(array('like', 'username', "Grace"))->queryAll();
            print_r($row);
        }
        if($_GET['ac']=='company'){

            $key = $_GET['key'];

            $fromuser =  'hrdev'.Yii::app()->params['envSuffix'].'.hr_company';

            $row = Yii::app()->db->createCommand("select * from hruat.hr_company where name=$key")->queryRow();

            $id = $row['id'];

            $sql = "SELECT a.* FROM docmanuat.dm_file a LEFT JOIN docmanuat.dm_master b ON a.mast_id = b.id WHERE b.doc_id='".$id."' and b.doc_type_code='COMPANY2'";
            //echo $sql;exit;

            $rowsx = Yii::app()->db->createCommand($sql)->queryAll();

            // phy_path_name	目录 /docman/uat/75/5a
            // phy_file_name	图片 262dad057c20357c340fb41579c6a744.jpg

            foreach($rowsx as $key=>$val){
                $image_path = "/data/part1".$val['phy_path_name']."/".$val['phy_file_name'];
                //$image_path = "/data/part1/docman/uat/75/5a/262dad057c20357c340fb41579c6a744.jpg";

                $image_data = file_get_contents($image_path);

                $image_base64 = base64_encode($image_data);

                $img_url = 'data:image/jpeg;base64,'.$image_base64;

                $rowsx[$key]['imgx']= $image_path;
                $rowsx[$key]['img'] = $img_url;
            }
            echo json_encode($rowsx);

            /*$image_path = "/data/part1/docman/uat/75/5a/262dad057c20357c340fb41579c6a744.jpg";

            //$image_path = 'D:3.jpg';

            $image_data = file_get_contents($image_path);

            $image_base64 = base64_encode($image_data);

            $img_url = 'data:image/jpeg;base64,'.$image_base64;


            echo '<img src="'.$img_url.'"/>';
            exit;
            //print_r($rowsx);exit;
            echo json_encode($rowsx);*/

        }
    }

}
