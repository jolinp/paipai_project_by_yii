<?php
namespace application\controllers;

use application\components\Controller;
use application\services\command\InitCommandService;
use application\services\query\RptQueryService;
use Yii;

class SiteController extends Controller
{
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    public function actionCustRpt($beginDate, $endDate)
    {
        $nick = $this->getLoginNick();
        $rptQueryService = new RptQueryService();
        $datas = $rptQueryService->CustRpt($nick, $beginDate, $endDate);
        $this->renderJson(array('result' => 'success', 'data' => $datas));
    }

    public function actionInitData()
    {
        $nick = $this->getLoginNick();
        $days = 15;
        $tool = new InitCommandService();
        $apiInfo = $tool->InitAll($nick, $days);
        if ($apiInfo['flag'] == true)
            $this->renderJson(array('result' => 'success'));
        else
            $this->renderJson(array('result' => 'failure', 'message' => $apiInfo['msg']));
    }

    public function actionGetNick()
    {
        $service = new InitCommandService();
        $apiRetInfo = $service->getCredit($this->getLoginNick());
        $this->renderJson(array(
            'result' => 'success',
            'nick' => $this->getLoginNick(),
            'credit' => $apiRetInfo
        ));
    }

    public function actionGetBalance()
    {
        $nick = $this->getLoginNick();
        $user = $this->getApiLoginUser();
        $appKey = $user->app_key;
        $access_token = $user->access_token;
        $appSecret = $user->app_secret;
        $tool = new TopLinkerTool($appKey, $appSecret);
        $balance = $tool->getBalance($nick, $appKey, $appSecret, $access_token);

        $this->renderJson(array('result' => 'success', 'balance' => $balance));

    }

    //剩余天数

    public function actionRemainDays()
    {
        $nick = $this->getLoginNick();
        //@todo 正式上线时补上此接口逻辑
        $this->renderJson(array('result' => 'success', 'days' => 0));
    }
}