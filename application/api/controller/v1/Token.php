<?php


namespace app\api\controller\v1;

use app\api\service\AppToken;
use app\api\service\Token as TokenService;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameterException;

class Token
{
    /**
     * 获取令牌
     * @param string $code
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\WeChatException
     * @throws \think\Exception
     */
    public function getToken($code=''){
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);
        $token = $ut->get();
        return [
            'token' =>$token
        ];
    }

    /**
     * 验证令牌是否有效
     * @param string $token
     * @return array
     * @throws ParameterException
     */
    public function verifyToken($token=''){
        if(!$token){
            throw new ParameterException([
                'msg' => 'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return [
            'isValid' => $valid
        ];
    }


    /**
     * 第三方应用获取令牌
     * @url /app_token?
     * @POST ac=:ac se=:secret
     */
//    public function getAppToken($ac='',$se=''){
//        (new AppTokenGet())->goCheck();
//        $app = new AppToken();
//        $token = $app->get($ac,$se);
//        return [
//            'token' => $token
//        ];
//    }

}
