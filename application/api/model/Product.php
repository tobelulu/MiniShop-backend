<?php


namespace app\api\model;


use think\model\concern\SoftDelete;

class Product extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';//软删除字段
    protected $hidden = ['update_time','img_id','from','category_id','create_time'];
    /**
     * 自动调用的读取器
     */
    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }

    public static function getMostRecent($page=1,$size=16){
        $products = self::where('status',1)->order('create_time','desc')
            ->paginate($size,true,['page' => $page]);
        return $products;
    }

    public static function getProductsByCategoryID($categoryID){
        $products = self::where('category_id','=',$categoryID)->select();
        return $products;
    }

    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }

    public function properties(){
        return $this->hasMany('ProductProperty','product_id','id');
    }

    /**
     * 获取商品详情
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductDetail($id){
        $products = self::with([
            'imgs' => function($query){
                $query->with(['imgUrl'])->order('order','asc');
            },
            'properties'
        ])->find($id);
        return $products;
    }
}
