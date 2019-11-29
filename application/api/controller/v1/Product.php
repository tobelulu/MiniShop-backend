<?php


namespace app\api\controller\v1;


use app\api\model\Product as ProductModel;
use app\api\validate\PagingParameter;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;
use think\Collection;

class Product
{

    /**
     * 获取最近添加的商品(可分页加载全部的商品)
     */
    public function getRecent($page=1,$size=16){
        (new PagingParameter())->goCheck();
        $products = ProductModel::getMostRecent($page,$size);
        if($products->isEmpty()){
            return [
                'data' => [],
                'current_page' => $page,
            ];
        }
        $products->hidden(['summary','delete_time']);
        return $products;
    }

    /**
     * 获取分类下的所有商品
     * @param $id
     * @return array|string|Collection
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     */
    public function getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products->hidden(['summary','delete_time']);
        return $products;
    }

    /*
     * 获取商品详细信息
     */
    public function getOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        $product->hidden(['delete_time']);
        return $product;
    }
}
