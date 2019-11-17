<?php


namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden = ['product_id','delete_time','id','update_time'];

//    public static function createProductProperty($data,$id){
//        foreach ($data as $name=>$detail){
//            self::create([
//                'name' => $name,
//                'detail' => $detail,
//                'product_id' => $id,
//            ]);
//        }
//    }
}
