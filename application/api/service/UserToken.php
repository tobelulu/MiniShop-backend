<?php


namespace app\api\service;


use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code){
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config("wx.app_secret");
        $this->wxLoginUrl = sprintf(config('wx.login_url'),
            $this->wxAppID,$this->wxAppSecret,$this->code);
    }

    /**
     * 调用微信接口，获取openID
     * @throws Exception
     * @throws WeChatException
     */
    public function get(){
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result,true);
        if(empty($wxResult)){
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        }
        else{
            $loginFail = array_key_exists('errcode',$wxResult);
            if($loginFail){
                $this->processLoginError($wxResult);
            }
            else{
                return $this->grantToken($wxResult);
            }
        }
    }

    /**
     * 授予令牌
     * @param $wxResult
     * @return mixed
     * @throws TokenException
     */
    private function grantToken($wxResult){
        //拿到openid
        //查看数据库，此id是否已存在
        //如果存在则不处理，不存在则新增一条user记录
        //生成令牌，写入缓存(写入缓存的数据是键值对 key:令牌 value:wxResult,uid,scope)
        //返回令牌
        //令牌其实是一个随机字符串
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        if($user){
            $uid = $user->id;
        }
        else{
            $uid = $this->newUser($openid);
        }
        $cachedValue = $this->prepareCacheValue($wxResult,$uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    /**
     * 将Token和相关数据存到缓存
     * @param $cachedValue
     * @return mixed
     * @throws TokenException
     */
    private function saveToCache($cachedValue){
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        //cache助手函数默认使用文件方式，可以通过配置支持redis等缓存数据库
        $request = cache($key,$value,$expire_in);
        if(!$request){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    /**
     * 准备缓存数据
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function prepareCacheValue($wxResult,$uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;//User即app用户的权限
        return $cachedValue;
    }

    /**
     * 将user表新增一条记录
     * @param $opedid
     * @return mixed
     */
    private function newUser($opedid){
        $user = UserModel::create([
            'openid' => $opedid
        ]);
        return $user->id;
    }

    /**
     * 抛出调用微信接口失败异常
     * @param $wxResult
     * @throws WeChatException
     */
    private function processLoginError($wxResult){
        throw new WeChatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }
}