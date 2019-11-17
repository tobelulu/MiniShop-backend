<?php


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Product as ProductModel;
use app\api\model\Image as ImageModel;
use app\api\model\ProductImage as ProductImageModel;
use app\api\model\ProductProperty as ProductPropertyModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PagingParameter;
use app\api\validate\UploadImg;
use app\lib\exception\ImageException;
use app\lib\exception\ProductException;
use app\lib\exception\SuccessMessage;
use think\Collection;

class Product extends BaseController
{
//    protected $beforeActionList = [
//        'checkAdministratorsScope' => ['only' => 'deleteOne,upload'],
//    ];//权限控制

    /**
     * 获取最近添加的商品(可分页加载全部的商品)
     * @param int $count
     * @return array
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     */
    public function getRecent($page=1,$size=16){
        (new Count())->goCheck();
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

    /**
     *软删除商品
     * @param $id
     * @return bool
     * @throws \app\lib\exception\ParameterException
     */
//    public function deleteOne($id){
//        (new IDMustBePositiveInt())->goCheck();
//        ProductModel::destroy($id);
//        return Json(new SuccessMessage(),201);
//    }

    /**
     * 获取全部商品(分页)(未被软删除的)
     * @param int $page
     * @param int $size
     * @return \think\Paginator
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     */
//    public function getAll($page=1,$size=15){
//        (new PagingParameter())->goCheck();
//        $pagingProducts = ProductModel::getAll($page,$size);
//        if(!$pagingProducts){
//            throw new ProductException();
//        }
//        return $pagingProducts;
//    }

    /**
     * 上传商品
     * @return bool
     * @throws ImageException
     * @throws \app\lib\exception\ParameterException
     */
//    public function upload(){
//        $validate = new UploadImg();
//        $validate->goCheck();
//        $dataArray = $validate->getDataByRule(input('post.'));
//        $main_img = request()->file('main_img');
//        $fileInfo = $main_img->validate(['ext'=>'jpg,jpeg,png,gif'])->move( '../public/images');//保存图片
//        if($fileInfo){
//            $image = ImageModel::createImage(str_replace('\\','/',$fileInfo->getSaveName()));//保存Image表
//            $product = ProductModel::createProduct($dataArray,str_replace('\\','/',$fileInfo->getSaveName()),$image->id);//保存Product表
//            ProductPropertyModel::createProductProperty(json_decode($dataArray['property'],true),$product->id);//保存product_property表
//            $product_image =[];
//            for($i=0;$i<$dataArray['imgLength'];$i++){
//                $product_image[$i] = request()->file('product_image'.$i);
//                $fileInfo = $product_image[$i]->validate(['ext'=>'jpg,jpeg,png,gif'])->move( '../public/images');//保存图片
//                if($fileInfo){
//                    $image = ImageModel::createImage(str_replace('\\','/',$fileInfo->getSaveName()));//保存Image表
//
//                    ProductImageModel::createProductImage($image->id,$i+1,$product->id);//保存product_image表
//                }
//                else{
//                    throw new ImageException();
//                }
//            }
//            return Json(new SuccessMessage(),201);
//        }
//        else{
//            throw new ImageException();
//        }
//    }
}
