<?php


namespace app\api\model;

use think\model\concern\SoftDelete;

class Sku extends BaseModel
{
    use SoftDelete;
    protected $hidden = ['update_time','create_time','delete_time','product_id','img_id'];

    public function imgUrl(){
        return $this->belongsTo('Image','img_id','id');
    }
}
