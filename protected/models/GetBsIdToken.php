<?php
//获取北森单点登录的token
class GetBsIdToken{
    protected $public_Key="";//公钥(Public_Key)

    protected $private_Key="";//私钥(Private_Key)

    public function getIDToken(){
        $suffix = Yii::app()->params['envSuffix'];
        $uid = Yii::app()->user->id;
        $row = Yii::app()->db->createCommand()->select("bs_staff_id,email")
            ->from("hr{$suffix}.hr_binding a")
            ->leftJoin("hr{$suffix}.employee b","a.employee_id=b.id")
            ->where("a.user_id=:user_id", array(':user_id'=>$uid))
            ->queryRow();
        if($row){
            $bsUserId = $row["bs_staff_id"];
            $bsUserEmail = $row["email"];
            if(!empty($bsUserId)){

            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), "该员工未同步北森资料，无法登录");
                return false;
            }
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "该员工未绑定账号，无法登录");
            return false;
        }
    }
}
