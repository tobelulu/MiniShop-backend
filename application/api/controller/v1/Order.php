<?php


namespace app\api\controller\v1;

use app\api\service\Token as TokenService;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;
use app\api\validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\api\model\Postage as PostageModel;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;

class Order extends BaseController
{
    //用户在选择商品后，向API提交包含它所选商品的相关信息
    //API在接受到消息后，需要检测相关商品的库存量
    //有库存则把订单数据存入数据库中=下单成功，返回客户端消息，告诉客户端可以支付
    //客户端调用支付接口进行支付
    //支付时还需要再次检测库存量
    //服务器调用微信的支付接口进行下预订单
    //小程序根据服务器返回的结果拉起微信支付
    //微信会返回支付结果
    //成功：进行库存量检测
    //  成功：进行库存量扣除

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getSummaryByUser,getDetail'],
        'checkAdministratorsScope' => ['only' => 'getSummary,delivery'],
    ];//权限控制

    //创建订单
    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        $uid = TokenService::getCurrentUid();
        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }

    /**
     * 获取某用户的订单列表
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByUser($page=1,$size=10){
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid,$page,$size);
        if($pagingOrders->isEmpty()){
            return [
                'data' => [],
                'current_page' => $page,
            ];
        }
        $data = $pagingOrders->hidden(['snap_items','snap_address','prepay_id'])->toArray();
        return $data;
    }

    /**
     * 获取订单的详细数据
     * @param $id
     * @return mixed
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     */
    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    /**
     * 获取全部订单(分页)
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page=1, $size = 20){
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->toArray();
        return $data;
    }

    /**
     * 发送模板消息
     * @param $id
     * @return SuccessMessage
     * @throws \app\lib\exception\ParameterException
     */
    public function delivery($id,$expressNumber){
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id,$expressNumber);
        if($success){
            return new SuccessMessage();
        }
    }

    //获取邮费
    public function getPostage(){
        $postage = PostageModel::find(1);
        return $postage;
    }

}