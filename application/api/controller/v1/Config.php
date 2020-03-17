<?php


namespace app\api\controller\v1;

use app\api\model\Config as ConfigModel;

class Config
{
    public function getShopStatus(){
        return ConfigModel::get(4)->hidden(['name']);
    }
}
