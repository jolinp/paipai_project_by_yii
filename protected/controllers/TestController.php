<?php
namespace application\controllers;

use application\components\ActiveUser;
use application\components\Controller;
use application\behaviors\InvokeApiBehavior;
use application\events\ApiEvent;

class TestController extends Controller
{
    public function actionTest()
    {
        $user = ActiveUser::getInstance();

        $behavior = new InvokeApiBehavior();
        $user->attachBehavior('invokeApi',$behavior);

        /*$ApiClassName = 'GetItem';
        $params = array(
            'ItemCode'=>'D17C250E000000000401000031919CA7',
            'PureData'=>1
        );*/

        /*$ApiClassName = 'SellerSearchItemList';
        $params = array(
            'sellerUin'=>$user->getUin(),
            'itemState'=>1,
            'pageIndex'=>1,
            'pageSize'=>30,
        );*/

        $ApiClassName = 'GetLtdActive';
        $params = array(
            'PureData'=>1
        );

        $user->setAfterInvoke('saveToDb');//下载后处理方式
        $user->invokeApi($ApiClassName, $params);

        $result = $user->getApiResult();
        print_r($result);
    }
}
