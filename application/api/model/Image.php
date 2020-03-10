<?php


namespace app\api\model;


use think\model\concern\SoftDelete;

class Image extends BaseModel
{
    use SoftDelete;
    protected $hidden = ['delete_time','id','update_time','from'];

    /**
     * 自动调用的读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }

}
