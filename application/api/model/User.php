<?php


namespace app\api\model;


class User extends BaseModel
{

    public function address(){
        //外键在user_address里，所以用hasOne,反之外键在自己模型里用belongsTo
        return $this->hasOne('UserAddress','user_id','id');
    }

    public static function getByOpenID($openid){
        $user = self::where('openid','=',$openid)->find();
        return $user;
    }


}