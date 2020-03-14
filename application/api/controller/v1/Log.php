<?php


namespace app\api\controller\v1;


use app\api\service\Token as TokenService;
use app\api\model\Log as LogModel;
use app\api\validate\LogNew;
use app\lib\exception\SuccessMessage;

class Log
{
    public function createLog() {
        $validate = new LogNew();
        $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $dataArray = $validate->getDataByRule(input('post.'));
        $dataArray['user_id'] = $uid;
        LogModel::create($dataArray);
        return Json(new SuccessMessage(),201);
    }
}
