<?php


namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = true;
    /**
     * 自定义获取器，将图片url补全
     * @param $value
     * @param $data
     * @return string
     */
    protected function prefixImgUrl($value,$data){
        $finalUrl = $value;
        if($data['from'] == 1){
            $finalUrl = config('setting.img_prefix').$value;
        }
        return $finalUrl;
    }
}
