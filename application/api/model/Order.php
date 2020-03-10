<?php


namespace app\api\model;


use think\model\concern\SoftDelete;

class Order extends BaseModel
{
    use SoftDelete;
    protected $hidden = ['delete_time','user_id','update_time'];

    public function deliverRecord(){
        return $this->hasOne('DeliverRecord','order_no','order_no');
    }

    public function getSnapItemsAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public static function getSummaryByUser($uid,$page=1,$size=10){
        $pagingData = self::where('user_id','=',$uid)
            ->order('create_time','desc')
            ->paginate($size,true,['page' => $page]);
        return $pagingData;
    }
}
