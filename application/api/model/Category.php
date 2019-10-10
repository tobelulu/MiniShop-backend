<?php


namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['delete_time','update_time','topic_img_id'];

    public function img(){
        return $this->belongsTo('Image','topic_img_id','id');
    }

    public static function createCategory($name,$img){
        $category = self::create([
            'name' => $name,
            'topic_img_id' => $img
        ]);
        return $category;
    }
}