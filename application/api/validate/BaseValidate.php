<?php


namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate
{
    protected $failException = true;
    public function goCheck(){
        //获取http传入的参数
        //对参数做校验
        $params = Request::param();

        $result = $this->batch()->check($params);
        if(!$result){
            $e = new ParameterException([
                'msg' => is_array($this->error) ? implode(
                    ';', $this->error) : $this->error,
            ]);
            throw $e;
        }
        else{
            return true;
        }
    }

    /**
     * 判断是否是正整数
     * @return bool
     */
    protected function isPositiveInteger($value,$rule='',$data='',$field=''){
        if(is_numeric($value)&&is_int($value+0)&&($value+0)>0){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 判断非空
     * @return bool
     */
    protected function isNotEmpty($value,$rule='',$data='',$field=''){
        if(empty($value)){
            return false;
        }
        else{
            return true;
        }
    }

    //不做验证
//    protected function noValidate($value,$rule='',$data='',$field=''){
//        return true;
//    }

    /**
     * 判断是否是手机号
     * @param $value
     * @return bool
     */
//    public function isMobile($value){
//        $rule = '^1(3|4|5|7|8|9)[0-9]\d{8}$^';
//        $result = preg_match($rule,$value);
//        if($result){
//            return true;
//        }
//        else{
//            return false;
//        }
//    }

    /**
     * 根据rule取数据,rule里没有的数据一律不取
     * @param $arrays
     * @return array
     * @throws ParameterException
     */
    public function getDataByRule($arrays){
        // array_key_exists结果是true|false,可以用位运算符|
        if(array_key_exists('user_id',$arrays)|array_key_exists('uid',$arrays)) {
            //不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或者uid'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value){
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;

    }


}
