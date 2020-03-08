<?php


namespace app\api\controller\v1;


use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\UserInfo;
use app\lib\exception\SuccessMessage;

class User
{

    public function updateInfo()
    {
        $validate = new UserInfo();
        $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        $dataArray = $validate->getDataByRule(input('post.'));
        $user->save($dataArray);
        return Json(new SuccessMessage(),201);
    }

}
