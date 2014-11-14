<?php
namespace application\services;

use application\components\TopLinkerTool;
use Yii;

class CommonService
{
    protected $topLinkerTool = null;

    function __construct()
    {
        $this->topLinkerTool = new TopLinkerTool($this->getLoginAppKey(), $this->getLoginAppSecret());
    }

    public function getLoginAppKey()
    {
        $user = Yii::app()->session->get('ApiLoginUser');
        return $user->app_key;
    }

    public function getLoginAppSecret()
    {
        $user = Yii::app()->session->get('ApiLoginUser');
        return $user->app_secret;
    }

    public static function getDBAccessToken($nick, $proxy_nick = null)
    {
        $sql = "SELECT access_token FROM auth_sessions WHERE taobao_user_nick='{$nick}' ORDER BY create_time DESC LIMIT 1";
        $command = Yii::app()->db->createCommand($sql);
        $data = $command->queryRow();
        return $data['access_token'];
    }

    public function getLoginAccessToken()
    {
        $user = Yii::app()->session->get('ApiLoginUser');
        return $user->access_token;
    }

    public function getLoginUser()
    {
        $user = Yii::app()->session->get('ApiLoginUser');
        return $user;
    }
}