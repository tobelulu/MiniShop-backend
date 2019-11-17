<?php


namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\api\model\Image as ImageModel;
use app\api\validate\AddCategory;
use app\lib\exception\CategoryException;
use app\lib\exception\ImageException;
use app\lib\exception\SuccessMessage;

class Category
{
//    protected $beforeActionList = [
//        'checkAdministratorsScope' => ['only' => 'add'],
//    ];//权限控制

    public function getAllCategories(){
        $categories = CategoryModel::with('img')->select();
        if($categories->isEmpty()){
            throw new CategoryException();
        }
        $categories->hidden(['']);
        return $categories;
    }

    /**
     * 添加分类
     * @return bool
     * @throws ImageException
     * @throws \app\lib\exception\ParameterException
     */
//    public function add(){
//        $validate = new AddCategory();
//        $validate->goCheck();
//        $name = $validate->getDataByRule(input('post.'));
//        $img = request()->file('img');
//        $fileInfo = $img->validate(['ext'=>'jpg,jpeg,png,gif'])->move( '../public/images');//保存图片
//        if($fileInfo){
//            $image = ImageModel::createImage(str_replace('\\','/',$fileInfo->getSaveName()));//保存Image表
//            CategoryModel::createCategory($name['name'],$image->id);
//            return Json(new SuccessMessage(),201);
//        }
//        else{
//            throw new ImageException();
//        }
//    }

}
