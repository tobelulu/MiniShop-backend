<?php


namespace app\api\controller\v1;

use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BannerMissException;

class Banner
{
    /**
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     */
    public function getBanner($id){
        (new IDMustBePositiveInt())->goCheck();//验证数据合法性
        $banner = BannerModel::getBannerByID($id);//查找
        if($banner->isEmpty()){
            throw new BannerMissException();
        }
        return $banner;
    }
}