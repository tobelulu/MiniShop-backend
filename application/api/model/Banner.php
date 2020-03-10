<?php


namespace app\api\model;


use think\model\concern\SoftDelete;

class Banner extends BaseModel
{
    use SoftDelete;
    //隐藏部分无用结果
    protected $hidden = ['delete_time','update_time'];
    //定义模型之间的关系，with函数会调用
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBannerByID($id){
        $banner = self::with(['items','items.img'])->get($id);//items.img会调用BannerItem里的img()
        return $banner;
    }
}
