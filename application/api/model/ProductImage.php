<?php


namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['delete_time','img_id','product_id'];
    protected $deleteTime = false;
    public function imgUrl(){
        return $this->belongsTo('Image','img_id','id');
    }
}
