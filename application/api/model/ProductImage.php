<?php


namespace app\api\model;


use think\model\concern\SoftDelete;

class ProductImage extends BaseModel
{
    use SoftDelete;
    protected $hidden = ['delete_time','img_id','product_id'];
    public function imgUrl(){
        return $this->belongsTo('Image','img_id','id');
    }
}
