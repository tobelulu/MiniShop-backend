<?php


namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['delete_time','img_id','product_id'];
    protected $deleteTime = false;
    public function imgUrl(){
        return $this->belongsTo('Image','img_id','id');
    }

    public static function createProductImage($img_id,$order,$product_id){
        self::create([
            'img_id' => $img_id,
            'order' => $order,
            'product_id' => $product_id,
        ]);
    }
}