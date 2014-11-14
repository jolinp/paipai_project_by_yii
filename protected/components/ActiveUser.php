<?php
namespace application\components;

use Yii;
use CComponent;

/**
 * 此类对象为：当前用户
 * 单例模式
 * Class ActiveUser
 * @package application\components
 */
class ActiveUser extends CComponent
{
    private static $_instance;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function __construct()
    {
    }

    public function setUin($number)
    {
        Yii::app()->session->add('ActiveUser', $number);
    }

    public function getUin()
    {
        return Yii::app()->session->get('ActiveUser');
    }

    public function setAccessToken($string)
    {
        Yii::app()->session->add('AccessToken', $string);
    }

    public function getAccessToken()
    {
        return Yii::app()->session->get('AccessToken');
    }

    public function clearIdentity()
    {
        Yii::app()->session->remove('ActiveUser');
        Yii::app()->session->remove('AccessToken');
    }

    public function isGuest()
    {
        return $this->uin === null;
    }

    public function setActivityId($activityId)
    {
        Yii::app()->session->remove('activityId');
        Yii::app()->session->add('activityId', $activityId);
    }

    public function getActivityId()
    {
        return Yii::app()->session->get('activityId');
    }
}