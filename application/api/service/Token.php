<?php


namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Cache;
use think\facade\Request;

class Token
{
    /**
     * 生成随机字符串作为令牌
     * @return string
     */
    public static function generateToken(){
        //32个字符组成一组随机字符串
        $randChars = getRandChars(32);
        //用三组字符串进行md5加密，提上安全性
        $timestamp = $_SERVER['REQUEST_TIME'];
        //salt 盐
        $salt = config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }

    /**
     * 根据Token返回用户的相关信息
     * @param $key
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVar($key){
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if(!$vars){
            throw new TokenException();
        }
        else{
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }
            else{
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }
    }

    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    /**
     * 用户和管理员都可以访问的权限
     */
    public static function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= ScopeEnum::User){
                return true;
            }
            else{
                throw new ForbiddenException();
            }
        }
        else{
            throw new TokenException();
        }
    }

    /**
     * 只有用户才可以访问的接口权限
     */
    public static function needExclusiveScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }
            else{
                throw new ForbiddenException();
            }
        }
        else{
            throw new TokenException();
        }
    }
    /*
     * 只有管理员才可以访问的接口权限
     */
    public static function needAdministratorsScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::Super){
                return true;
            }
            else{
                throw new ForbiddenException();
            }
        }
        else{
            throw new TokenException();
        }
    }

    /**
     * 检测uid是否和想要操作的数据的uid一致
     * @param $checkedUID
     * @return bool
     * @throws Exception
     */
    public static function isValidOperate($checkedUID){
        if(!$checkedUID){
            throw new Exception('检测UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkedUID){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 验证Token是否有效
     * @param $token
     * @return bool
     */
    public static function verifyToken($token){
        $exist = Cache::get($token);
        if($exist){
            return true;
        }
        else{
            return false;
        }
    }

}
