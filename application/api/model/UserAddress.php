<?php


namespace app\api\model;


use think\model\concern\SoftDelete;

class UserAddress extends BaseModel
{
    use SoftDelete;
    protected $hidden = ['delete_time','id','user_id'];
}
