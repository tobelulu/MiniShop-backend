<?php


namespace app\api\model;


use think\model\concern\SoftDelete;

class ProductProperty extends BaseModel
{
    use SoftDelete;
    protected $hidden = ['product_id','delete_time','id','update_time'];

}
